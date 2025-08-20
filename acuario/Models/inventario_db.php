<?php
// Models/inventario_db.php
require_once 'Models/conexion.php';

class InventarioDB
{
    /** Cambia por tu secuencia real de productos, o deja '' si tienes trigger en la PK */
    private const SEQ_PRODUCTO = 'SEQ_PRODUCTO_ID';

    private function conn() {
        $cx = new Conexion();
        return $cx->getConexion(); // OCI8
    }

    private static function nv($v) {
        return ($v === '' || !isset($v)) ? null : $v;
    }

    private function nextProductoId(): ?int
    {
        if (self::SEQ_PRODUCTO === '') return null;
        $cn  = $this->conn();
        $st  = oci_parse($cn, "SELECT " . self::SEQ_PRODUCTO . ".NEXTVAL AS ID FROM DUAL");
        if (!@oci_execute($st)) { oci_free_statement($st); return null; }
        $row = oci_fetch_assoc($st);
        oci_free_statement($st);
        return $row && isset($row['ID']) ? (int)$row['ID'] : null;
    }

    /* ================= Lecturas ================= */

    /** Lista productos junto con categoría e inventario (si existe) */
    public function listarInventario(): array
    {
        $sql = "SELECT p.ID_PRODUCTO,
                       p.NOMBRE        AS NOMBRE_PRODUCTO,
                       c.NOMBRE_CATEGORIA,
                       p.ID_CATEGORIA,
                       p.ID_UNIDAD_MEDIDA,
                       p.ID_ESTADO     AS ID_ESTADO_PROD,
                       i.CANTIDAD,
                       TO_CHAR(i.FECHA_INGRESO,'YYYY-MM-DD') AS FECHA_INGRESO,
                       i.ID_ESTADO     AS ID_ESTADO_INV
                  FROM FIDE_PRODUCTO_TB p
             LEFT JOIN FIDE_CATEGORIA_TB c ON c.ID_CATEGORIA = p.ID_CATEGORIA
             LEFT JOIN FIDE_INVENTARIO_TB i ON i.ID_PRODUCTO = p.ID_PRODUCTO
              ORDER BY p.ID_PRODUCTO";
        $cn = $this->conn();
        $st = oci_parse($cn, $sql);
        @oci_execute($st);
        $out = [];
        while ($r = oci_fetch_assoc($st)) $out[] = $r;
        oci_free_statement($st);
        return $out;
    }

    /** Obtiene un producto + inventario por ID */
    public function obtenerPorId(int $id): ?array
    {
        $sql = "SELECT p.ID_PRODUCTO,
                       p.NOMBRE,
                       p.ID_CATEGORIA,
                       p.ID_UNIDAD_MEDIDA,
                       p.ID_ESTADO     AS ID_ESTADO_PROD,
                       i.CANTIDAD,
                       TO_CHAR(i.FECHA_INGRESO,'YYYY-MM-DD') AS FECHA_INGRESO,
                       i.ID_ESTADO     AS ID_ESTADO_INV
                  FROM FIDE_PRODUCTO_TB p
             LEFT JOIN FIDE_INVENTARIO_TB i ON i.ID_PRODUCTO = p.ID_PRODUCTO
                 WHERE p.ID_PRODUCTO = :id";
        $cn = $this->conn();
        $st = oci_parse($cn, $sql);
        oci_bind_by_name($st, ':id', $id);
        $ok = @oci_execute($st);
        if (!$ok) { oci_free_statement($st); return null; }
        $row = oci_fetch_assoc($st) ?: null;
        oci_free_statement($st);
        return $row;
    }

    /* ================= Escrituras (SP del paquete) ================= */

    /** Inserta PRODUCTO y su fila en INVENTARIO */
    public function insertar(
        string $nombre,
        $id_categoria,
        $id_unidad_medida,
        $id_estado_prod,
        $cantidad,
        ?string $fecha_ingreso,     // 'YYYY-MM-DD'
        $id_estado_inv
    ): bool {
        $cn = $this->conn();

        // 1) Insertar producto
        $idProd = $this->nextProductoId(); // si usas trigger, deja SEQ_PRODUCTO=''
        if (self::SEQ_PRODUCTO !== '' && $idProd === null) return false;

        $plProd = "BEGIN
          FIDE_PROYECTO_FINAL_PCK.FIDE_INSERTAR_PRODUCTO_SP(
            :p_id, :p_nombre, :p_cat, :p_um, :p_estado
          );
        END;";
        $sp1 = oci_parse($cn, $plProd);
        oci_bind_by_name($sp1, ':p_id',     $idProd);
        oci_bind_by_name($sp1, ':p_nombre', $nombre);
        $id_categoria     = self::nv($id_categoria);     oci_bind_by_name($sp1, ':p_cat',    $id_categoria);
        $id_unidad_medida = self::nv($id_unidad_medida); oci_bind_by_name($sp1, ':p_um',     $id_unidad_medida);
        $id_estado_prod   = self::nv($id_estado_prod);   oci_bind_by_name($sp1, ':p_estado', $id_estado_prod);

        $ok1 = @oci_execute($sp1, OCI_NO_AUTO_COMMIT);
        oci_free_statement($sp1);
        if (!$ok1) { oci_rollback($cn); return false; }

        // 2) Insertar inventario (PK es ID_PRODUCTO en tu DDL)
        $plInv = "BEGIN
          FIDE_PROYECTO_FINAL_PCK.FIDE_INSERTAR_INVENTARIO_SP(
            :p_cant,
            TO_DATE(:p_fing,'YYYY-MM-DD'),
            :p_idprod,
            :p_estado
          );
        END;";
        $sp2 = oci_parse($cn, $plInv);
        $cantidad     = self::nv($cantidad);
        $fecha_ingreso= self::nv($fecha_ingreso);
        $id_estado_inv= self::nv($id_estado_inv);
        oci_bind_by_name($sp2, ':p_cant',   $cantidad);
        oci_bind_by_name($sp2, ':p_fing',   $fecha_ingreso);
        oci_bind_by_name($sp2, ':p_idprod', $idProd);
        oci_bind_by_name($sp2, ':p_estado', $id_estado_inv);

        $ok2 = @oci_execute($sp2, OCI_NO_AUTO_COMMIT);
        oci_free_statement($sp2);

        if ($ok2) oci_commit($cn); else oci_rollback($cn);
        return $ok2;
    }

    /** Actualiza PRODUCTO e INVENTARIO */
    public function actualizar(
        int $id_prod,
        string $nombre,
        $id_categoria,
        $id_unidad_medida,
        $id_estado_prod,
        $cantidad,
        ?string $fecha_ingreso,
        $id_estado_inv
    ): bool {
        $cn = $this->conn();

        // 1) Producto
        $plProd = "BEGIN
          FIDE_PROYECTO_FINAL_PCK.FIDE_MODIFICAR_PRODUCTO_SP(
            :p_id, :p_nombre, :p_cat, :p_um, :p_estado
          );
        END;";
        $sp1 = oci_parse($cn, $plProd);
        oci_bind_by_name($sp1, ':p_id',     $id_prod);
        oci_bind_by_name($sp1, ':p_nombre', $nombre);
        $id_categoria     = self::nv($id_categoria);     oci_bind_by_name($sp1, ':p_cat',    $id_categoria);
        $id_unidad_medida = self::nv($id_unidad_medida); oci_bind_by_name($sp1, ':p_um',     $id_unidad_medida);
        $id_estado_prod   = self::nv($id_estado_prod);   oci_bind_by_name($sp1, ':p_estado', $id_estado_prod);

        $ok1 = @oci_execute($sp1, OCI_NO_AUTO_COMMIT);
        oci_free_statement($sp1);
        if (!$ok1) { oci_rollback($cn); return false; }

        // 2) Inventario (firma: cant, fecha, id_producto, id_estado)
        $plInv = "BEGIN
          FIDE_PROYECTO_FINAL_PCK.FIDE_MODIFICAR_INVENTARIO_SP(
            :p_cant,
            TO_DATE(:p_fing,'YYYY-MM-DD'),
            :p_idprod,
            :p_estado
          );
        END;";
        $sp2 = oci_parse($cn, $plInv);
        $cantidad     = self::nv($cantidad);
        $fecha_ingreso= self::nv($fecha_ingreso);
        $id_estado_inv= self::nv($id_estado_inv);
        oci_bind_by_name($sp2, ':p_cant',   $cantidad);
        oci_bind_by_name($sp2, ':p_fing',   $fecha_ingreso);
        oci_bind_by_name($sp2, ':p_idprod', $id_prod);
        oci_bind_by_name($sp2, ':p_estado', $id_estado_inv);

        $ok2 = @oci_execute($sp2, OCI_NO_AUTO_COMMIT);
        oci_free_statement($sp2);

        if ($ok2) oci_commit($cn); else oci_rollback($cn);
        return $ok2;
    }

    /** Elimina (inactiva) ambas entidades. Maneja firmas alternativas del SP de inventario. */
    public function eliminar(int $id_prod): bool
    {
        $cn = $this->conn();

        // Intento 1: inventario por ID_PRODUCTO (firma de tu DDL)
        $okInv = false;
        $pl1 = "BEGIN FIDE_PROYECTO_FINAL_PCK.FIDE_ELIMINAR_INVENTARIO_SP(:pid); END;";
        $st1 = oci_parse($cn, $pl1);
        oci_bind_by_name($st1, ':pid', $id_prod);
        $okInv = @oci_execute($st1, OCI_NO_AUTO_COMMIT);
        oci_free_statement($st1);

        // Si falló por firma, ignoramos y seguimos (algunos scripts usan ID_INVENTARIO)
        if (!$okInv) { oci_rollback($cn); }

        // Producto (inactiva)
        $pl2 = "BEGIN FIDE_PROYECTO_FINAL_PCK.FIDE_ELIMINAR_PRODUCTO_SP(:pid); END;";
        $st2 = oci_parse($cn, $pl2);
        oci_bind_by_name($st2, ':pid', $id_prod);
        $okProd = @oci_execute($st2, OCI_NO_AUTO_COMMIT);
        oci_free_statement($st2);

        if ($okProd) oci_commit($cn); else oci_rollback($cn);
        return $okProd;
    }

    /* ================= Catálogos ================= */

    public function listarCategorias(): array
    {
        $cn = $this->conn();
        $st = oci_parse($cn, "SELECT ID_CATEGORIA AS ID, NOMBRE_CATEGORIA AS NOMBRE
                                FROM FIDE_CATEGORIA_TB
                            ORDER BY NOMBRE_CATEGORIA");
        @oci_execute($st);
        $out = [];
        while ($r = oci_fetch_assoc($st)) $out[] = $r;
        oci_free_statement($st);
        return $out;
    }

    public function listarUnidades(): array
    {
        $cn = $this->conn();
        $st = oci_parse($cn, "SELECT ID_UNIDAD_MEDIDA AS ID, NOMBRE_UNIDAD_MEDIDA AS NOMBRE
                                FROM FIDE_UNIDAD_MEDIDA_TB
                            ORDER BY NOMBRE_UNIDAD_MEDIDA");
        @oci_execute($st);
        $out = [];
        while ($r = oci_fetch_assoc($st)) $out[] = $r;
        oci_free_statement($st);
        return $out;
    }

    public function listarEstados(): array
    {
        $cn = $this->conn();
        $st = oci_parse($cn, "SELECT ID_ESTADO AS ID, NOMBRE_ESTADO AS NOMBRE
                                FROM FIDE_ESTADOS_TB
                            ORDER BY NOMBRE_ESTADO");
        @oci_execute($st);
        $out = [];
        while ($r = oci_fetch_assoc($st)) $out[] = $r;
        oci_free_statement($st);
        return $out;
    }
}
