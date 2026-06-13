<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class GravityFormsService
{
    protected $apiUrl;
    protected $consumerKey;
    protected $consumerSecret;
    protected $formId;

    public function __construct()
    {
        $this->apiUrl = config('services.gravity_forms.api_url');
        $this->consumerKey = config('services.gravity_forms.consumer_key');
        $this->consumerSecret = config('services.gravity_forms.consumer_secret');
        $this->formId = config('services.gravity_forms.form_id');
    }

    /**
     * Check if credentials are configured
     */
    public function isConfigured(): bool
    {
        return !empty($this->consumerKey) && !empty($this->consumerSecret) && !empty($this->apiUrl);
    }

    /**
     * Fetch all Gravity Forms
     *
     * @return array
     */
    public function getForms()
    {
        try {
            $response = Http::withBasicAuth($this->consumerKey, $this->consumerSecret)
                ->timeout(30)
                ->get("{$this->apiUrl}/forms");

            if ($response->successful()) {
                return $response->json();
            }

            Log::warning('GF API: Failed to fetch forms', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return [];
        } catch (\Exception $e) {
            Log::error('GF API: Exception fetching forms', [
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }

    /**
     * Fetch entries for a specific form with pagination
     *
     * @param int $formId
     * @param array $params
     * @return array
     */
    public function getEntries($formId = null, array $params = [])
    {
        $formId = $formId ?? $this->formId;

        try {
            $defaultParams = [
                'paging' => [
                    'page_size' => 100,
                    'current_page' => 1
                ]
            ];

            $params = array_merge($defaultParams, $params);

            $response = Http::withBasicAuth($this->consumerKey, $this->consumerSecret)
                ->timeout(30)
                ->get("{$this->apiUrl}/forms/{$formId}/entries", $params);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'entries' => $data['entries'] ?? [],
                    'total_count' => $data['total_count'] ?? 0,
                    'paging' => $data['paging'] ?? []
                ];
            }

            Log::warning('GF API: Failed to fetch entries', [
                'form_id' => $formId,
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return ['entries' => [], 'total_count' => 0, 'paging' => []];
        } catch (\Exception $e) {
            Log::error('GF API: Exception fetching entries', [
                'form_id' => $formId,
                'error' => $e->getMessage()
            ]);
            return ['entries' => [], 'total_count' => 0, 'paging' => []];
        }
    }

    /**
     * Fetch all entries with automatic pagination
     *
     * @param int $formId
     * @return array
     */
    public function getAllEntries($formId = null)
    {
        $formId = $formId ?? $this->formId;
        $allEntries = [];
        $currentPage = 1;
        $pageSize = 100;

        do {
            $result = $this->getEntries($formId, [
                'paging' => [
                    'page_size' => $pageSize,
                    'current_page' => $currentPage
                ]
            ]);

            $entries = $result['entries'] ?? [];
            $allEntries = array_merge($allEntries, $entries);

            $totalCount = $result['total_count'] ?? 0;
            $currentPage++;

            // Break if we've fetched all entries
            if (count($allEntries) >= $totalCount) {
                break;
            }
        } while (!empty($entries));

        return $allEntries;
    }

    /**
     * Fetch a single entry by ID
     *
     * @param int $entryId
     * @return array|null
     */
    public function getEntry($entryId)
    {
        try {
            $response = Http::withBasicAuth($this->consumerKey, $this->consumerSecret)
                ->timeout(30)
                ->get("{$this->apiUrl}/entries/{$entryId}");

            if ($response->successful()) {
                return $response->json();
            }

            Log::warning('GF API: Failed to fetch entry', [
                'entry_id' => $entryId,
                'status' => $response->status()
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('GF API: Exception fetching entry', [
                'entry_id' => $entryId,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Get form fields structure (cached for 1 hour)
     *
     * @param int $formId
     * @return array
     */
    public function getFormFields($formId = null)
    {
        $formId = $formId ?? $this->formId;
        $cacheKey = "gf_form_fields_{$formId}";

        return Cache::remember($cacheKey, 3600, function () use ($formId) {
            try {
                $response = Http::withBasicAuth($this->consumerKey, $this->consumerSecret)
                    ->timeout(30)
                    ->get("{$this->apiUrl}/forms/{$formId}");

                if ($response->successful()) {
                    $form = $response->json();
                    return $form['fields'] ?? [];
                }

                Log::warning('GF API: Failed to fetch form fields', [
                    'form_id' => $formId,
                    'status' => $response->status()
                ]);

                return [];
            } catch (\Exception $e) {
                Log::error('GF API: Exception fetching form fields', [
                    'form_id' => $formId,
                    'error' => $e->getMessage()
                ]);
                return [];
            }
        });
    }

    /**
     * Build field ID to label mapping
     *
     * @param int $formId
     * @return array
     */
    public function getFieldMapping($formId = null)
    {
        $fields = $this->getFormFields($formId);
        $mapping = [];

        foreach ($fields as $field) {
            $fieldId = $field['id'] ?? null;
            $label = $field['label'] ?? null;

            if ($fieldId && $label) {
                $mapping[$fieldId] = [
                    'label' => $label,
                    'type' => $field['type'] ?? 'text',
                    'admin_label' => $field['adminLabel'] ?? $label
                ];
            }
        }

        return $mapping;
    }

    /**
     * Clear cached form fields
     *
     * @param int $formId
     * @return void
     */
    public function clearCache($formId = null)
    {
        $formId = $formId ?? $this->formId;
        $cacheKey = "gf_form_fields_{$formId}";
        Cache::forget($cacheKey);
    }

    /**
     * Test API connection
     *
     * @return bool
     */
    public function testConnection()
    {
        // Guard against missing credentials
        if (empty($this->consumerKey) || empty($this->consumerSecret) || empty($this->apiUrl)) {
            Log::warning('GF API: Connection test skipped - credentials not configured');
            return false;
        }

        try {
            $response = Http::withBasicAuth($this->consumerKey, $this->consumerSecret)
                ->timeout(10)
                ->get("{$this->apiUrl}/forms");

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('GF API: Connection test failed', [
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
}
