<?php

require_once 'Models/conexion.php';

class InventarioDB
{
    private const SEQ_PRODUCTO = 'ID_PRODUCTO_SEQ';

    private function conn() {
        $cx = new Conexion();
        return $cx->getConexion(); 
    }

    private static function nv($v) {
        return ($v === '' || !isset($v)) ? null : $v;
    }

    private function nextProductoId(): ?int
    {
        if (self::SEQ_PRODUCTO === '') return null;
        $cn  = $this->conn();
        $st  = oci_parse($cn, "SELECT " . self::SEQ_PRODUCTO . ".NEXTVAL AS ID FROM DUAL");
        if (!oci_execute($st)) { 
            $e = oci_error($st); error_log('[nextProductoId] '.$e['message'] ?? 'Error'); 
            oci_free_statement($st); 
            return null; 
        }
        $row = oci_fetch_assoc($st);
        oci_free_statement($st);
        return $row && isset($row['ID']) ? (int)$row['ID'] : null;
    }

    /* ================= Lecturas (VISTA) ================= */

    public function listarInventario(): array
{
    $sql = "SELECT
                ID_PRODUCTO,
                NOMBRE AS NOMBRE_PRODUCTO,
                NOMBRE_CATEGORIA,
                NOMBRE_UNIDAD_MEDIDA,
                CANTIDAD,
                -- La fecha de ingreso no existe en la vista, se omite o se maneja de otra forma.
                ID_ESTADO,
                NOMBRE_ESTADO
            FROM FIDE_PRODUCTO_V
            ORDER BY ID_PRODUCTO";
    $cn = $this->conn();
    $st = oci_parse($cn, $sql);
    $ok = oci_execute($st);
    $out = [];
    if ($ok) {
        while ($r = oci_fetch_assoc($st)) $out[] = $r; // claves en MAYÚSCULAS
    } else {
        $e = oci_error($st); error_log('[listarInventario] '.$e['message'] ?? 'Error');
    }
    oci_free_statement($st);
    return $out;
}

    public function obtenerPorId(int $id): ?array
    {
        $sql = "SELECT
                  ID_PRODUCTO,
                  PRODUCTO_NOMBRE         AS NOMBRE_PRODUCTO,
                  CATEGORIA_NOMBRE        AS NOMBRE_CATEGORIA,
                  UNIDAD_MEDIDA_NOMBRE    AS NOMBRE_UNIDAD_MEDIDA,
                  CANTIDAD,
                  TO_CHAR(FECHA_INGRESO,'YYYY-MM-DD') AS FECHA_INGRESO,
                  ID_ESTADO_PRODUCTO,
                  ESTADO_PRODUCTO,
                  ID_ESTADO_INVENTARIO,
                  ESTADO_INVENTARIO
                FROM FIDE_INVENTARIO_V
               WHERE ID_PRODUCTO = :id";
        $cn = $this->conn();
        $st = oci_parse($cn, $sql);
        oci_bind_by_name($st, ':id', $id);
        $ok = oci_execute($st);
        if (!$ok) { 
            $e = oci_error($st); error_log('[obtenerPorId] '.$e['message'] ?? 'Error');
            oci_free_statement($st); 
            return null; 
        }
        $row = oci_fetch_assoc($st) ?: null;
        oci_free_statement($st);
        return $row;
    }

        string $nombre,
        $id_categoria,
        $id_unidad_medida,
        $id_estado_prod,
        $cantidad,
        ?string $fecha_ingreso,     // 'YYYY-MM-DD'
        $id_estado_inv
    ): bool {
        $cn = $this->conn();

    
        $idProd = $this->nextProductoId();
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

        $ok1 = oci_execute($sp1, OCI_NO_AUTO_COMMIT);
        oci_free_statement($sp1);
        if (!$ok1) { oci_rollback($cn); return false; }

    
        $plInv = "BEGIN
          FIDE_PROYECTO_FINAL_PCK.FIDE_INSERTAR_INVENTARIO_SP(
            :p_cant,
            TO_DATE(:p_fing,'YYYY-MM-DD'),
            :p_idprod,
            :p_estado
          );
        END;";
        $sp2 = oci_parse($cn, $plInv);
        $cantidad      = self::nv($cantidad);
        $fecha_ingreso = self::nv($fecha_ingreso);
        $id_estado_inv = self::nv($id_estado_inv);
        oci_bind_by_name($sp2, ':p_cant',   $cantidad);
        oci_bind_by_name($sp2, ':p_fing',   $fecha_ingreso);
        oci_bind_by_name($sp2, ':p_idprod', $idProd);
        oci_bind_by_name($sp2, ':p_estado', $id_estado_inv);

        $ok2 = oci_execute($sp2, OCI_NO_AUTO_COMMIT);
        oci_free_statement($sp2);

        if ($ok2) oci_commit($cn); else oci_rollback($cn);
        return $ok2;
    }

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

        $ok1 = oci_execute($sp1, OCI_NO_AUTO_COMMIT);
        oci_free_statement($sp1);
        if (!$ok1) { oci_rollback($cn); return false; }

        
        $plInv = "BEGIN
          FIDE_PROYECTO_FINAL_PCK.FIDE_MODIFICAR_INVENTARIO_SP(
            :p_cant,
            TO_DATE(:p_fing,'YYYY-MM-DD'),
            :p_idprod,
            :p_estado
          );
        END;";
        $sp2 = oci_parse($cn, $plInv);
        $cantidad      = self::nv($cantidad);
        $fecha_ingreso = self::nv($fecha_ingreso);
        $id_estado_inv = self::nv($id_estado_inv);
        oci_bind_by_name($sp2, ':p_cant',   $cantidad);
        oci_bind_by_name($sp2, ':p_fing',   $fecha_ingreso);
        oci_bind_by_name($sp2, ':p_idprod', $id_prod);
        oci_bind_by_name($sp2, ':p_estado', $id_estado_inv);

        $ok2 = oci_execute($sp2, OCI_NO_AUTO_COMMIT);
        oci_free_statement($sp2);

        if ($ok2) oci_commit($cn); else oci_rollback($cn);
        return $ok2;
    }


    public function eliminar(int $id_prod): bool
    {
        $cn = $this->conn();

        $pl1 = "BEGIN FIDE_PROYECTO_FINAL_PCK.FIDE_ELIMINAR_INVENTARIO_SP(:pid); END;";
        $st1 = oci_parse($cn, $pl1);
        oci_bind_by_name($st1, ':pid', $id_prod);
        $okInv = oci_execute($st1, OCI_NO_AUTO_COMMIT);
        oci_free_statement($st1);
        if (!$okInv) { oci_rollback($cn); } // no aborta; seguimos con producto

        $pl2 = "BEGIN FIDE_PROYECTO_FINAL_PCK.FIDE_ELIMINAR_PRODUCTO_SP(:pid); END;";
        $st2 = oci_parse($cn, $pl2);
        oci_bind_by_name($st2, ':pid', $id_prod);
        $okProd = oci_execute($st2, OCI_NO_AUTO_COMMIT);
        oci_free_statement($st2);

        if ($okProd) oci_commit($cn); else oci_rollback($cn);
        return $okProd;
    }

    public function listarCategorias(): array
    {
        $cn = $this->conn();
        $st = oci_parse($cn, "SELECT ID_CATEGORIA AS ID, NOMBRE_CATEGORIA AS NOMBRE
                                FROM FIDE_CATEGORIA_TB
                            ORDER BY NOMBRE_CATEGORIA");
        oci_execute($st);
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
        oci_execute($st);
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
        oci_execute($st);
        $out = [];
        while ($r = oci_fetch_assoc($st)) $out[] = $r;
        oci_free_statement($st);
        return $out;
    }
}
