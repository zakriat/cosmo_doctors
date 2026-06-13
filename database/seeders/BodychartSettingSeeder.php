<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\BodyChartSetting;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

class BodychartSettingSeeder extends Seeder
{
    public function run()
    {
        Schema::disableForeignKeyConstraints();

        $bodychart_image = [
            [
                'name' => 'The Muscular System',
                'file_url' => '/dummy-images/body_chart/muscular_system.jpeg',
                'uniqueId' => 'm4h0axaalku7exfwhl',
                'default' => 1,
                'image_name' => 'muscular_system.jpeg',
            ],
            [
                'name' => 'Eye Section',
                'file_url' => '/dummy-images/body_chart/eye_section.webp',
                'uniqueId' => 'm4h0d68s8b844dq5cwu',
                'default' => 1,
                'image_name' => 'eye_section.webp',
            ],
            [
                'name' => 'Layers of the Skin',
                'file_url' => '/dummy-images/body_chart/skin_structure.jpg',
                'uniqueId' => 'm4h0obupz7y2xydc9ql',
                'default' => 1,
                'image_name' => 'skin_structure.jpeg',
            ],
        ];

        if (env('IS_DUMMY_DATA')) {
            foreach ($bodychart_image as $data) {
                $posterPath = $data['file_url'] ?? null;

                // Create data excluding file_url
                $gener = BodyChartSetting::create(Arr::except($data, ['file_url']));

                if ($posterPath) {
                    // Store the image locally
                    $posterUrl = $this->uploadToStorage(public_path($posterPath));

                    if ($posterUrl) {
                        // Save the URL in the database
                        $gener->full_url = $posterUrl;
                    }
                }

                $gener->save();
            }

            Schema::enableForeignKeyConstraints();
        }
    }

    /**
     * Upload an image to the storage folder and return the storage URL.
     *
     * @param string $filePath
     * @return string|null
     */
    private function uploadToStorage($filePath)
    {
        try {
            // Check if file exists
            if (!file_exists($filePath)) {
                \Log::error("File not found: $filePath");
                return null;
            }

            // Get file contents
            $contents = file_get_contents($filePath);

            // Create a unique file name
            $fileName = uniqid() . '_' . basename($filePath);

            // Store the file in the 'public' disk (linked to 'storage/app/public')
            Storage::disk('public')->put($fileName, $contents);

            // Return the public URL of the stored file
            return Storage::disk('public')->url($fileName);
        } catch (\Exception $e) {
            \Log::error("Error uploading file to storage: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Extract the file name from a given URL.
     *
     * @param string $url
     * @return string|null
     */
    private function extractFileNameFromUrl($url)
    {
        return basename(parse_url($url, PHP_URL_PATH));
    }

    }

