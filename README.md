# Aura Farm 67 · donación ficticia 🐱

Sitio de broma: simula una donación, pide "tarjeta" solo como gag y termina con
"¡TE LA CREÍSTE WE!". No se cobra nada y **nunca** se envían ni guardan datos de tarjeta.

## Estructura

- `public/index.html` — frontend (SweetAlert2, gatos de Cataas, responsive).
- `supabase/functions/donar/index.ts` — Edge Function: calcula el aura, manda el
  correo por Resend y guarda la donación en Postgres.
- `supabase/migrations/0001_donaciones.sql` — tabla `donaciones` con RLS activo
  (solo la función, con service_role, escribe).
- `netlify.toml` — publica `public/`.

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
