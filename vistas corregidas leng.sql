CREATE OR REPLACE VIEW FIDE_ANIMAL_V AS
SELECT a.id_animal,
       a.nombre_animal,
       a.fecha_ingreso,
       a.edad,
       a.peso,
       a.observacion,
       a.id_genero,
       g.genero        AS genero_nombre,
       a.id_tipo,
       t.nombre_tipo   AS tipo_nombre,
       a.id_habitat,
       h.nombre_habitat AS habitat_nombre,
       a.id_estado
FROM FIDE_ANIMAL_TB a
LEFT JOIN FIDE_GENERO_TB  g ON g.id_genero  = a.id_genero
LEFT JOIN FIDE_TIPO_TB    t ON t.id_tipo    = a.id_tipo
LEFT JOIN FIDE_HABITAT_TB h ON h.id_habitat = a.id_habitat
WHERE a.id_estado = 1;



CREATE OR REPLACE VIEW FIDE_ESTADO_V AS
SELECT e.id_estado AS id, e.nombre_estado AS nombre, e.descripcion_estado
FROM fide_estados_tb e;

CREATE OR REPLACE VIEW FIDE_GENERO_V AS
SELECT g.id_genero AS id, g.genero AS nombre
FROM fide_genero_tb g
WHERE g.id_estado = 1;

CREATE OR REPLACE VIEW FIDE_TIPO_V AS
SELECT t.id_tipo AS id, t.nombre_tipo AS nombre, t.descripcion_tipo
FROM fide_tipo_tb t
WHERE t.id_estado = 1;

CREATE OR REPLACE VIEW FIDE_HABITAT_V AS
SELECT h.id_habitat AS id, h.nombre_habitat AS nombre, h.descripcion_habitat
FROM fide_habitat_tb h
WHERE h.id_estado = 1;

CREATE OR REPLACE VIEW FIDE_RUTINA_V AS
SELECT r.id_rutina AS id, r.nombre_rutina AS nombre
FROM fide_rutina_tb r
WHERE r.id_estado = 1;

CREATE OR REPLACE VIEW FIDE_MARCA_ALIMENTO_V AS
SELECT m.id_marca_alimento AS id, m.nombre AS nombre, m.descripcion
FROM fide_marca_alimento_tb m
WHERE m.id_estado = 1;

/* --------- Alimentación --------- */
CREATE OR REPLACE VIEW FIDE_ALIMENTACION_V AS
SELECT a.cantidad,
       a.horario,
       a.frecuencia,
       a.id_marca_alimento,
       m.nombre  AS marca_nombre,
       a.id_rutina,
       r.nombre_rutina,
       a.id_estado
FROM fide_alimentacion_tb a
LEFT JOIN fide_marca_alimento_tb m ON m.id_marca_alimento = a.id_marca_alimento
LEFT JOIN fide_rutina_tb r         ON r.id_rutina         = a.id_rutina
WHERE a.id_estado = 1;

/* --------- Roles / Usuarios / Horarios --------- */
CREATE OR REPLACE VIEW FIDE_ROL_V AS
SELECT r.id_rol AS id, r.nombre_rol AS nombre, r.descripcion
FROM fide_rol_tb r
WHERE r.id_estado = 1;

-- Si tu FIDE_USUARIO_TB NO tiene ID_DIRECCION, quita esa columna del SELECT
CREATE OR REPLACE VIEW FIDE_USUARIO_V AS
SELECT u.id_usuario,
       u.nombre,
       u.fecha_registro,
       u.telefono,
       u.correo,
       u.id_rol,
       r.nombre_rol,
       /* u.id_direccion */ NULL AS id_direccion,
       u.id_estado
FROM fide_usuario_tb u
LEFT JOIN fide_rol_tb r ON r.id_rol = u.id_rol
WHERE u.id_estado = 1;

CREATE OR REPLACE VIEW FIDE_HORARIO_V AS
SELECT h.id_horario,
       h.dia,
       h.hora_inicio,
       h.hora_final,
       h.id_usuario,
       u.nombre AS usuario_nombre,
       h.id_estado
FROM fide_horario_tb h
LEFT JOIN fide_usuario_tb u ON u.id_usuario = h.id_usuario
WHERE h.id_estado = 1;

/* --------- Geografía / Dirección --------- */
CREATE OR REPLACE VIEW FIDE_PAIS_V AS
SELECT p.id_pais AS id, p.nombre_pais AS nombre
FROM fide_pais_tb p
WHERE p.id_estado = 1;

CREATE OR REPLACE VIEW FIDE_PROVINCIA_V AS
SELECT p.id_provincia AS id, p.nombre_provincia AS nombre
FROM fide_provincia_tb p
WHERE p.id_estado = 1;

CREATE OR REPLACE VIEW FIDE_CANTON_V AS
SELECT c.id_canton AS id, c.nombre_canton AS nombre
FROM fide_canton_tb c
WHERE c.id_estado = 1;

CREATE OR REPLACE VIEW FIDE_DISTRITO_V AS
SELECT d.id_distrito AS id, d.nombre_distrito AS nombre
FROM fide_distrito_tb d
WHERE d.id_estado = 1;

CREATE OR REPLACE VIEW FIDE_DIRECCION_V AS
SELECT d.id_direccion,
       d.detalle_direccion,
       d.id_distrito,
       d.id_canton,
       d.id_provincia,
       d.id_pais,
       d.id_estado
FROM fide_direccion_tb d
WHERE d.id_estado = 1;

/* --------- Empresa / Proveedores --------- */
CREATE OR REPLACE VIEW FIDE_EMPRESA_V AS
SELECT e.id_empresa,
       e.nombre_empresa,
       e.telefono,
       e.correo,
       e.id_direccion,
       e.id_estado
FROM fide_empresa_tb e
WHERE e.id_estado = 1;

/* --------- Productos / Inventario --------- */
CREATE OR REPLACE VIEW FIDE_CATEGORIA_V AS
SELECT c.id_categoria AS id, c.nombre_categoria AS nombre
FROM fide_categoria_tb c
WHERE c.id_estado = 1;

CREATE OR REPLACE VIEW FIDE_UNIDAD_MEDIDA_V AS
SELECT u.id_unidad_medida AS id, u.nombre_unidad_medida AS nombre
FROM fide_unidad_medida_tb u
WHERE u.id_estado = 1;

CREATE OR REPLACE VIEW FIDE_PRODUCTO_V AS
SELECT p.id_producto,
       p.nombre,
       p.id_categoria,
       c.nombre_categoria,
       p.id_unidad_medida,
       um.nombre_unidad_medida,
       p.id_estado
FROM fide_producto_tb p
LEFT JOIN fide_categoria_tb     c  ON c.id_categoria      = p.id_categoria
LEFT JOIN fide_unidad_medida_tb um ON um.id_unidad_medida = p.id_unidad_medida
WHERE p.id_estado = 1;

CREATE OR REPLACE VIEW FIDE_INVENTARIO_V AS
SELECT i.cantidad,
       i.fecha_ingreso,
       i.id_producto,
       p.nombre AS producto_nombre,
       i.id_estado
FROM fide_inventario_tb i
LEFT JOIN fide_producto_tb p ON p.id_producto = i.id_producto
WHERE i.id_estado = 1;

/* --------- Facturación --------- */
CREATE OR REPLACE VIEW FIDE_METODO_PAGO_V AS
SELECT m.id_metodo_pago AS id, m.nombre_metodo_pago AS nombre
FROM fide_metodo_pago_tb m
WHERE m.id_estado = 1;

CREATE OR REPLACE VIEW FIDE_FACTURA_V AS
SELECT f.id_factura,
       f.fecha_registro,
       f.monto_total,
       f.subtotal,
       f.iva,
       f.descuento,
       f.id_usuario,
       u.nombre             AS usuario_nombre,
       f.id_metodo_pago,
       mp.nombre_metodo_pago,
       f.id_estado
FROM fide_factura_tb f
LEFT JOIN fide_usuario_tb u   ON u.id_usuario     = f.id_usuario
LEFT JOIN fide_metodo_pago_tb mp ON mp.id_metodo_pago = f.id_metodo_pago
WHERE f.id_estado = 1;

CREATE OR REPLACE VIEW FIDE_DETALLE_FACTURA_V AS
SELECT df.cantidad,
       df.precio_unitario,
       df.total,
       df.id_factura,
       df.id_producto,
       p.nombre AS producto_nombre,
       df.id_estado
FROM fide_detalle_factura_tb df
LEFT JOIN fide_producto_tb p ON p.id_producto = df.id_producto
WHERE df.id_estado = 1;