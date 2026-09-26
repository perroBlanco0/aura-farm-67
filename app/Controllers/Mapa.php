<?php

namespace App\Controllers;

use App\Models\ArenaModel;
use App\Models\CapturaModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use Throwable;

class Mapa extends BaseController
{
    protected $helpers = ['form', 'funciones', 'url'];

    public function index(): string
    {
        $arena = new ArenaModel();
        $jugadorId = (int) session()->get('jugador_id');
        $jugador = $arena->obtenerJugador($jugadorId);

        if ($jugador === null) {
            throw PageNotFoundException::forPageNotFound(
                'Importa database/aura_duelos.sql antes de abrir el mapa.'
            );
        }

        $spawns = [];
        $error = session()->getFlashdata('error');

        try {
            $spawns = (new CapturaModel())->spawnMichis($jugadorId);
        } catch (Throwable $excepcion) {
            $error ??= 'Reimporta database/aura_duelos.sql: faltan los michis salvajes (' . $excepcion->getMessage() . ')';
        }

        return view('mapa/index', [
            'jugador' => $jugador,
            'es_admin' => (int) session()->get('es_admin') === 1,
            'error' => $error,
            'spawns' => $spawns,
            'mazo_ids' => array_map('intval', array_column($arena->obtenerMazo($jugadorId), 'id')),
        ]);
    }
}
