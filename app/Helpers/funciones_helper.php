<?php

if (! function_exists('formatear_auracoins')) {
    function formatear_auracoins(int|float $monto): string
    {
        $signo = $monto < 0 ? '-' : '';

        return '💰 ' . $signo . number_format(abs($monto), 0, ',', '.') . ' $MICHI';
    }
}

if (! function_exists('calcular_barra_aura')) {
    function calcular_barra_aura(int|float $actual, int|float $max): float
    {
        if ($max <= 0) {
            return 0;
        }

        return round(max(0, min(100, ($actual / $max) * 100)), 1);
    }
}

if (! function_exists('badge_rareza')) {
    function badge_rareza(int $nivel): string
    {
        if ($nivel === 67) {
            return 'rareza-67';
        }

        if ($nivel >= 40) {
            return 'rareza-epica';
        }

        if ($nivel >= 20) {
            return 'rareza-rara';
        }

        return 'rareza-comun';
    }
}

if (! function_exists('frase_resultado_meme')) {
    function frase_resultado_meme(string $estado): string
    {
        $frases = [
            'VICTORIA_RETADOR' => [
                'Nivel Dios del Rizz: el bot perdió hasta el Wi-Fi.',
                'Aura infinita confirmada. El chat solo pudo decir W.',
                'Mogging perfecto: la apuesta volvió con amigos.',
            ],
            'DERROTA_RETADOR' => [
                'Cringe crítico. Toca entrenar en el gimnasio del aura.',
                'El bot dijo “skill issue” y se llevó el pozo.',
                'F en el chat. La remontada comienza en el próximo duelo.',
            ],
        ];

        $opciones = $frases[$estado] ?? ['La batalla de aura continúa.'];

        return $opciones[array_rand($opciones)];
    }
}

if (! function_exists('efecto_descripcion')) {
    function efecto_descripcion(string $efecto): string
    {
        return [
            'CRITICO_MEME' => '×1.25 de daño al atacar',
            'ROBAR_AURA'   => 'recuperas 25% del daño que haces',
            'ESCUDO_CHILL' => 'recibes la mitad de daño',
            'NINGUNO'      => 'sin efecto especial',
        ][$efecto] ?? 'efecto misterioso';
    }
}
