/**
 * Configuración de gobernanza del proyecto Aura Farm 67.
 * Fuente única de verdad para roles, permisos, datos permitidos y trazabilidad.
 * La usan el frontend (public/) y la Edge Function (supabase/functions/donar).
 */
export const GOVERNANCE = {
  version: "1.0.0",
  proyecto: "aura-farm-67",

  roles: {
    donante: {
      descripcion: "Visitante anónimo que completa el formulario de broma.",
      autenticacion: false,
      permisos: ["donacion:crear"],
    },
    lector: {
      descripcion: "Puede consultar donaciones y bitácora, sin modificar.",
      autenticacion: true,
      permisos: ["donacion:leer", "auditoria:leer"],
    },
    admin: {
      descripcion: "Administra donaciones, roles y consulta la bitácora.",
      autenticacion: true,
      permisos: [
        "donacion:leer",
        "donacion:editar",
        "donacion:borrar",
        "auditoria:leer",
        "rol:asignar",
      ],
    },
  },

  /** Único payload que el frontend puede enviar al backend. */
  camposPermitidos: ["nombre", "email", "monto", "moneda"],

  /** Campos que jamás se transmiten ni se persisten (el gag de la tarjeta). */
  camposProhibidos: ["numero_tarjeta", "cvv", "expiracion", "titular"],

  datos: {
    retencionDias: 90,
    baseLegal: "consentimiento del usuario al enviar el formulario",
    responsable: "perroBlanco0",
    finalidad: "envío del diploma de aura y estadísticas del sitio",
  },

  /** Eventos que quedan trazados en public.auditoria. */
  eventos: [
    "donacion.insert",
    "donacion.update",
    "donacion.delete",
    "correo.enviado",
    "correo.fallido",
    "validacion.rechazada",
    "rol.insert",
    "rol.update",
    "rol.delete",
  ],

  puede(rol, permiso) {
    return Boolean(this.roles[rol]?.permisos.includes(permiso));
  },
};

export default GOVERNANCE;
