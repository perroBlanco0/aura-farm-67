<?php

namespace App\Controllers;

use App\Models\CapturaModel;
use Throwable;

class Captura extends BaseController
{
    protected $helpers = ['form', 'funciones', 'url'];

    public function resolver()
    {
        if (! $this->request->is('post')) {
            return $this->response->setStatusCode(405)->setJSON([
                'ok'      => false,
                'mensaje' => 'Método no permitido.',
            ]);
        }

        $datos = $this->request->getJSON(true);

        if (! is_array($datos)) {
            $datos = $this->request->getPost();
        }

        $spawnId    = (int) ($datos['spawn_id'] ?? 0);
        $toques     = (int) ($datos['toques'] ?? 0);
        $duracionMs = (int) ($datos['duracion_ms'] ?? 0);
        $exito      = ! empty($datos['exito']) ? 1 : 0;

        if ($spawnId <= 0) {
            return $this->response->setStatusCode(422)->setJSON([
                'ok'      => false,
                'mensaje' => 'Spawn inválido',
            ]);
        }

        try {
            $resultado = (new CapturaModel())->resolver(
                $spawnId,
                (int) session()->get('jugador_id'),
                $toques,
                $duracionMs,
                $exito
            );
        } catch (Throwable $error) {
            return $this->response->setStatusCode(422)->setJSON([
                'ok'      => false,
                'mensaje' => $error->getMessage(),
            ]);
        }

        if ($resultado === []) {
            return $this->response->setStatusCode(500)->setJSON([
                'ok'      => false,
                'mensaje' => 'El procedimiento no devolvió un resultado.',
            ]);
        }

        return $this->response->setJSON([
            'ok'           => true,
            'capturado'    => (int) ($resultado['capturado'] ?? 0) === 1,
            'mensaje'      => $resultado['mensaje'] ?? '',
            'carta_nombre' => $resultado['carta_nombre'] ?? '',
        ]);
    }
}
