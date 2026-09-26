CREATE DATABASE IF NOT EXISTS aura_duelos
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE aura_duelos;

CREATE TABLE IF NOT EXISTS jugadores (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(80) NOT NULL,
    aura_actual INT UNSIGNED NOT NULL DEFAULT 1000,
    aura_max INT UNSIGNED NOT NULL DEFAULT 1000,
    auracoins BIGINT UNSIGNED NOT NULL DEFAULT 0,
    victorias INT UNSIGNED NOT NULL DEFAULT 0,
    derrotas INT UNSIGNED NOT NULL DEFAULT 0,
    UNIQUE KEY uq_jugadores_username (username)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS cartas_meme (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    ataque_aura INT UNSIGNED NOT NULL,
    defensa_cringe INT UNSIGNED NOT NULL,
    rareza_nivel INT UNSIGNED NOT NULL DEFAULT 1,
    efecto_especial VARCHAR(40) NOT NULL DEFAULT 'NINGUNO',
    imagen_url VARCHAR(500) NOT NULL,
    UNIQUE KEY uq_cartas_meme_nombre (nombre)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS mazos_jugador (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    jugador_id INT UNSIGNED NOT NULL,
    carta_id INT UNSIGNED NOT NULL,
    UNIQUE KEY uq_mazo_jugador_carta (jugador_id, carta_id),
    CONSTRAINT fk_mazos_jugador
        FOREIGN KEY (jugador_id) REFERENCES jugadores (id)
        ON DELETE CASCADE,
    CONSTRAINT fk_mazos_carta
        FOREIGN KEY (carta_id) REFERENCES cartas_meme (id)
        ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS duelos_activos (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    retador_id INT UNSIGNED NOT NULL,
    oponente_bot_id INT UNSIGNED NOT NULL,
    aura_retador INT UNSIGNED NOT NULL,
    aura_oponente INT UNSIGNED NOT NULL DEFAULT 1000,
    auracoins_apuesta INT UNSIGNED NOT NULL,
    turno INT UNSIGNED NOT NULL DEFAULT 1,
    estado ENUM(
        'BATALLANDO',
        'VICTORIA_RETADOR',
        'DERROTA_RETADOR'
    ) NOT NULL DEFAULT 'BATALLANDO',
    creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    actualizado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    KEY idx_duelos_retador_estado (retador_id, estado),
    CONSTRAINT fk_duelos_retador
        FOREIGN KEY (retador_id) REFERENCES jugadores (id),
    CONSTRAINT fk_duelos_bot
        FOREIGN KEY (oponente_bot_id) REFERENCES jugadores (id)
) ENGINE=InnoDB;

INSERT INTO jugadores (
    id,
    username,
    aura_actual,
    aura_max,
    auracoins,
    victorias,
    derrotas
) VALUES
    (1, 'AuraFarmer67', 1000, 1000, 2500, 0, 0),
    (2, 'The Rizzler Bot', 1000, 1000, 999999, 0, 0)
ON DUPLICATE KEY UPDATE
    username = VALUES(username),
    aura_max = VALUES(aura_max);

INSERT INTO cartas_meme (
    id,
    nombre,
    ataque_aura,
    defensa_cringe,
    rareza_nivel,
    efecto_especial,
    imagen_url
) VALUES
    (
        1,
        'GigaChad',
        310,
        180,
        67,
        'CRITICO_MEME',
        'https://api.dicebear.com/9.x/adventurer/svg?seed=GigaChad&backgroundColor=ffd5dc'
    ),
    (
        2,
        'Gato Pescador',
        190,
        240,
        23,
        'ROBAR_AURA',
        'https://api.dicebear.com/9.x/bottts-neutral/svg?seed=GatoPescador&backgroundColor=c0aede'
    ),
    (
        3,
        'El Rizzler',
        280,
        140,
        67,
        'ROBAR_AURA',
        'https://api.dicebear.com/9.x/adventurer/svg?seed=Rizzler&backgroundColor=ffdfbf'
    ),
    (
        4,
        'NPC Scripted',
        165,
        300,
        12,
        'ESCUDO_CHILL',
        'https://api.dicebear.com/9.x/bottts-neutral/svg?seed=NPCScripted&backgroundColor=b6e3f4'
    ),
    (
        5,
        'Capybara Chill',
        145,
        360,
        42,
        'ESCUDO_CHILL',
        'https://api.dicebear.com/9.x/adventurer/svg?seed=CapybaraChill&backgroundColor=d1d4f9'
    ),
    (
        6,
        'Skibidi Doge',
        250,
        120,
        31,
        'CRITICO_MEME',
        'https://api.dicebear.com/9.x/bottts-neutral/svg?seed=SkibidiDoge&backgroundColor=ffdfbf'
    )
ON DUPLICATE KEY UPDATE
    ataque_aura = VALUES(ataque_aura),
    defensa_cringe = VALUES(defensa_cringe),
    rareza_nivel = VALUES(rareza_nivel),
    efecto_especial = VALUES(efecto_especial),
    imagen_url = VALUES(imagen_url);

INSERT IGNORE INTO mazos_jugador (jugador_id, carta_id)
SELECT 1, id FROM cartas_meme WHERE id BETWEEN 1 AND 6;

INSERT IGNORE INTO mazos_jugador (jugador_id, carta_id)
SELECT 2, id FROM cartas_meme WHERE id BETWEEN 1 AND 6;

DROP PROCEDURE IF EXISTS sp_iniciar_duelo_meme;
DROP PROCEDURE IF EXISTS sp_jugar_carta_turno;

DELIMITER $$

CREATE PROCEDURE sp_iniciar_duelo_meme(
    IN p_jugador_id INT,
    IN p_apuesta_auracoins INT
)
BEGIN
    DECLARE v_jugador_existe INT DEFAULT 0;
    DECLARE v_bot_id INT UNSIGNED;
    DECLARE v_saldo BIGINT UNSIGNED;
    DECLARE v_aura_actual INT UNSIGNED;
    DECLARE v_aura_max INT UNSIGNED;
    DECLARE v_duelo_id INT UNSIGNED;

    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;

    IF p_apuesta_auracoins IS NULL OR p_apuesta_auracoins <= 0 THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'La apuesta debe ser mayor que cero';
    END IF;

    START TRANSACTION;

    SELECT COUNT(*)
    INTO v_jugador_existe
    FROM jugadores
    WHERE id = p_jugador_id;

    IF v_jugador_existe = 0 THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Jugador no encontrado';
    END IF;

    SELECT auracoins, aura_actual, aura_max
    INTO v_saldo, v_aura_actual, v_aura_max
    FROM jugadores
    WHERE id = p_jugador_id
    FOR UPDATE;

    IF v_aura_actual = 0 THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Tu Aura permanente está en cero';
    END IF;

    IF v_saldo < p_apuesta_auracoins THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'AuraCoins insuficientes para esta apuesta';
    END IF;

    SELECT id
    INTO v_bot_id
    FROM jugadores
    WHERE username = 'The Rizzler Bot'
    LIMIT 1;

    UPDATE jugadores
    SET auracoins = auracoins - p_apuesta_auracoins
    WHERE id = p_jugador_id;

    INSERT INTO duelos_activos (
        retador_id,
        oponente_bot_id,
        aura_retador,
        aura_oponente,
        auracoins_apuesta,
        turno,
        estado
    ) VALUES (
        p_jugador_id,
        v_bot_id,
        LEAST(v_aura_actual, v_aura_max),
        1000,
        p_apuesta_auracoins,
        1,
        'BATALLANDO'
    );

    SET v_duelo_id = LAST_INSERT_ID();

    COMMIT;

    SELECT
        v_duelo_id AS duelo_id,
        'BATALLANDO' AS estado,
        LEAST(v_aura_actual, v_aura_max) AS aura_retador,
        1000 AS aura_oponente,
        p_apuesta_auracoins AS auracoins_apuesta,
        v_saldo - p_apuesta_auracoins AS auracoins_saldo;
END$$

CREATE PROCEDURE sp_jugar_carta_turno(
    IN p_duelo_id INT,
    IN p_carta_id INT,
    IN p_turno_esperado INT
)
BEGIN
    DECLARE v_duelo_existe INT DEFAULT 0;
    DECLARE v_carta_pertenece INT DEFAULT 0;
    DECLARE v_retador_id INT UNSIGNED;
    DECLARE v_bot_id INT UNSIGNED;
    DECLARE v_aura_retador INT;
    DECLARE v_aura_oponente INT;
    DECLARE v_aura_max_retador INT;
    DECLARE v_apuesta INT UNSIGNED;
    DECLARE v_turno INT UNSIGNED;
    DECLARE v_estado VARCHAR(30);
    DECLARE v_ataque INT;
    DECLARE v_defensa INT;
    DECLARE v_rareza INT;
    DECLARE v_efecto VARCHAR(40);
    DECLARE v_carta_nombre VARCHAR(100);
    DECLARE v_bot_ataque INT;
    DECLARE v_bot_defensa INT;
    DECLARE v_bot_rareza INT;
    DECLARE v_bot_efecto VARCHAR(40);
    DECLARE v_bot_carta_nombre VARCHAR(100);
    DECLARE v_dano_realizado INT DEFAULT 0;
    DECLARE v_dano_recibido INT DEFAULT 0;
    DECLARE v_aura_robada INT DEFAULT 0;
    DECLARE v_auracoins_movimiento INT DEFAULT 0;
    DECLARE v_saldo BIGINT UNSIGNED DEFAULT 0;
    DECLARE v_frase VARCHAR(500);

    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;

    START TRANSACTION;

    SELECT COUNT(*)
    INTO v_duelo_existe
    FROM duelos_activos
    WHERE id = p_duelo_id;

    IF v_duelo_existe = 0 THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Duelo no encontrado';
    END IF;

    SELECT
        retador_id,
        oponente_bot_id,
        aura_retador,
        aura_oponente,
        auracoins_apuesta,
        turno,
        estado
    INTO
        v_retador_id,
        v_bot_id,
        v_aura_retador,
        v_aura_oponente,
        v_apuesta,
        v_turno,
        v_estado
    FROM duelos_activos
    WHERE id = p_duelo_id
    FOR UPDATE;

    IF v_estado <> 'BATALLANDO' THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Este duelo ya terminó';
    END IF;

    IF p_turno_esperado <> v_turno THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Este turno ya fue procesado';
    END IF;

    SELECT COUNT(*)
    INTO v_carta_pertenece
    FROM mazos_jugador
    WHERE jugador_id = v_retador_id
      AND carta_id = p_carta_id;

    IF v_carta_pertenece = 0 THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'La carta no pertenece al mazo del retador';
    END IF;

    SELECT
        nombre,
        ataque_aura,
        defensa_cringe,
        rareza_nivel,
        efecto_especial
    INTO
        v_carta_nombre,
        v_ataque,
        v_defensa,
        v_rareza,
        v_efecto
    FROM cartas_meme
    WHERE id = p_carta_id;

    SELECT
        c.nombre,
        c.ataque_aura,
        c.defensa_cringe,
        c.rareza_nivel,
        c.efecto_especial
    INTO
        v_bot_carta_nombre,
        v_bot_ataque,
        v_bot_defensa,
        v_bot_rareza,
        v_bot_efecto
    FROM mazos_jugador AS m
    INNER JOIN cartas_meme AS c ON c.id = m.carta_id
    WHERE m.jugador_id = v_bot_id
    ORDER BY RAND()
    LIMIT 1;

    SELECT aura_max
    INTO v_aura_max_retador
    FROM jugadores
    WHERE id = v_retador_id
    FOR UPDATE;

    SET v_dano_realizado = GREATEST(
        50,
        v_ataque - FLOOR(v_bot_defensa / 2)
    );

    IF v_rareza = 67 THEN
        SET v_dano_realizado = FLOOR(v_dano_realizado * 1.50);
    END IF;

    IF v_efecto = 'CRITICO_MEME' THEN
        SET v_dano_realizado = FLOOR(v_dano_realizado * 1.25);
    END IF;

    SET v_aura_oponente = GREATEST(0, v_aura_oponente - v_dano_realizado);

    IF v_efecto = 'ROBAR_AURA' THEN
        SET v_aura_robada = LEAST(
            FLOOR(v_dano_realizado * 0.25),
            GREATEST(0, v_aura_max_retador - v_aura_retador)
        );
        SET v_aura_retador = LEAST(
            v_aura_max_retador,
            v_aura_retador + v_aura_robada
        );
    END IF;

    IF v_aura_oponente = 0 THEN
        SET v_estado = 'VICTORIA_RETADOR';
        SET v_auracoins_movimiento = v_apuesta * 2;
        SET v_frase = CONCAT(
            v_carta_nombre,
            ' mandó al bot directo al compilador del cringe. Aura infinita desbloqueada.'
        );

        UPDATE jugadores
        SET
            auracoins = auracoins + v_auracoins_movimiento,
            victorias = victorias + 1
        WHERE id = v_retador_id;
    ELSE
        SET v_dano_recibido = GREATEST(
            50,
            v_bot_ataque - FLOOR(v_defensa / 2)
        );

        IF v_bot_rareza = 67 THEN
            SET v_dano_recibido = FLOOR(v_dano_recibido * 1.50);
        END IF;

        IF v_bot_efecto = 'CRITICO_MEME' THEN
            SET v_dano_recibido = FLOOR(v_dano_recibido * 1.25);
        END IF;

        IF v_efecto = 'ESCUDO_CHILL' THEN
            SET v_dano_recibido = FLOOR(v_dano_recibido * 0.50);
        END IF;

        SET v_aura_retador = GREATEST(0, v_aura_retador - v_dano_recibido);

        IF v_bot_efecto = 'ROBAR_AURA' THEN
            SET v_aura_oponente = LEAST(
                1000,
                v_aura_oponente + FLOOR(v_dano_recibido * 0.25)
            );
        END IF;

        IF v_aura_retador = 0 THEN
            SET v_estado = 'DERROTA_RETADOR';
            SET v_auracoins_movimiento = -v_apuesta;
            SET v_frase = CONCAT(
                v_bot_carta_nombre,
                ' te dejó sin Aura. El chat escribió F y el bot cobró la apuesta.'
            );

            UPDATE jugadores
            SET
                aura_actual = GREATEST(0, aura_actual - 100),
                derrotas = derrotas + 1
            WHERE id = v_retador_id;
        ELSE
            SET v_frase = CONCAT(
                v_carta_nombre,
                ' hizo ',
                v_dano_realizado,
                ' de daño, pero ',
                v_bot_carta_nombre,
                ' respondió con ',
                v_dano_recibido,
                '. El mogging continúa.'
            );
        END IF;
    END IF;

    UPDATE duelos_activos
    SET
        aura_retador = v_aura_retador,
        aura_oponente = v_aura_oponente,
        turno = v_turno + 1,
        estado = v_estado
    WHERE id = p_duelo_id;

    SELECT auracoins
    INTO v_saldo
    FROM jugadores
    WHERE id = v_retador_id;

    COMMIT;

    SELECT JSON_OBJECT(
        'duelo_id', p_duelo_id,
        'turno', v_turno,
        'carta_jugada', v_carta_nombre,
        'carta_bot', v_bot_carta_nombre,
        'dano_realizado', v_dano_realizado,
        'dano_recibido', v_dano_recibido,
        'aura_robada', v_aura_robada,
        'aura_retador', v_aura_retador,
        'aura_oponente', v_aura_oponente,
        'auracoins_movimiento', v_auracoins_movimiento,
        'auracoins_saldo', v_saldo,
        'efecto_jugador', v_efecto,
        'efecto_bot', v_bot_efecto,
        'frase', v_frase,
        'estado', v_estado
    ) AS resultado_json;
END$$

DELIMITER ;
