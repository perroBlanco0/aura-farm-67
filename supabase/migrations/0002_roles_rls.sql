-- Gobernanza: roles (admin / lector / donante) y políticas RLS sobre donaciones.

create type public.app_role as enum ('admin', 'lector');

create table if not exists public.user_roles (
  user_id uuid primary key references auth.users (id) on delete cascade,
  rol public.app_role not null default 'lector',
  created_at timestamptz not null default now()
);

alter table public.user_roles enable row level security;

create or replace function public.rol_actual()
returns public.app_role
language sql
stable
security definer
set search_path = public
as $$
  select rol from public.user_roles where user_id = auth.uid();
$$;

-- Cada usuario ve su propio rol; solo un admin administra los roles.
create policy "ver mi rol" on public.user_roles
  for select to authenticated
  using (user_id = auth.uid() or public.rol_actual() = 'admin');

create policy "admin administra roles" on public.user_roles
  for all to authenticated
  using (public.rol_actual() = 'admin')
  with check (public.rol_actual() = 'admin');

-- Donaciones: el donante (anónimo) nunca lee la tabla; solo escribe vía la
-- Edge Function con service_role. admin y lector pueden consultarla.
create policy "admin y lector leen donaciones" on public.donaciones
  for select to authenticated
  using (public.rol_actual() in ('admin', 'lector'));

create policy "admin edita donaciones" on public.donaciones
  for all to authenticated
  using (public.rol_actual() = 'admin')
  with check (public.rol_actual() = 'admin');

-- Todo usuario nuevo entra como lector; los admin se asignan a mano.
create or replace function public.registrar_rol_por_defecto()
returns trigger
language plpgsql
security definer
set search_path = public
as $$
begin
  insert into public.user_roles (user_id, rol)
  values (new.id, 'lector')
  on conflict (user_id) do nothing;
  return new;
end;
$$;

drop trigger if exists on_auth_user_created on auth.users;
create trigger on_auth_user_created
  after insert on auth.users
  for each row execute function public.registrar_rol_por_defecto();
