<?php

require_once 'Models/conexion.php';

class FacturasDB
{
    
    private const SEQ_FACTURA = 'ID_FACTURA_SEQ';

    private function conn() {
        $cx = new Conexion();
        return $cx->getConexion(); 
    }

    private static function nv($v) {
        return ($v === '' || !isset($v)) ? null : $v;
    }

    private function nextFacturaId(): int
    {
        $cn = $this->conn();
        if (self::SEQ_FACTURA !== '') {
            $st = oci_parse($cn, "SELECT " . self::SEQ_FACTURA . ".NEXTVAL AS ID FROM DUAL");
            if (@oci_execute($st)) {
                $row = oci_fetch_assoc($st);
                oci_free_statement($st);
                if ($row && isset($row['ID'])) return (int)$row['ID'];
            }
            oci_free_statement($st);
        }
        $st = oci_parse($cn, "SELECT NVL(MAX(ID_FACTURA),0)+1 AS ID FROM FIDE_FACTURA_TB");
        @oci_execute($st);
        $row = oci_fetch_assoc($st);
        oci_free_statement($st);
        return (int)($row['ID'] ?? 1);
    }

    /* ==================== LISTADOS / LECTURAS ==================== */

    public function listarFacturas(): array
{
    $sql = "SELECT ID_FACTURA,
                   TO_CHAR(FECHA_REGISTRO,'YYYY-MM-DD') FECHA_REGISTRO,
                   MONTO_TOTAL, SUBTOTAL, IVA, DESCUENTO,
                   ID_USUARIO, NOMBRE,
                   ID_METODO_PAGO, NOMBRE_METODO_PAGO,
                   ID_ESTADO, ESTADO_NOMBRE,
                   (SELECT NVL(SUM(df.CANTIDAD),0)
                      FROM FIDE_DETALLE_FACTURA_TB df
                     WHERE df.ID_FACTURA = FIDE_FACTURA_V.ID_FACTURA) AS ITEMS
             FROM FIDE_FACTURA_V
             ORDER BY ID_FACTURA";
    $cn = $this->conn();
    $st = oci_parse($cn, $sql);
    @oci_execute($st);
    $out = [];
    while ($r = oci_fetch_assoc($st)) {
        $out[] = $r;
    }
    oci_free_statement($st);
    return $out;
}

    public function obtenerFacturaPorId(int $id): ?array
    {
        $sql = "SELECT f.ID_FACTURA,
                       TO_CHAR(f.FECHA_REGISTRO,'YYYY-MM-DD') FECHA_REGISTRO,
                       f.MONTO_TOTAL, f.SUBTOTAL, f.IVA, f.DESCUENTO,
                       f.ID_USUARIO, f.ID_METODO_PAGO, f.ID_ESTADO
                  FROM FIDE_FACTURA_TB f
                 WHERE f.ID_FACTURA = :id";
        $cn = $this->conn();
        $st = oci_parse($cn, $sql);
        oci_bind_by_name($st, ':id', $id);
        $ok = @oci_execute($st);
        if (!$ok) { oci_free_statement($st); return null; }
        $row = oci_fetch_assoc($st) ?: null;
        oci_free_statement($st);
        return $row;
    }

    public function listarDetalles(int $id_factura): array
    {
        $sql = "SELECT d.ID_FACTURA, d.ID_PRODUCTO,
                       p.NOMBRE AS NOMBRE_PRODUCTO,
                       d.CANTIDAD, d.PRECIO_UNITARIO, d.TOTAL,
                       d.ID_ESTADO, s.NOMBRE_ESTADO
                  FROM FIDE_DETALLE_FACTURA_TB d
             LEFT JOIN FIDE_PRODUCTO_TB p ON p.ID_PRODUCTO = d.ID_PRODUCTO
             LEFT JOIN FIDE_ESTADOS_TB  s ON s.ID_ESTADO   = d.ID_ESTADO
                 WHERE d.ID_FACTURA = :id
              ORDER BY d.ID_PRODUCTO";
        $cn = $this->conn();
        $st = oci_parse($cn, $sql);
        oci_bind_by_name($st, ':id', $id_factura);
        @oci_execute($st);
        $out = [];
        while ($r = oci_fetch_assoc($st)) $out[] = $r;
        oci_free_statement($st);
        return $out;
    }


    public function insertarFactura(
        ?string $fecha,  // 'YYYY-MM-DD'
        $montoTotal, $subtotal, $iva, $descuento,
        $id_usuario, $id_metodo_pago, $id_estado
    ): bool {
        $cn = $this->conn();
        $id = $this->nextFacturaId();

        $pl = "BEGIN
          FIDE_PROYECTO_FINAL_PCK.FIDE_INSERTAR_FACTURA_SP(
            :p_id,
            TO_DATE(:p_fecha,'YYYY-MM-DD'),
            :p_total, :p_sub, :p_iva, :p_desc,
            :p_usr, :p_mp, :p_est
          );
        END;";
        $st = oci_parse($cn, $pl);
        oci_bind_by_name($st, ':p_id', $id);
        $fecha = self::nv($fecha);              oci_bind_by_name($st, ':p_fecha', $fecha);
        $montoTotal = self::nv($montoTotal);    oci_bind_by_name($st, ':p_total', $montoTotal);
        $subtotal   = self::nv($subtotal);      oci_bind_by_name($st, ':p_sub', $subtotal);
        $iva        = self::nv($iva);           oci_bind_by_name($st, ':p_iva', $iva);
        $descuento  = self::nv($descuento);     oci_bind_by_name($st, ':p_desc', $descuento);
        $id_usuario = self::nv($id_usuario);    oci_bind_by_name($st, ':p_usr', $id_usuario);
        $id_metodo_pago = self::nv($id_metodo_pago); oci_bind_by_name($st, ':p_mp', $id_metodo_pago);
        $id_estado  = self::nv($id_estado);     oci_bind_by_name($st, ':p_est', $id_estado);

        $ok = @oci_execute($st, OCI_NO_AUTO_COMMIT);
        if ($ok) oci_commit($cn); else oci_rollback($cn);
        oci_free_statement($st);
        return $ok;
    }

    public function actualizarFactura(
        int $id, ?string $fecha,
        $montoTotal, $subtotal, $iva, $descuento,
        $id_usuario, $id_metodo_pago, $id_estado
    ): bool {
        $cn = $this->conn();
        $pl = "BEGIN
          FIDE_PROYECTO_FINAL_PCK.FIDE_MODIFICAR_FACTURA_SP(
            :p_id,
            TO_DATE(:p_fecha,'YYYY-MM-DD'),
            :p_total, :p_sub, :p_iva, :p_desc,
            :p_usr, :p_mp, :p_est
          );
        END;";
        $st = oci_parse($cn, $pl);
        oci_bind_by_name($st, ':p_id', $id);
        $fecha = self::nv($fecha);              oci_bind_by_name($st, ':p_fecha', $fecha);
        $montoTotal = self::nv($montoTotal);    oci_bind_by_name($st, ':p_total', $montoTotal);
        $subtotal   = self::nv($subtotal);      oci_bind_by_name($st, ':p_sub', $subtotal);
        $iva        = self::nv($iva);           oci_bind_by_name($st, ':p_iva', $iva);
        $descuento  = self::nv($descuento);     oci_bind_by_name($st, ':p_desc', $descuento);
        $id_usuario = self::nv($id_usuario);    oci_bind_by_name($st, ':p_usr', $id_usuario);
        $id_metodo_pago = self::nv($id_metodo_pago); oci_bind_by_name($st, ':p_mp', $id_metodo_pago);
        $id_estado  = self::nv($id_estado);     oci_bind_by_name($st, ':p_est', $id_estado);

        $ok = @oci_execute($st, OCI_NO_AUTO_COMMIT);
        if ($ok) oci_commit($cn); else oci_rollback($cn);
        oci_free_statement($st);
        return $ok;
    }

    public function eliminarFactura(int $id): bool
    {
        $cn = $this->conn();
        $pl = "BEGIN FIDE_PROYECTO_FINAL_PCK.FIDE_ELIMINAR_FACTURA_SP(:p_id); END;";
        $st = oci_parse($cn, $pl);
        oci_bind_by_name($st, ':p_id', $id);
        $ok = @oci_execute($st, OCI_NO_AUTO_COMMIT);
        if ($ok) oci_commit($cn); else oci_rollback($cn);
        oci_free_statement($st);
        return $ok;
    }


    public function upsertDetalle(
        int $id_factura,
        int $id_producto,
        $cantidad,
        $precio_unitario,
        $total,
        $id_estado
    ): bool {
        $cn = $this->conn();


        $chk = oci_parse($cn, "SELECT 1 FROM FIDE_DETALLE_FACTURA_TB WHERE ID_FACTURA = :f AND ID_PRODUCTO = :p");
        oci_bind_by_name($chk, ':f', $id_factura);
        oci_bind_by_name($chk, ':p', $id_producto);
        @oci_execute($chk);
        $existe = (bool) oci_fetch_assoc($chk);
        oci_free_statement($chk);

        if ($existe) {
            $pl = "BEGIN
              FIDE_PROYECTO_FINAL_PCK.FIDE_MODIFICAR_DETALLE_FACTURA_SP(
                :p_cant, :p_precio, :p_total, :p_fact, :p_prod, :p_est
              );
            END;";
        } else {
            $pl = "BEGIN
              FIDE_PROYECTO_FINAL_PCK.FIDE_INSERTAR_DETALLE_FACTURA_SP(
                :p_cant, :p_precio, :p_total, :p_fact, :p_prod, :p_est
              );
            END;";
        }

        $st = oci_parse($cn, $pl);
        $cantidad = self::nv($cantidad);               oci_bind_by_name($st, ':p_cant',   $cantidad);
        $precio_unitario = self::nv($precio_unitario); oci_bind_by_name($st, ':p_precio', $precio_unitario);
        $total = self::nv($total);                      oci_bind_by_name($st, ':p_total',  $total);
        oci_bind_by_name($st, ':p_fact',  $id_factura);
        oci_bind_by_name($st, ':p_prod',  $id_producto);
        $id_estado = self::nv($id_estado);             oci_bind_by_name($st, ':p_est',    $id_estado);

        $ok = @oci_execute($st, OCI_NO_AUTO_COMMIT);
        if ($ok) oci_commit($cn); else oci_rollback($cn);
        oci_free_statement($st);
        return $ok;
    }

    public function eliminarDetalle(int $id_factura, int $id_producto): bool
    {
        $cn = $this->conn();
        $pl = "BEGIN FIDE_PROYECTO_FINAL_PCK.FIDE_ELIMINAR_DETALLE_FACTURA_SP(:p_fact, :p_prod); END;";
        $st = oci_parse($cn, $pl);
        oci_bind_by_name($st, ':p_fact', $id_factura);
        oci_bind_by_name($st, ':p_prod', $id_producto);
        $ok = @oci_execute($st, OCI_NO_AUTO_COMMIT);
        if ($ok) oci_commit($cn); else oci_rollback($cn);
        oci_free_statement($st);
        return $ok;
    }


    public function listarEstados(): array {
        $cn = $this->conn();
        $st = oci_parse($cn, "SELECT ID_ESTADO AS ID, NOMBRE_ESTADO AS NOMBRE FROM FIDE_ESTADOS_TB ORDER BY NOMBRE_ESTADO");
        @oci_execute($st); $out=[]; while($r=oci_fetch_assoc($st)) $out[]=$r; oci_free_statement($st); return $out;
    }
    public function listarUsuarios(): array {
        $cn = $this->conn();
        $st = oci_parse($cn, "SELECT ID_USUARIO AS ID, NOMBRE AS NOMBRE FROM FIDE_USUARIO_TB ORDER BY NOMBRE");
        @oci_execute($st); $out=[]; while($r=oci_fetch_assoc($st)) $out[]=$r; oci_free_statement($st); return $out;
    }
    public function listarMetodosPago(): array {
        $cn = $this->conn();
        $st = oci_parse($cn, "SELECT ID_METODO_PAGO AS ID, NOMBRE_METODO_PAGO AS NOMBRE FROM FIDE_METODO_PAGO_TB ORDER BY NOMBRE_METODO_PAGO");
        @oci_execute($st); $out=[]; while($r=oci_fetch_assoc($st)) $out[]=$r; oci_free_statement($st); return $out;
    }
    public function listarProductos(): array {
        $cn = $this->conn();
        $st = oci_parse($cn, "SELECT ID_PRODUCTO AS ID, NOMBRE AS NOMBRE FROM FIDE_PRODUCTO_TB ORDER BY NOMBRE");
        @oci_execute($st); $out=[]; while($r=oci_fetch_assoc($st)) $out[]=$r; oci_free_statement($st); return $out;
    }
}
