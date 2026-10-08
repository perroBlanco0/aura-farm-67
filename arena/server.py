"""Nebulous Sparse Arena — daemon headless del mundo.

Mundo 2000x2000 a 60 ticks/s en CPU: comida estática, jugadores por
WebSocket (solo mandan target del mouse) y 20 bots con visión local
de 350px cuyas decisiones salen de un árbitro simbólico HDC
(bind/bundle/cleanup — el mismo core del Sparse Engine).

Broadcast por tick: deltas compactos JSON (<1 KB típico).
Uso:  python arena/server.py  ->  ws://localhost:4002
"""
import asyncio
import json
import math
import random
import sys
import os
import time

ROOT = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
sys.path.insert(0, ROOT)
from hdc_engine.reasoner import HDCReasoner   # árbitro simbólico real

# ---------------- mundo ----------------
W = H = 2000
FPS = 60
FOOD_N = 180
FOOD_MASS = 2.0
PLAYER_START_MASS = 40.0
BOT_START_MASS = 35.0
VISION = 350.0
EAT_RATIO = 1.2          # hay que ser 20% más grande para comer
BOT_N = 20
MAX_SPEED = 190.0        # px/s con masa mínima
MAX_MASS = 600.0         # techo: radio ~196px — nadie cubre el mundo

def radius(mass):  return 8.0 * math.sqrt(mass)        # agar: r ∝ sqrt(m)
def speed(mass):   return MAX_SPEED * (mass ** -0.22)  # más grande = más lento

# ---------------- árbitro HDC: decide el MODO del bot ----------------
# Récords: (Peligro x nivel) (+) (Presa x nivel) -> Modo
# niveles: Cerca / Lejos / Nada ; modos: Huir / Cazar / Pastar
class Arbiter:
    def __init__(self, seed=99):
        self.r = HDCReasoner(seed=seed)
        mem = self.r.memory
        for n in ("R:Peligro", "R:Presa", "R:Modo",
                  "N:Cerca", "N:Lejos", "N:Nada",
                  "M:Huir", "M:Cazar", "M:Pastar"):
            mem.get_or_create(n)
        rules = {  # (peligro, presa) -> modo
            ("N:Cerca", "N:Cerca"): "M:Huir",
            ("N:Cerca", "N:Lejos"): "M:Huir",
            ("N:Cerca", "N:Nada"):  "M:Huir",
            ("N:Lejos", "N:Cerca"): "M:Cazar",
            ("N:Lejos", "N:Lejos"): "M:Cazar",
            ("N:Lejos", "N:Nada"):  "M:Pastar",
            ("N:Nada",  "N:Cerca"): "M:Cazar",
            ("N:Nada",  "N:Lejos"): "M:Pastar",
            ("N:Nada",  "N:Nada"):  "M:Pastar",
        }
        for k, (pel, pre) in enumerate(rules):
            self.r.store_record(f"Sit:{k}",
                {"R:Peligro": pel, "R:Presa": pre, "R:Modo": rules[(pel, pre)]})
        self._idx = {(p, q): i for i, (p, q) in enumerate(rules)}

    def decide(self, pel_nivel, presa_nivel):
        """Consulta simbólica: desata R:Modo del récord y devuelve M:*."""
        rec = self._idx.get((pel_nivel, presa_nivel), 8)
        return self.r.query_relation(f"Sit:{rec}", "R:Modo")

ARB = Arbiter()

# ---------------- estado ----------------
cells = {}      # id -> dict(cell)   (jugadores + bots)
food = {}       # fid -> (x, y)
next_id = [1]
clients = {}    # ws -> pid
world_meta = {} # pid -> {name,color}
tps_counter = {"n": 0}
bytes_last = [0]

BOT_NAMES = ["Álgebra", "Vector", "Hamming", "XOR", "Bundle", "Kanerva",
             "Hiper", "Símbolo", "Kernel", "Sparse", "Delta", "Tupla",
             "Lambda", "Recoil", "Parry", "Knockback", "Raster",
             "Daemon", "Tick", "Bit"]

def spawn_cell(kind, name=None, color=None, mass=None):
    cid = next_id[0]; next_id[0] += 1
    m = mass if mass is not None else (BOT_START_MASS if kind == "bot"
                                       else PLAYER_START_MASS)
    for _ in range(40):
        x, y = random.uniform(120, W - 120), random.uniform(120, H - 120)
        if all((x - c["x"]) ** 2 + (y - c["y"]) ** 2 > (radius(c["m"]) + 90) ** 2
               for c in cells.values()):
            break
    cells[cid] = {"id": cid, "kind": kind, "x": x, "y": y, "m": m,
                  "tx": x, "ty": y, "name": name, "color": color,
                  "dead_t": 0.0, "mode": "Pastar"}
    return cid

def refil_food():
    while len(food) < FOOD_N:
        fid = next_id[0]; next_id[0] += 1
        food[fid] = (random.uniform(10, W - 10), random.uniform(10, H - 10))
        broadcast_queue.append({"t": "food+", "i": fid,
                                "x": round(food[fid][0]), "y": round(food[fid][1])})

broadcast_queue = []   # eventos discretos: comida, muertes, altas

def level(dist):
    if dist < VISION * 0.55: return "N:Cerca"
    if dist < VISION:        return "N:Lejos"
    return "N:Nada"

def bot_think(b):
    """Percepción local justa -> vectores Peligro/Caza/Comida ->
    el árbitro HDC elige el modo; la dirección es la suma vectorial
    del modo elegido."""
    vx_d = vy_d = vx_h = vy_h = vx_f = vy_f = 0.0
    dmin_d = dmin_h = 1e9
    for c in cells.values():
        if c["id"] == b["id"] or c["dead_t"]:
            continue
        dx, dy = c["x"] - b["x"], c["y"] - b["y"]
        d2 = dx * dx + dy * dy
        if d2 > VISION * VISION:
            continue                     # fuera del cono: no lo ve
        d = math.sqrt(d2) or 1.0
        if c["m"] > b["m"] * EAT_RATIO:  # me puede comer -> peligro
            w = (VISION - d) / VISION * (c["m"] / b["m"])
            vx_d -= dx / d * w; vy_d -= dy / d * w
            dmin_d = min(dmin_d, d)
        elif b["m"] > c["m"] * EAT_RATIO:  # presa
            w = (VISION - d) / VISION
            vx_h += dx / d * w; vy_h += dy / d * w
            dmin_h = min(dmin_h, d)
    # comida solo si no hay amenaza cercana
    fdist = 1e9; fdx = fdy = 0.0
    if dmin_d > VISION * 0.55:
        for fid, (fx, fy) in food.items():
            dx, dy = fx - b["x"], fy - b["y"]
            d2 = dx * dx + dy * dy
            if d2 < VISION * VISION and d2 < fdist:
                fdist, fdx, fdy = d2, dx, dy
    if fdist < 1e9:
        d = math.sqrt(fdist) or 1.0
        vx_f, vy_f = fdx / d, fdy / d

    mode = ARB.decide(level(dmin_d), level(dmin_h))[2:]
    b["mode"] = mode
    if mode == "Huir":   vx, vy = vx_d * 2.2, vy_d * 2.2
    elif mode == "Cazar": vx, vy = vx_h * 1.4, vy_h * 1.4
    else:
        vx, vy = vx_f * 0.8, vy_f * 0.8
        if fdist == 1e9:                       # sin comida a la vista: vagar
            vx, vy = math.sin(b["id"] + world_t), math.cos(b["id"] * 2 + world_t)
    n = math.hypot(vx, vy)
    if n < 0.01:
        vx, vy = math.sin(b["id"] + world_t), math.cos(b["id"] + world_t)
        n = 1.0
    b["tx"] = b["x"] + vx / n * 200
    b["ty"] = b["y"] + vy / n * 200

world_t = 0.0
tick_n = 0

def tick(dt):
    global world_t, tick_n
    world_t += dt
    tick_n += 1
    # intención de movimiento
    for c in cells.values():
        if c["dead_t"]:
            continue
        # decaimiento de masa: frena la bola de nieve del que va ganando
        if c["m"] > 100:
            c["m"] -= c["m"] * 0.006 * dt
        if c["m"] > MAX_MASS:
            c["m"] = MAX_MASS
        # cada bot decide 1/5 de los ticks, escalonados: 4 consultas HDC/tick
        # (suficiente: el modo cambia a 12Hz, imperceptible para el ojo)
        if c["kind"] == "bot" and (tick_n + c["id"]) % 5 == 0:
            bot_think(c)
        dx, dy = c["tx"] - c["x"], c["ty"] - c["y"]
        d = math.hypot(dx, dy)
        if d > 4:
            v = speed(c["m"])
            c["x"] += dx / d * min(v * dt, d)
            c["y"] += dy / d * min(v * dt, d)
        c["x"] = min(max(c["x"], 20), W - 20)
        c["y"] = min(max(c["y"], 20), H - 20)
    # comer comida
    for c in cells.values():
        if c["dead_t"]:
            continue
        r = radius(c["m"])
        for fid in list(food):
            fx, fy = food[fid]
            if (fx - c["x"]) ** 2 + (fy - c["y"]) ** 2 < (r + 4) ** 2:
                del food[fid]
                broadcast_queue.append({"t": "food-", "i": fid})
                c["m"] += FOOD_MASS
    # comer células
    lst = [c for c in cells.values() if not c["dead_t"]]
    for a in lst:
        ra = radius(a["m"])
        for o in lst:
            if o is a or o["dead_t"] or a["dead_t"]:
                continue
            if a["m"] > o["m"] * EAT_RATIO:
                dx, dy = o["x"] - a["x"], o["y"] - a["y"]
                if dx * dx + dy * dy < (ra * 0.72) ** 2:
                    o["dead_t"] = world_t
                    a["m"] = min(a["m"] + o["m"] * 0.85, MAX_MASS)
                    broadcast_queue.append({"t": "dead", "i": o["id"],
                                            "by": a["id"]})
                    if o["kind"] == "player":
                        o["respawn_t"] = world_t + 2.0
    # respawns
    for cid in list(cells):
        c = cells[cid]
        if c["dead_t"] and world_t > c["dead_t"] + (c.get("respawn_t", c["dead_t"] + 2.0) - c["dead_t"]):
            pass  # handled below
    for cid in list(cells):
        c = cells[cid]
        if not c["dead_t"]:
            continue
        if c["kind"] == "player" and c.get("respawn_t", 0) >= 1e18 \
                and world_t > c["dead_t"] + 15:
            # jugador desconectado: purgarlo del mundo
            del cells[cid]
            world_meta.pop(cid, None)
            broadcast_queue.append({"t": "leave", "i": cid})
            continue
        if c["kind"] == "player" and world_t >= c.get("respawn_t", 1e18):
            x, y = random.uniform(120, W - 120), random.uniform(120, H - 120)
            c.update(x=x, y=y, tx=x, ty=y, m=PLAYER_START_MASS, dead_t=0.0)
            broadcast_queue.append({"t": "respawn", "i": cid,
                                    "x": round(x), "y": round(y)})
        elif c["kind"] == "bot" and world_t > c["dead_t"] + 3.0:
            del cells[cid]
            meta = world_meta.pop(cid)
            spawn_cell("bot", meta["name"], meta["color"],
                       BOT_START_MASS)
            new_id = next_id[0] - 1
            world_meta[new_id] = meta
            broadcast_queue.append({"t": "join", "i": new_id,
                                    "n": meta["name"], "c": meta["color"],
                                    "x": round(cells[new_id]["x"]),
                                    "y": round(cells[new_id]["y"]),
                                    "m": round(cells[new_id]["m"], 1),
                                    "k": "bot"})
    refil_food()

def snapshot():
    """Estado compacto del mundo para broadcast."""
    out = {"t": "s", "cells": []}
    for c in cells.values():
        out["cells"].append([c["id"], round(c["x"]), round(c["y"]),
                             round(c["m"], 1), 1 if c["dead_t"] else 0,
                             c["mode"] if c["kind"] == "bot" else ""])
    if broadcast_queue:
        out["ev"] = broadcast_queue[:]
        broadcast_queue.clear()
    return out

async def handler(ws):
    pid = None
    try:
        async for raw in ws:
            try:
                msg = json.loads(raw)
            except Exception:
                continue
            if msg.get("t") == "join" and pid is None:
                name = str(msg.get("n", "player"))[:16] or "player"
                color = str(msg.get("c", "#5a7bff"))[:16]
                pid = spawn_cell("player", name, color)
                world_meta[pid] = {"name": name, "color": color}
                clients[ws] = pid
                await ws.send(json.dumps({
                    "t": "init", "id": pid, "w": W, "h": H,
                    "vision": VISION,
                    "food": [[i, round(x), round(y)]
                             for i, (x, y) in food.items()],
                    "meta": {str(cid): [world_meta[cid]["name"],
                                        world_meta[cid]["color"],
                                        cells[cid]["kind"]]
                             for cid in cells},
                }))
                broadcast_queue.append({"t": "join", "i": pid, "n": name,
                                        "c": color, "x": round(cells[pid]["x"]),
                                        "y": round(cells[pid]["y"]),
                                        "m": cells[pid]["m"], "k": "player"})
            elif msg.get("t") == "in" and pid is not None:
                c = cells.get(pid)
                if c and not c["dead_t"]:
                    c["tx"] = min(max(float(msg.get("x", c["tx"])), 0), W)
                    c["ty"] = min(max(float(msg.get("y", c["ty"])), 0), H)
    except Exception:
        pass
    finally:
        clients.pop(ws, None)
        if pid is not None and pid in cells:
            cells[pid]["dead_t"] = world_t  # desconectado = comestible y se limpia
            cells[pid]["respawn_t"] = 1e18
            broadcast_queue.append({"t": "dead", "i": pid, "by": 0})

def reset_world():
    """Mundo nuevo: 20 bots + comida, todo desde cero."""
    global world_t
    cells.clear(); food.clear(); world_meta.clear(); broadcast_queue.clear()
    world_t = 0.0
    for i in range(BOT_N):
        cid = spawn_cell("bot")
        meta = {"name": BOT_NAMES[i % len(BOT_NAMES)],
                "color": f"hsl({(i * 47) % 360},70%,60%)"}
        cells[cid]["name"], cells[cid]["color"] = meta["name"], meta["color"]
        world_meta[cid] = meta
    refil_food()

async def loop():
    dt = 1.0 / FPS
    tps_t0 = time.perf_counter(); tps_n = 0
    empty_since = None
    spent = 0.0
    while True:
        t0 = time.perf_counter()
        if not clients:
            # sin jugadores reales: mundo en pausa (cero CPU), cola limpia;
            # 5 min vacio -> reset total (nace mundo fresco para el proximo)
            broadcast_queue.clear()
            if empty_since is None:
                empty_since = time.time()
            elif time.time() - empty_since > 300:
                print("mundo vacio 5min -> reset", flush=True)
                reset_world()
                empty_since = time.time()
            await asyncio.sleep(0.5)
            continue
        empty_since = None
        tick(dt)
        if clients:
            if tick_n % 2 == 0:   # broadcast 30Hz: mitad de ancho de banda
                msg = json.dumps(snapshot(), separators=(",", ":"))
                bytes_last[0] = len(msg)
                for ws in list(clients):
                    try:
                        await ws.send(msg)
                    except Exception:
                        clients.pop(ws, None)
            # tick impar: los eventos esperan al próximo broadcast (cola intacta)
        else:
            broadcast_queue.clear()
        tps_counter["n"] += 1
        tps_n += 1
        if time.perf_counter() - tps_t0 >= 5:
            print(f"tps={tps_n/5:.1f} spent_ms={spent*1000:.2f} "
                  f"clients={len(clients)} bytes={bytes_last[0]}", flush=True)
            tps_t0 = time.perf_counter(); tps_n = 0
        spent = time.perf_counter() - t0
        deadline = t0 + dt
        await asyncio.sleep(max(0.0, dt - spent))
        while time.perf_counter() < deadline:   # guardia: timers flojos en esta VM
            await asyncio.sleep(0)

INDEX_HTML = open(os.path.join(os.path.dirname(__file__), "index.html"),
                  encoding="utf-8").read()

def process_request(connection, request):
    """HTTP y WS en el mismo puerto: GET / sirve el frontend; el resto es WS.
    Así el preview/proxy usa un solo origen (una cookie de auth basta)."""
    path = request.path.split("?")[0]
    if path in ("/", "/index.html"):
        if "websocket" in request.headers.get("Upgrade", "").lower():
            return connection.respond(400, "Usa /ws para WebSocket\n")
        resp = connection.respond(200, INDEX_HTML)
        resp.headers["Content-Type"] = "text/html; charset=utf-8"
        return resp
    return None  # /ws (o cualquier otro path) sigue el handshake WebSocket

async def main():
    reset_world()
    import websockets
    port = int(os.environ.get("PORT", "4002"))
    async with websockets.serve(handler, "0.0.0.0", port,
                                process_request=process_request,
                                ping_interval=10, ping_timeout=30):
        print(f"Arena -> http://0.0.0.0:{port} + ws  ({BOT_N} bots, {FOOD_N} comida)")
        await loop()

if __name__ == "__main__":
    random.seed(int(time.time()))
    asyncio.run(main())
