<?php

namespace App\Controllers;

use App\Models\ArenaModel;
use CodeIgniter\Exceptions\PageNotFoundException;

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

        return view('mapa/index', [
            'jugador' => $jugador,
            'es_admin' => (int) session()->get('es_admin') === 1,
            'error' => session()->getFlashdata('error'),
            'mazo' => $arena->obtenerMazo($jugadorId),
        ]);
    }
}
