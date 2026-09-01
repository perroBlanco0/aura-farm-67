import { createClient } from "https://esm.sh/@supabase/supabase-js@2.45.4";

const CORS = {
  "Access-Control-Allow-Origin": "*",
  "Access-Control-Allow-Headers": "Content-Type, Authorization, apikey",
  "Access-Control-Allow-Methods": "POST,OPTIONS",
  "Content-Type": "application/json",
};

const RESEND_ENDPOINT = "https://api.resend.com/emails";

const reply = (status: number, body: unknown) =>
  new Response(JSON.stringify(body), { status, headers: CORS });

function html(nombre: string, monto: number, moneda: string, aura: number, id: string) {
  return `
<div style="font-family:Nunito,Arial,sans-serif;background:#1e1b4b;color:#fff;padding:28px;border-radius:18px">
  <h1 style="color:#fde047;margin:0 0 4px">6 7 — Diploma de Aura 🐱</h1>
  <p>Hola <b>${nombre}</b>, tu donación de <b>${monto} ${moneda}</b> fue... <b>puro meme</b>.</p>
  <p style="font-size:20px"><b>TE LA CREÍSTE WE 😹</b></p>
  <p>No se cobró nada, no existe ninguna tarjeta guardada, y nunca la hubo.
     Lo único real es tu nuevo nivel de aura.</p>
  <p style="font-size:26px;color:#f472b6"><b>+${aura} de aura</b></p>
  <img src="https://cataas.com/cat/says/te%20la%20creiste?fontSize=60&fontColor=red" width="320" alt="gato meme" style="border-radius:14px" />
  <p style="font-size:12px;opacity:.7">ID: ${id} · Sitio de broma, sin cobros reales.</p>
</div>`;
}

async function enviarCorreo(
  destino: string, nombre: string, monto: number, moneda: string, aura: number, id: string,
) {
  const apiKey = Deno.env.get("RESEND_API_KEY");
  if (!apiKey) return { enviado: false, motivo: "RESEND_API_KEY no configurada" };
  try {
    const res = await fetch(RESEND_ENDPOINT, {
      method: "POST",
      headers: {
        Authorization: `Bearer ${apiKey}`,
        "Content-Type": "application/json",
        Accept: "application/json",
        "User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0 Safari/537.36",
      },
      body: JSON.stringify({
        from: Deno.env.get("MAIL_FROM") ?? "Aura Farm 67 <onboarding@resend.dev>",
        to: [destino],
        subject: "🐱 67 · Tu diploma de aura (te la creíste we xd)",
        html: html(nombre, monto, moneda, aura, id),
      }),
    });
    const data = await res.json();
    return res.ok ? { enviado: true, respuesta: data } : { enviado: false, motivo: data };
  } catch (exc) {
    return { enviado: false, motivo: String(exc) };
  }
}

Deno.serve(async (req) => {
  if (req.method === "OPTIONS") return reply(200, { ok: true });

  let body: Record<string, unknown>;
  try {
    body = await req.json();
  } catch {
    return reply(400, { mensaje: "JSON inválido, we 😿" });
  }

  const nombre = String(body.nombre ?? "Anónimo").slice(0, 60);
  const email = String(body.email ?? "").trim().slice(0, 120);
  const moneda = String(body.moneda ?? "CLP").slice(0, 20);
  const monto = Number(body.monto) || 0;

  if (!email.includes("@")) return reply(400, { mensaje: "Correo inválido 😹" });

  const id = crypto.randomUUID().slice(0, 8);
  const aura = 67 + Math.floor(Math.min(monto, 9999)) * 10;
  const correo = await enviarCorreo(email, nombre, monto, moneda, aura, id);

  const supabase = createClient(
    Deno.env.get("SUPABASE_URL")!,
    Deno.env.get("SUPABASE_SERVICE_ROLE_KEY")!,
  );
  const { error } = await supabase.from("donaciones").insert({
    id, nombre, email, monto, moneda, aura, correo_enviado: correo.enviado,
  });
  if (error) console.error("insert error", error.message);

  return reply(200, {
    id,
    aura,
    mensaje: correo.enviado
      ? "Te mandamos el diploma a tu correo 📬"
      : "No se pudo mandar el correo, pero el aura ya es tuya 😼",
    correo_enviado: correo.enviado,
    guardado: !error,
    meme: "te la creíste we xd",
  });
});
