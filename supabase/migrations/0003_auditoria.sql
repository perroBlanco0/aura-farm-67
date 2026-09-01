-- Trazabilidad: bitácora append-only de todo evento del sistema.

create table if not exists public.auditoria (
  id bigint generated always as identity primary key,
  evento text not null,
  actor text not null default 'anonimo',
  recurso text,
  recurso_id text,
  detalle jsonb not null default '{}'::jsonb,
  origen_ip text,
  user_agent text,
  created_at timestamptz not null default now()
);

create index if not exists auditoria_created_at_idx on public.auditoria (created_at desc);
create index if not exists auditoria_evento_idx on public.auditoria (evento);

alter table public.auditoria enable row level security;

-- Solo admin y lector consultan la bitácora; nadie la edita ni la borra
-- (append-only: las escrituras entran por la Edge Function con service_role).
create policy "admin y lector leen auditoria" on public.auditoria
  for select to authenticated
  using (public.rol_actual() in ('admin', 'lector'));

-- Traza automática de cambios sobre donaciones.
create or replace function public.auditar_donacion()
returns trigger
language plpgsql
security definer
set search_path = public
as $$
begin
  insert into public.auditoria (evento, actor, recurso, recurso_id, detalle)
  values (
    'donacion.' || lower(tg_op),
    coalesce(auth.uid()::text, 'service_role'),
    'donaciones',
    coalesce(new.id, old.id),
    case when tg_op = 'DELETE' then to_jsonb(old) else to_jsonb(new) end
  );
  return coalesce(new, old);
end;
$$;

drop trigger if exists auditar_donaciones on public.donaciones;
create trigger auditar_donaciones
  after insert or update or delete on public.donaciones
  for each row execute function public.auditar_donacion();

-- Traza de cambios de rol.
create or replace function public.auditar_rol()
returns trigger
language plpgsql
security definer
set search_path = public
as $$
begin
  insert into public.auditoria (evento, actor, recurso, recurso_id, detalle)
  values (
    'rol.' || lower(tg_op),
    coalesce(auth.uid()::text, 'service_role'),
    'user_roles',
    coalesce(new.user_id, old.user_id)::text,
    case when tg_op = 'DELETE' then to_jsonb(old) else to_jsonb(new) end
  );
  return coalesce(new, old);
end;
$$;

drop trigger if exists auditar_user_roles on public.user_roles;
create trigger auditar_user_roles
  after insert or update or delete on public.user_roles
  for each row execute function public.auditar_rol();
