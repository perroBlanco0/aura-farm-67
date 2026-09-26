# Aura Arena 67

Juego web de duelos de cartas meme y apuestas de AuraCoins construido con CodeIgniter 4 y MySQL 8.
El flujo del backend es deliberadamente directo: `Controlador -> Modelo -> Vista/JSON`.
Todo cálculo de combate, efecto de carta y movimiento de moneda ocurre dentro de stored procedures.

## Requisitos

- PHP 8.2 o superior con `intl`, `mbstring` y `mysqli`.
- Composer 2.
- MySQL 8.

## Instalación local

1. Instala las dependencias:

   ```bash
   composer install
   ```

2. Crea el archivo de entorno:

   ```bash
   cp env .env
   ```

   Ajusta en `.env` el usuario, contraseña, host y puerto de MySQL si no usas los valores locales
   por defecto.

3. Importa la base de datos, los seeds y los procedimientos:

   ```bash
   mysql -u root -p < database/aura_duelos.sql
   ```

   El script es idempotente: crea `aura_duelos`, conserva el progreso mutable de jugadores
   existentes y recrea los procedimientos con su versión actual.

4. Inicia CodeIgniter:

   ```bash
   php spark serve
   ```

5. Abre `http://localhost:8080`.

El seed incluye al jugador `AuraFarmer67` con 2.500 AuraCoins, al rival
`The Rizzler Bot` y seis cartas. El selector del lobby retiene la apuesta al crear el duelo.

## Arquitectura

- `database/aura_duelos.sql`: tablas, seeds, `sp_iniciar_duelo_meme` y `sp_jugar_carta_turno`.
- `app/Controllers/Arena.php`: lobby, creación de duelo, mesa y endpoint AJAX.
- `app/Models/ArenaModel.php`: consultas planas y llamadas `CALL sp_...`.
- `app/Helpers/funciones_helper.php`: formato de AuraCoins, barra, rareza y frases meme.
- `app/Views/arena/`: lobby y mesa responsiva.

### Contrato AJAX

`POST /arena/jugar` recibe `duelo_id` y `carta_id`. Responde JSON con el daño realizado y
recibido, Aura restante, efectos activados, saldo de AuraCoins, frase del turno y estado final.
