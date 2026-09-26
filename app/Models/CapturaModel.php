<?php

namespace App\Models;

use CodeIgniter\Model;

class CapturaModel extends Model
{
    protected $table = 'michis_salvajes';

    public function spawnMichis(int $jugadorId): array
    {
        $query = $this->db->query('CALL sp_spawn_michis(?)', [$jugadorId]);
        $spawns = $query->getResultArray();
        $query->freeResult();
        $this->limpiarResultadosProcedimiento();

        return $spawns;
    }

    public function resolver(int $spawnId, int $jugadorId, int $toques, int $duracionMs, int $exito): array
    {
        $query = $this->db->query(
            'CALL sp_resolver_captura(?, ?, ?, ?, ?)',
            [$spawnId, $jugadorId, $toques, $duracionMs, $exito]
        );
        $resultado = $query->getRowArray() ?? [];
        $query->freeResult();
        $this->limpiarResultadosProcedimiento();

        return $resultado;
    }

    private function limpiarResultadosProcedimiento(): void
    {
        $conexion = $this->db->connID;

        if (! $conexion instanceof \mysqli) {
            return;
        }

        while ($conexion->more_results() && $conexion->next_result()) {
            $resultado = $conexion->store_result();

            if ($resultado instanceof \mysqli_result) {
                $resultado->free();
            }
        }
    }
}
