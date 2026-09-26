<?php

namespace App\Models;

use CodeIgniter\Model;

class ArenaModel extends Model
{
    protected $table = 'jugadores';

    public function crearDuelo(int $jugadorId, int $apuesta): array
    {
        $query = $this->db->query(
            'CALL sp_iniciar_duelo_meme(?, ?)',
            [$jugadorId, $apuesta]
        );
        $resultado = $query->getRowArray() ?? [];
        $query->freeResult();
        $this->limpiarResultadosProcedimiento();

        return $resultado;
    }

    public function lanzarCarta(int $dueloId, int $cartaId, int $turnoEsperado, int $jugadorId): array
    {
        $query = $this->db->query(
            'CALL sp_jugar_carta_turno(?, ?, ?, ?)',
            [$dueloId, $cartaId, $turnoEsperado, $jugadorId]
        );
        $fila = $query->getRowArray() ?? [];
        $query->freeResult();
        $this->limpiarResultadosProcedimiento();

        if (! isset($fila['resultado_json'])) {
            return [];
        }

        $resultado = json_decode($fila['resultado_json'], true);

        return is_array($resultado) ? $resultado : [];
    }

    public function obtenerMazo(int $jugadorId): array
    {
        return $this->db->table('mazos_jugador AS m')
            ->select('c.id, c.nombre, c.ataque_aura, c.defensa_cringe, c.rareza_nivel, c.efecto_especial, c.imagen_url')
            ->join('cartas_meme AS c', 'c.id = m.carta_id')
            ->where('m.jugador_id', $jugadorId)
            ->orderBy('c.rareza_nivel', 'DESC')
            ->get()
            ->getResultArray();
    }

    public function obtenerJugador(int $jugadorId): ?array
    {
        return $this->db->table('jugadores')
            ->where('id', $jugadorId)
            ->get()
            ->getRowArray();
    }

    public function obtenerDuelo(int $dueloId): ?array
    {
        return $this->db->table('duelos_activos AS d')
            ->select(
                'd.*, r.username AS retador_nombre, r.aura_max AS aura_max_retador, '
                . 'r.auracoins, b.username AS bot_nombre, b.aura_max AS aura_max_bot'
            )
            ->join('jugadores AS r', 'r.id = d.retador_id')
            ->join('jugadores AS b', 'b.id = d.oponente_bot_id')
            ->where('d.id', $dueloId)
            ->get()
            ->getRowArray();
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
