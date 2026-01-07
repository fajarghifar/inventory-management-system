<?php

namespace App\Services;

use App\Models\Unit;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class UnitService
{
    /**
     * Create a new unit record.
     *
     * @param array $data
     * @return Unit
     * @throws Exception
     */
    public function createUnit(array $data): Unit
    {
        return DB::transaction(function () use ($data) {
            try {
                Log::info('Attempting to create a new unit', ['name' => $data['name']]);

                $unit = Unit::create([
                    'name'   => $data['name'],
                    'symbol' => $data['symbol'],
                ]);

                Log::info('Unit created successfully', ['id' => $unit->id]);

                return $unit;

            } catch (Exception $e) {
                Log::error('Failed to create unit: ' . $e->getMessage(), [
                    'data'  => $data,
                    'trace' => $e->getTraceAsString()
                ]);

                throw new Exception('Failed to create unit. Please try again later.');
            }
        });
    }

    /**
     * Update an existing unit record.
     *
     * @param Unit $unit
     * @param array $data
     * @return Unit
     * @throws Exception
     */
    public function updateUnit(Unit $unit, array $data): Unit
    {
        return DB::transaction(function () use ($unit, $data) {
            try {
                Log::info('Attempting to update unit', ['id' => $unit->id]);

                $unit->update([
                    'name'   => $data['name'] ?? $unit->name,
                    'symbol' => $data['symbol'] ?? $unit->symbol,
                ]);

                Log::info('Unit updated successfully', ['id' => $unit->id]);

                return $unit->refresh();

            } catch (Exception $e) {
                Log::error('Failed to update unit: ' . $e->getMessage(), [
                    'id'   => $unit->id,
                    'data' => $data
                ]);

                throw new Exception('Failed to update unit information.');
            }
        });
    }

    /**
     * Delete a unit record.
     *
     * @param Unit $unit
     * @return void
     * @throws Exception
     */
    public function deleteUnit(Unit $unit): void
    {
        DB::transaction(function () use ($unit) {
            try {
                // Future robustness: Check if unit is used in Products before deleting
                // if ($unit->products()->exists()) { throw ... }

                Log::info('Attempting to delete unit', ['id' => $unit->id]);

                $unit->delete();

                Log::info('Unit deleted successfully', ['id' => $unit->id]);

            } catch (Exception $e) {
                Log::error('Failed to delete unit: ' . $e->getMessage(), ['id' => $unit->id]);
                throw new Exception('Failed to delete unit.');
            }
        });
    }
}
