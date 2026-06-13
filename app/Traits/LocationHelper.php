<?php

namespace App\Traits;

use Modules\World\Models\State;
use Modules\World\Models\City;

trait LocationHelper
{
    /**
     * Get or create state with duplicate prevention
     */
    public function getOrCreateState($stateName, $countryId = 229)
    {
        // Handle numeric ID (existing selection)
        if (is_numeric($stateName)) {
            return $stateName;
        }

        // Handle text input (custom entry)
        $normalizedName = ucwords(strtolower(trim($stateName)));
        
        // Search case-insensitively for existing record
        $existingState = State::whereRaw('LOWER(TRIM(name)) = ?', [strtolower($normalizedName)])
            ->where('country_id', $countryId)
            ->first();
        
        if ($existingState) {
            return $existingState->id;
        }
        
        // Create new record if not found
        try {
            $newState = State::create([
                'name' => $normalizedName,
                'country_id' => $countryId,
                'status' => 1
            ]);
            return $newState->id;
        } catch (\Exception $e) {
            // Handle race condition - another process might have created it
            $existingState = State::whereRaw('LOWER(TRIM(name)) = ?', [strtolower($normalizedName)])
                ->where('country_id', $countryId)
                ->first();
            
            if ($existingState) {
                return $existingState->id;
            }
            
            // If still fails, throw original exception
            throw $e;
        }
    }

    /**
     * Get or create city with duplicate prevention
     * Note: Cities table only has state_id, not country_id
     */
    public function getOrCreateCity($cityName, $stateId = null)
    {
        // Handle numeric ID (existing selection)
        if (is_numeric($cityName)) {
            return $cityName;
        }

        // Handle text input (custom entry)
        $normalizedName = ucwords(strtolower(trim($cityName)));
        
        // Search case-insensitively for existing record
        // Note: We search without state_id filter to allow independent city selection
        $existingCity = City::whereRaw('LOWER(TRIM(name)) = ?', [strtolower($normalizedName)])
            ->first();
        
        if ($existingCity) {
            return $existingCity->id;
        }
        
        // Create new record if not found
        try {
            $newCity = City::create([
                'name' => $normalizedName,
                'state_id' => $stateId, // Can be null for independent cities
                'status' => 1
            ]);
            return $newCity->id;
        } catch (\Exception $e) {
            // Handle race condition - another process might have created it
            $existingCity = City::whereRaw('LOWER(TRIM(name)) = ?', [strtolower($normalizedName)])
                ->first();
            
            if ($existingCity) {
                return $existingCity->id;
            }
            
            // If still fails, throw original exception
            throw $e;
        }
    }
}