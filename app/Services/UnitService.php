<?php

namespace App\Services;

use Exception;
use App\DTOs\UnitData;
use App\Models\Unit;
use App\Exceptions\UnitException;
use Illuminate\Support\Facades\DB;

class UnitService
{
    /**
     * Create a new unit record.
     */
    public function createUnit(UnitData $data): Unit
    {
        return DB::transaction(function () use ($data) {
            try {
                return Unit::create([
                    'name' => $data->name,
                    'symbol' => $data->symbol,
                ]);

            } catch (Exception $e) {
                throw UnitException::creationFailed($e->getMessage(), [
                    'data' => (array) $data,
                    'trace' => $e->getTraceAsString()
                ]);
            }
        });
    }

    /**
     * Update an existing unit record.
     */
    public function updateUnit(Unit $unit, UnitData $data): Unit
    {
        return DB::transaction(function () use ($unit, $data) {
            try {
                $unit->update([
                    'name' => $data->name,
                    'symbol' => $data->symbol,
                ]);

                return $unit->refresh();

            } catch (Exception $e) {
                throw UnitException::updateFailed($e->getMessage(), [
                    'id'   => $unit->id,
                    'data' => (array) $data
                ]);
            }
        });
    }

    /**
     * Delete a unit record.
     */
    public function deleteUnit(Unit $unit): void
    {
        DB::transaction(function () use ($unit) {
            try {
                if ($unit->products()->exists()) {
                    throw new Exception('Cannot delete unit because it is associated with products.');
                }

                $unit->delete();

            } catch (Exception $e) {
                throw UnitException::deletionFailed($e->getMessage(), ['id' => $unit->id]);
            }
        });
    }
}
