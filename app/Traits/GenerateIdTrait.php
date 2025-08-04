<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;

trait GenerateIdTrait
{
    /**
     * Genera un ID único con el formato A010725001
     * Donde:
     * - A01 es el código de la tabla
     * - 07 es el mes actual
     * - 25 es el año actual
     * - 001 es un número secuencial que se incrementa
     *
     * @param string $tablePrefix Prefijo de la tabla (A01, A03, etc.)
     * @param string $tableName Nombre de la tabla en la base de datos
     * @return string
     */
    protected function generateId(string $tablePrefix, string $tableName): string
    {
        // Obtener mes y año actuales
        $currentMonth = date('m');
        $currentYear = date('y'); // Últimos dos dígitos del año


        $idPrefix = $tablePrefix . $currentMonth . $currentYear;


        $lastId = DB::table($tableName)
            ->where('IdKode', 'like', $idPrefix . '%')
            ->orderBy('IdKode', 'desc')
            ->value('IdKode');


        if (!$lastId) {
            return $idPrefix . '001';
        }


        $lastSequentialNumber = (int) substr($lastId, -3);
        $newSequentialNumber = $lastSequentialNumber + 1;


        $formattedSequentialNumber = str_pad($newSequentialNumber, 3, '0', STR_PAD_LEFT);

        return $idPrefix . $formattedSequentialNumber;
    }
}