<?php

namespace App\Controllers;

use App\Models\UsuariosModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use Throwable;

class Mantenedor extends BaseController
{
    protected $helpers = ['form', 'funciones', 'url'];

    public function index(): string
    {
        $this->exigirAdmin();

        return view('mantenedor/index', [
            'jugadores' => (new UsuariosModel())->listarJugadores(),
            'error' => session()->getFlashdata('error'),
            'ok' => session()->getFlashdata('ok'),
        ]);
    }

    public function editar(int $id)
    {
        $this->exigirAdmin();

        $usuarios = new UsuariosModel();
        $jugador = $usuarios->obtenerJugador($id);

        if ($jugador === null || $jugador['eliminado_en'] !== null) {
            throw PageNotFoundException::forPageNotFound('Jugador no encontrado.');
        }

        if (! $this->request->is('post')) {
            return view('mantenedor/editar', [
                'jugador' => $jugador,
                'error' => null,
            ]);
        }

        $username = trim((string) $this->request->getPost('username'));
        $email = trim((string) $this->request->getPost('email'));
        $auraActual = (int) $this->request->getPost('aura_actual');
        $auraMax = (int) $this->request->getPost('aura_max');
        $auracoins = (int) $this->request->getPost('auracoins');
        $esAdmin = $this->request->getPost('es_admin') ? 1 : 0;

        if ($username === '') {
            return view('mantenedor/editar', [
                'jugador' => $jugador,
                'error' => 'El username no puede quedar vacío.',
            ]);
        }

        try {
            $usuarios->actualizarJugador(
                $id,
                $username,
                $email === '' ? null : $email,
                $auraActual,
                $auraMax,
                $auracoins,
                $esAdmin
            );
        } catch (Throwable $error) {
            return view('mantenedor/editar', [
                'jugador' => $jugador,
                'error' => $error->getMessage(),
            ]);
        }

        return redirect()->to('/mantenedor')->with('ok', "Michi {$username} actualizado.");
    }

    public function eliminar(int $id)
    {
        $this->exigirAdmin();

        try {
            (new UsuariosModel())->eliminarLogico($id, (int) session()->get('jugador_id'));
        } catch (Throwable $error) {
            return redirect()->to('/mantenedor')->with('error', $error->getMessage());
        }

        return redirect()->to('/mantenedor')->with('ok', 'Jugador enviado a mimir (borrado lógico).');
    }

    private function exigirAdmin(): void
    {
        if ((int) session()->get('es_admin') !== 1) {
            throw PageNotFoundException::forPageNotFound();
        }
    }
}
