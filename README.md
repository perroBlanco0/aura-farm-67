# Michi Arena 🐱

Juego web de duelos de cartas de gatos virales de TikTok (Oiia Oiia Cat, Big Floppa,
Chipi Chipi Chapa, Smudge, Michi Llorón, Beluga) y apuestas de AuraCoins, construido
con CodeIgniter 4 y MySQL 8 / MariaDB.

El flujo del backend es deliberadamente directo: `Controlador -> Modelo -> Vista/JSON`.
Todo cálculo de combate, efecto de carta, movimiento de moneda y gestión de jugadores
ocurre dentro de stored procedures transaccionales.

Autor: [@perroBlanco0](https://github.com/perroBlanco0)

## Requisitos

- PHP 8.2 o superior con `intl`, `mbstring` y `mysqli`.
- Composer 2.
- MySQL 8 o MariaDB 10.6+.
- Mailpit (opcional, recomendado) para capturar los correos de recuperación en desarrollo.

## Instalación local

1. Instala las dependencias:

   ```bash
   composer install
   ```

2. Crea el archivo de entorno:

   ```bash
   cp env .env
   ```

   Ajusta en `.env` el usuario, contraseña, host y puerto de MySQL si no usas los
   valores locales por defecto. La sección `EMAIL` apunta por defecto a Mailpit en
   `localhost:1025`.

3. Importa la base de datos, los seeds y los procedimientos:

   ```bash
   mysql -u root -p < database/aura_duelos.sql
   ```

   El script es idempotente: crea `aura_duelos`, migra bases antiguas añadiendo las
   columnas de autenticación, conserva el progreso mutable de jugadores existentes y
   recrea los procedimientos con su versión actual.

4. (Opcional) Levanta Mailpit para ver los correos de recuperación:

   ```bash
   mailpit --listen 127.0.0.1:8025 --smtp 127.0.0.1:1025
   # bandeja en http://localhost:8025
   ```

5. Inicia CodeIgniter:

   ```bash
   php spark serve
   ```

6. Abre `http://localhost:8080` y entra con `demo` / `nirvana`.

## Cuentas de semilla

| Usuario | Clave | Rol |
| --- | --- | --- |
| `demo` | `nirvana` | admin (mantenedor) |
| `michi_fan` | `michi123` | jugador |
| `duelista_pro` | `michi123` | jugador |
| `Michi Aburrido Bot` | — | rival automático, sin login |

Los correos de las cuentas demo apuntan al namespace Testmail `vkwxq`
(`vkwxq.<tag>@inbox.testmail.app`) para verificar la entrega en pruebas externas.

## Funcionalidades

- **Login con sesión**: `GET/POST /login`, `GET /salir`.
- **Recuperación de clave por correo**: `GET/POST /recuperar` genera un código de
  6 dígitos con expiración de 15 minutos, lo guarda hasheado (SHA-256) y lo envía por
  SMTP con plantilla HTML de marca. `GET/POST /verificar` canjea el código y fija la
  nueva clave. La respuesta no revela si el correo existe.
- **Mantenedor de usuarios** (solo admin): `GET /mantenedor` lista jugadores,
  `GET/POST /mantenedor/editar/{id}` edita username/correo/aura/AuraCoins/rol, y
  `POST /mantenedor/eliminar/{id}` hace borrado lógico (`eliminado_en`). El último
  admin no puede quitarse ni eliminarse.
- **Duelos**: `POST /arena/iniciar` retiene la apuesta; `POST /arena/jugar` juega un
  turno con protección anti doble-clic (`turno_esperado`) y verificación de dueño
  (`retador_id` validado contra la sesión dentro del procedimiento).
- **Responsivo**: lobby, mesa, login y mantenedor funcionan en móvil (carrusel de
  cartas con scroll-snap, sin overflow horizontal).

## Arquitectura

- `database/aura_duelos.sql`: tablas, seeds y todos los procedimientos
  (`sp_iniciar_duelo_meme`, `sp_jugar_carta_turno`, `sp_admin_actualizar_jugador`,
  `sp_eliminar_jugador_logico`, `sp_generar_codigo_recuperacion`,
  `sp_canjear_codigo_recuperacion`).
- `app/Controllers/`: `Arena` (juego), `Auth` (login/recuperación), `Mantenedor` (CRUD).
- `app/Models/`: `ArenaModel` y `UsuariosModel`, consultas planas y `CALL sp_...`.
- `app/Filters/AuthFilter.php`: protege `arena/*` y `mantenedor/*` por sesión.
- `app/Helpers/funciones_helper.php`: formato de AuraCoins, barra, rareza y frases meme.
- `app/Views/`: `arena/`, `auth/`, `mantenedor/` y `emails/recuperacion.php`.

### Correo

El envío usa SMTP configurable por `.env` (`email.SMTPHost`, `email.SMTPPort`,
`email.SMTPUser`, `email.SMTPPass`, `email.SMTPCrypto`). Nunca pongas credenciales
reales en `Constants.php` ni en git; usa `.env` o variables de entorno del servidor.
En el despliegue de demo los correos se capturan con Mailpit en el mismo servidor.

### Contrato AJAX

`POST /arena/jugar` recibe `duelo_id`, `carta_id` y `turno_esperado`. El procedimiento
bloquea el duelo, valida que el retador sea el jugador de la sesión y rechaza
solicitudes repetidas para un turno ya procesado. Responde JSON con el daño realizado
y recibido, Aura restante, efectos activados, saldo de AuraCoins, frase del turno y
estado final.
