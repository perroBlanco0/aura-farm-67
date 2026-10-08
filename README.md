# Aura Farm 67 · donación ficticia 🐱

Sitio de broma: simula una donación, pide "tarjeta" solo como gag y termina con
"¡TE LA CREÍSTE WE!". No se cobra nada y **nunca** se envían ni guardan datos de tarjeta.

## Estructura

- `public/index.html` — frontend (SweetAlert2, gatos de Cataas, responsive, QR del sitio y del diploma).
- `public/admin.html` — panel de gobernanza: login por enlace mágico, donaciones y bitácora según rol.
- `public/governance.config.js` — roles, permisos, campos permitidos/prohibidos, retención y eventos trazados.
- `supabase/functions/donar/index.ts` — Edge Function: calcula el aura, manda el
  correo por Resend y guarda la donación en Postgres.
- `supabase/migrations/0001_donaciones.sql` — tabla `donaciones` con RLS activo
  (solo la función, con service_role, escribe).
- `supabase/migrations/0002_roles_rls.sql` — tabla `user_roles` y políticas por rol.
- `supabase/migrations/0003_auditoria.sql` — bitácora `auditoria` y triggers de traza.
- `netlify.toml` — publica `public/`.

## Gobernanza

| Rol | Autenticado | Puede |
| --- | --- | --- |
| donante | no | crear una donación (solo vía la Edge Function) |
| lector | sí | leer donaciones y auditoría |
| admin | sí | además editar/borrar donaciones y asignar roles |

Todo usuario que se registra entra como `lector`; para promover a admin:

```sql
update public.user_roles set rol = 'admin' where user_id = '<uuid>';
```

La Edge Function rechaza cualquier campo fuera de `nombre, email, monto, moneda`
(los datos de tarjeta del gag nunca salen del navegador) y traza cada evento en
`public.auditoria`, junto con los triggers que registran altas, cambios y bajas.

## Despliegue

1. Base de datos y función:

   ```bash
   supabase link --project-ref <PROJECT_REF>
   supabase db push
   supabase secrets set RESEND_API_KEY=<key>
   supabase functions deploy donar
   ```

2. En `public/index.html`, apunta `API_URL` a
   `https://<PROJECT_REF>.supabase.co/functions/v1/donar`.

3. Frontend: conecta el repo en Netlify (o Vercel/Cloudflare Pages); no requiere build.

`SUPABASE_URL` y `SUPABASE_SERVICE_ROLE_KEY` ya vienen inyectadas en las Edge Functions.
Con el remitente de prueba `onboarding@resend.dev` solo puedes enviar a tu propio
correo registrado en Resend; para cualquier destinatario hay que verificar un dominio.

## Contrato

`POST /donar` con `{ nombre, email, monto, moneda }` responde
`{ id, aura, mensaje, correo_enviado, guardado, meme }`.
