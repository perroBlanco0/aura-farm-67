<?php

namespace App\Controllers;

use App\Models\UsuariosModel;
use Throwable;

class Auth extends BaseController
{
    protected $helpers = ['form', 'funciones', 'url'];

    public function login()
    {
        if ($this->request->is('post')) {
            $username = trim((string) $this->request->getPost('username'));
            $clave = (string) $this->request->getPost('clave');

            $jugador = (new UsuariosModel())->buscarParaLogin($username);

            if ($jugador === null || ! password_verify($clave, (string) $jugador['password_hash'])) {
                return view('auth/login', [
                    'error' => 'Usuario o clave incorrectos. El michi no te reconoció.',
                ]);
            }

            session()->set([
                'jugador_id' => (int) $jugador['id'],
                'username' => $jugador['username'],
                'es_admin' => (int) $jugador['es_admin'],
            ]);

            return redirect()->to('/arena');
        }

        return view('auth/login', [
            'error' => session()->getFlashdata('error'),
            'ok' => session()->getFlashdata('ok'),
        ]);
    }

    public function salir()
    {
        session()->destroy();

        return redirect()->to('/login');
    }

    public function recuperar()
    {
        if (! $this->request->is('post')) {
            return view('auth/recuperar', ['error' => null]);
        }

        $correo = trim((string) $this->request->getPost('email'));
        $codigo = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        try {
            $fila = (new UsuariosModel())->generarCodigoRecuperacion(
                $correo,
                hash('sha256', $codigo)
            );
        } catch (Throwable) {
            $fila = [];
        }

        if (! empty($fila['jugador_id'])) {
            $this->enviarCorreoCodigo($correo, (string) $fila['username'], $codigo);
        }

        return view('auth/verificar', [
            'email' => $correo,
            'ok' => 'Si el correo existe en la arena, ya tiene un código de 6 dígitos. Revisa tu bandeja (y el spam del gato).',
            'error' => null,
        ]);
    }

    public function verificar()
    {
        if (! $this->request->is('post')) {
            return view('auth/verificar', [
                'email' => '',
                'ok' => null,
                'error' => null,
            ]);
        }

        $correo = trim((string) $this->request->getPost('email'));
        $codigo = trim((string) $this->request->getPost('codigo'));
        $clave = (string) $this->request->getPost('clave');

        if (strlen($clave) < 6) {
            return view('auth/verificar', [
                'email' => $correo,
                'ok' => null,
                'error' => 'La nueva clave necesita al menos 6 caracteres.',
            ]);
        }

        try {
            (new UsuariosModel())->canjearCodigoRecuperacion(
                $correo,
                hash('sha256', $codigo),
                password_hash($clave, PASSWORD_BCRYPT)
            );
        } catch (Throwable $error) {
            return view('auth/verificar', [
                'email' => $correo,
                'ok' => null,
                'error' => $error->getMessage(),
            ]);
        }

        return redirect()->to('/login')->with('ok', 'Clave actualizada. Entra con tu nueva contraseña, michi.');
    }

    private function enviarCorreoCodigo(string $correo, string $username, string $codigo): void
    {
        try {
            $email = \Config\Services::email();
            $email->setTo($correo);
            $email->setSubject('Tu código de recuperación Michi Arena');
            $email->setMessage(view('emails/recuperacion', [
                'username' => $username,
                'codigo' => $codigo,
            ]));
            $email->setAltMessage(
                "Hola {$username}, tu código de recuperación Michi Arena es {$codigo}. Expira en 15 minutos."
            );

            if (! $email->send(false)) {
                log_message('error', 'No se pudo enviar correo de recuperación: {debug}', [
                    'debug' => $email->printDebugger(['headers', 'subject']),
                ]);
            }
        } catch (Throwable $error) {
            log_message('error', 'Error enviando correo de recuperación: {msg}', [
                'msg' => $error->getMessage(),
            ]);
        }
    }
}
