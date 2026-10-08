create table if not exists public.donaciones (
  id text primary key,
  nombre text not null,
  email text not null,
  monto numeric not null default 0,
  moneda text not null default 'CLP',
  aura integer not null default 67,
  correo_enviado boolean not null default false,
  created_at timestamptz not null default now()
);

alter table public.donaciones enable row level security;
-- Sin políticas: solo la Edge Function (service_role) puede leer/escribir.
