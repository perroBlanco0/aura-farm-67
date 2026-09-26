<?php

namespace App\Controllers;

use App\Models\ArenaModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use Throwable;

class Arena extends BaseController
{
    protected $helpers = ['form', 'funciones', 'url'];

    public function index(): string
    {
        $arena = new ArenaModel();
        $jugador = $arena->obtenerJugador($this->jugadorId());

        if ($jugador === null) {
            throw PageNotFoundException::forPageNotFound(
                'Importa database/aura_duelos.sql antes de abrir la arena.'
            );
        }

        return view('arena/index', [
            'jugador' => $jugador,
            'es_admin' => (int) session()->get('es_admin') === 1,
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
            $duelo = (new ArenaModel())->crearDuelo($this->jugadorId(), $apuesta);
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

        if ($duelo === null || (int) $duelo['retador_id'] !== $this->jugadorId()) {
            throw PageNotFoundException::forPageNotFound('Duelo no encontrado.');
        }

        return view('arena/duelo', [
            'duelo' => $duelo,
            'mazo' => $arena->obtenerMazo($this->jugadorId()),
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
        $turnoEsperado = (int) $this->request->getPost('turno_esperado');

        if ($dueloId <= 0 || $cartaId <= 0 || $turnoEsperado <= 0) {
            return $this->response->setStatusCode(422)->setJSON([
                'ok' => false,
                'mensaje' => 'Duelo, carta o turno inválidos.',
            ]);
        }

        try {
            $resultado = (new ArenaModel())->lanzarCarta(
                $dueloId,
                $cartaId,
                $turnoEsperado,
                $this->jugadorId()
            );
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

    private function jugadorId(): int
    {
        return (int) session()->get('jugador_id');
    }
}
