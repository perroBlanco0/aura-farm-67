<?php

namespace App\Models;

use CodeIgniter\Model;

class UsuariosModel extends Model
{
    protected $table = 'jugadores';

    public function buscarParaLogin(string $username): ?array
    {
        return $this->db->table('jugadores')
            ->select('id, username, password_hash, es_admin')
            ->where('username', $username)
            ->where('eliminado_en IS NULL', null, false)
            ->where('password_hash IS NOT NULL', null, false)
            ->get()
            ->getRowArray();
    }

    public function listarJugadores(): array
    {
        return $this->db->table('jugadores')
            ->select('id, username, email, es_admin, aura_actual, aura_max, auracoins, victorias, derrotas, eliminado_en')
            ->orderBy('eliminado_en IS NULL', 'DESC', false)
            ->orderBy('id', 'ASC')
            ->get()
            ->getResultArray();
    }

    public function obtenerJugador(int $jugadorId): ?array
    {
        return $this->db->table('jugadores')
            ->select('id, username, email, es_admin, aura_actual, aura_max, auracoins, victorias, derrotas, eliminado_en')
            ->where('id', $jugadorId)
            ->get()
            ->getRowArray();
    }

    public function actualizarJugador(
        int $jugadorId,
        string $username,
        ?string $email,
        int $auraActual,
        int $auraMax,
        int $auracoins,
        int $esAdmin
    ): array {
        return $this->llamarProc(
            'CALL sp_admin_actualizar_jugador(?, ?, ?, ?, ?, ?, ?)',
            [$jugadorId, $username, $email, $auraActual, $auraMax, $auracoins, $esAdmin]
        );
    }

    public function eliminarLogico(int $jugadorId, int $solicitanteId): array
    {
        return $this->llamarProc(
            'CALL sp_eliminar_jugador_logico(?, ?)',
            [$jugadorId, $solicitanteId]
        );
    }

    public function generarCodigoRecuperacion(string $email, string $codigoHash): array
    {
        return $this->llamarProc(
            'CALL sp_generar_codigo_recuperacion(?, ?, ?)',
            [$email, $codigoHash, 15]
        );
    }

    public function canjearCodigoRecuperacion(
        string $email,
        string $codigoHash,
        string $passwordHash
    ): array {
        return $this->llamarProc(
            'CALL sp_canjear_codigo_recuperacion(?, ?, ?)',
            [$email, $codigoHash, $passwordHash]
        );
    }

    public function registrarJugador(string $username, string $email, string $passwordHash): array
    {
        return $this->llamarProc(
            'CALL sp_registrar_jugador(?, ?, ?)',
            [$username, $email, $passwordHash]
        );
    }

    private function llamarProc(string $sql, array $params): array
    {
        $query = $this->db->query($sql, $params);
        $fila = $query->getRowArray() ?? [];
        $query->freeResult();
        $this->limpiarResultadosProcedimiento();

        return $fila;
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
