<?php

require_once 'Models/conexion.php';

class EntregasDB
{
    
    private const SEQ_ENTREGA = 'ID_ENTREGA_SEQ';

    private function conn() {
        $cx = new Conexion();
        return $cx->getConexion(); // OCI8
    }

    private static function nv($v) {
        return ($v === '' || !isset($v)) ? null : $v;
    }

    private function nextEntregaId(): int
    {
        $cn = $this->conn();
        if (self::SEQ_ENTREGA !== '') {
            $st = oci_parse($cn, "SELECT " . self::SEQ_ENTREGA . ".NEXTVAL AS ID FROM DUAL");
            if (@oci_execute($st)) {
                $row = oci_fetch_assoc($st);
                oci_free_statement($st);
                if ($row && isset($row['ID'])) return (int)$row['ID'];
            }
            oci_free_statement($st);
        }
        $st = oci_parse($cn, "SELECT NVL(MAX(ID_ENTREGA),0)+1 AS ID FROM FIDE_ENTREGA_TB");
        @oci_execute($st);
        $row = oci_fetch_assoc($st);
        oci_free_statement($st);
        return (int)($row['ID'] ?? 1);
    }

    /* ==================== LISTADOS / LECTURAS ==================== */

    public function listarEntregas(): array
    {
        $sql = "SELECT e.ID_ENTREGA,
                       TO_CHAR(e.FECHA,'YYYY-MM-DD') FECHA,
                       e.ID_DIRECCION,
                       d.DETALLE_DIRECCION,
                       e.ID_USUARIO,
                       u.NOMBRE AS NOMBRE_USUARIO,
                       e.ID_ESTADO,
                       s.NOMBRE_ESTADO
                  FROM FIDE_ENTREGA_TB e
             LEFT JOIN FIDE_DIRECCION_TB d ON d.ID_DIRECCION = e.ID_DIRECCION
             LEFT JOIN FIDE_USUARIO_TB  u ON u.ID_USUARIO   = e.ID_USUARIO
             LEFT JOIN FIDE_ESTADOS_TB  s ON s.ID_ESTADO    = e.ID_ESTADO
              ORDER BY e.ID_ENTREGA";
        $cn = $this->conn();
        $st = oci_parse($cn, $sql);
        @oci_execute($st);
        $out = [];
        while ($r = oci_fetch_assoc($st)) $out[] = $r;
        oci_free_statement($st);
        return $out;
    }

    public function obtenerEntregaPorId(int $id): ?array
    {
        $sql = "SELECT e.ID_ENTREGA,
                       TO_CHAR(e.FECHA,'YYYY-MM-DD') FECHA,
                       e.ID_DIRECCION,
                       e.ID_USUARIO,
                       e.ID_ESTADO
                  FROM FIDE_ENTREGA_TB e
                 WHERE e.ID_ENTREGA = :id";
        $cn = $this->conn();
        $st = oci_parse($cn, $sql);
        oci_bind_by_name($st, ':id', $id);
        $ok = @oci_execute($st);
        if (!$ok) { oci_free_statement($st); return null; }
        $row = oci_fetch_assoc($st) ?: null;
        oci_free_statement($st);
        return $row;
    }

    public function obtenerDetallePorEntrega(int $id_entrega): ?array
    {
        // Con tu DDL, hay 0..1 fila por entrega (PK = ID_ENTREGA).
        $sql = "SELECT de.DESCRIPCION, de.CANTIDAD, de.ID_ESTADO,
                       s.NOMBRE_ESTADO
                  FROM FIDE_DETALLE_ENTREGA_TB de
             LEFT JOIN FIDE_ESTADOS_TB s ON s.ID_ESTADO = de.ID_ESTADO
                 WHERE de.ID_ENTREGA = :id";
        $cn = $this->conn();
        $st = oci_parse($cn, $sql);
        oci_bind_by_name($st, ':id', $id_entrega);
        @oci_execute($st);
        $row = oci_fetch_assoc($st) ?: null;
        oci_free_statement($st);
        return $row;
    }

    /* ==================== ESCRITURAS (SP) ==================== */
    /* ----- Entrega (cabecera) ----- */

    public function insertarEntrega(
        ?string $fecha,       // 'YYYY-MM-DD'
        $id_direccion,
        $id_usuario,
        $id_estado
    ): bool {
        $cn = $this->conn();
        $id = $this->nextEntregaId();

        $pl = "BEGIN
          FIDE_PROYECTO_FINAL_PCK.FIDE_INSERTAR_ENTREGA_SP(
            :p_id,
            TO_DATE(:p_fecha,'YYYY-MM-DD'),
            :p_dir,
            :p_usr,
            :p_estado
          );
        END;";
        $st = oci_parse($cn, $pl);
        oci_bind_by_name($st, ':p_id',     $id);
        $fecha = self::nv($fecha); oci_bind_by_name($st, ':p_fecha', $fecha);
        $id_direccion = self::nv($id_direccion); oci_bind_by_name($st, ':p_dir', $id_direccion);
        $id_usuario   = self::nv($id_usuario);   oci_bind_by_name($st, ':p_usr', $id_usuario);
        $id_estado    = self::nv($id_estado);    oci_bind_by_name($st, ':p_estado', $id_estado);

        $ok = @oci_execute($st, OCI_NO_AUTO_COMMIT);
        if ($ok) oci_commit($cn); else oci_rollback($cn);
        oci_free_statement($st);
        return $ok;
    }

    public function actualizarEntrega(
        int $id,
        ?string $fecha,       // 'YYYY-MM-DD'
        $id_direccion,
        $id_usuario,
        $id_estado
    ): bool {
        $cn = $this->conn();
        $pl = "BEGIN
          FIDE_PROYECTO_FINAL_PCK.FIDE_MODIFICAR_ENTREGA_SP(
            :p_id,
            TO_DATE(:p_fecha,'YYYY-MM-DD'),
            :p_dir,
            :p_usr,
            :p_estado
          );
        END;";
        $st = oci_parse($cn, $pl);
        oci_bind_by_name($st, ':p_id',     $id);
        $fecha = self::nv($fecha); oci_bind_by_name($st, ':p_fecha', $fecha);
        $id_direccion = self::nv($id_direccion); oci_bind_by_name($st, ':p_dir', $id_direccion);
        $id_usuario   = self::nv($id_usuario);   oci_bind_by_name($st, ':p_usr', $id_usuario);
        $id_estado    = self::nv($id_estado);    oci_bind_by_name($st, ':p_estado', $id_estado);

        $ok = @oci_execute($st, OCI_NO_AUTO_COMMIT);
        if ($ok) oci_commit($cn); else oci_rollback($cn);
        oci_free_statement($st);
        return $ok;
    }

    public function eliminarEntrega(int $id): bool
    {
        $cn = $this->conn();
        $pl = "BEGIN FIDE_PROYECTO_FINAL_PCK.FIDE_ELIMINAR_ENTREGA_SP(:p_id); END;";
        $st = oci_parse($cn, $pl);
        oci_bind_by_name($st, ':p_id', $id);
        $ok = @oci_execute($st, OCI_NO_AUTO_COMMIT);
        if ($ok) oci_commit($cn); else oci_rollback($cn);
        oci_free_statement($st);
        return $ok;
    }

    /* ----- Detalle de entrega ----- */

    public function upsertDetalle(
        int $id_entrega,
        string $descripcion,
        $cantidad,
        $id_estado
    ): bool {
        // Intento de UPDATE con SP de MODIFICAR; si NO existe fila, hacemos INSERT.
        $cn = $this->conn();

        // ¿Existe?
        $stx = oci_parse($cn, "SELECT 1 FROM FIDE_DETALLE_ENTREGA_TB WHERE ID_ENTREGA = :id");
        oci_bind_by_name($stx, ':id', $id_entrega);
        @oci_execute($stx);
        $existe = (bool) oci_fetch_assoc($stx);
        oci_free_statement($stx);

        if ($existe) {
            $pl = "BEGIN
              FIDE_PROYECTO_FINAL_PCK.FIDE_MODIFICAR_DETALLE_ENTREGA_SP(
                :p_desc, :p_cant, :p_id_entrega, :p_estado
              );
            END;";
        } else {
            $pl = "BEGIN
              FIDE_PROYECTO_FINAL_PCK.FIDE_INSERTAR_DETALLE_ENTREGA_SP(
                :p_desc, :p_cant, :p_id_entrega, :p_estado
              );
            END;";
        }

        $st = oci_parse($cn, $pl);
        oci_bind_by_name($st, ':p_desc',       $descripcion);
        $cantidad = self::nv($cantidad);       oci_bind_by_name($st, ':p_cant', $cantidad);
        oci_bind_by_name($st, ':p_id_entrega', $id_entrega);
        $id_estado = self::nv($id_estado);     oci_bind_by_name($st, ':p_estado', $id_estado);

        $ok = @oci_execute($st, OCI_NO_AUTO_COMMIT);
        if ($ok) oci_commit($cn); else oci_rollback($cn);
        oci_free_statement($st);
        return $ok;
    }

    public function eliminarDetalle(int $id_entrega): bool
    {
        $cn = $this->conn();
        $pl = "BEGIN FIDE_PROYECTO_FINAL_PCK.FIDE_ELIMINAR_DETALLE_ENTREGA_SP(:p_id_entrega); END;";
        $st = oci_parse($cn, $pl);
        oci_bind_by_name($st, ':p_id_entrega', $id_entrega);
        $ok = @oci_execute($st, OCI_NO_AUTO_COMMIT);
        if ($ok) oci_commit($cn); else oci_rollback($cn);
        oci_free_statement($st);
        return $ok;
    }

    /* ==================== CATÁLOGOS ==================== */

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
    public function listarDirecciones(): array {
        $cn = $this->conn();
        $st = oci_parse($cn, "SELECT ID_DIRECCION AS ID, DETALLE_DIRECCION AS NOMBRE FROM FIDE_DIRECCION_TB ORDER BY DETALLE_DIRECCION");
        @oci_execute($st); $out=[]; while($r=oci_fetch_assoc($st)) $out[]=$r; oci_free_statement($st); return $out;
    }
}
