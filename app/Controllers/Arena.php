<?php

namespace App\Controllers;

use App\Models\ArenaModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use Throwable;

class Arena extends BaseController
{
    protected $helpers = ['form', 'funciones', 'url'];

    private const JUGADOR_DEMO_ID = 1;

    public function index(): string
    {
        $arena = new ArenaModel();
        $jugador = $arena->obtenerJugador(self::JUGADOR_DEMO_ID);

        if ($jugador === null) {
            throw PageNotFoundException::forPageNotFound(
                'Importa database/aura_duelos.sql antes de abrir la arena.'
            );
        }

        return view('arena/index', [
            'jugador' => $jugador,
            'error' => session()->getFlashdata('error'),
        ]);
    }

    public function iniciar()
    {
        $apuesta = (int) $this->request->getPost('apuesta');

        if ($apuesta <= 0) {
            return redirect()->to('/arena')->with('error', 'Elige una apuesta de AuraCoins válida.');
        }

        try {
            $duelo = (new ArenaModel())->crearDuelo(self::JUGADOR_DEMO_ID, $apuesta);
        } catch (Throwable $error) {
            return redirect()->to('/arena')->with('error', $error->getMessage());
        }

        if (! isset($duelo['duelo_id'])) {
            return redirect()->to('/arena')->with('error', 'El duelo no pudo iniciar.');
        }

        return redirect()->to('/arena/duelo/' . $duelo['duelo_id']);
    }

    public function duelo(int $id): string
    {
        $arena = new ArenaModel();
        $duelo = $arena->obtenerDuelo($id);

        if ($duelo === null || (int) $duelo['retador_id'] !== self::JUGADOR_DEMO_ID) {
            throw PageNotFoundException::forPageNotFound('Duelo no encontrado.');
        }

        return view('arena/duelo', [
            'duelo' => $duelo,
            'mazo' => $arena->obtenerMazo(self::JUGADOR_DEMO_ID),
        ]);
    }

    public function jugar()
    {
        if (! $this->request->is('post')) {
            return $this->response->setStatusCode(405)->setJSON([
                'ok' => false,
                'mensaje' => 'Método no permitido.',
            ]);
        }

        $dueloId = (int) $this->request->getPost('duelo_id');
        $cartaId = (int) $this->request->getPost('carta_id');

        if ($dueloId <= 0 || $cartaId <= 0) {
            return $this->response->setStatusCode(422)->setJSON([
                'ok' => false,
                'mensaje' => 'Duelo o carta inválidos.',
            ]);
        }

        try {
            $resultado = (new ArenaModel())->lanzarCarta($dueloId, $cartaId);
        } catch (Throwable $error) {
            return $this->response->setStatusCode(422)->setJSON([
                'ok' => false,
                'mensaje' => $error->getMessage(),
            ]);
        }

        if ($resultado === []) {
            return $this->response->setStatusCode(500)->setJSON([
                'ok' => false,
                'mensaje' => 'El procedimiento no devolvió un resultado.',
            ]);
        }

        $resultado['ok'] = true;
        $resultado['frase_resultado'] = frase_resultado_meme($resultado['estado']);

        return $this->response->setJSON($resultado);
    }
}
