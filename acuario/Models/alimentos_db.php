<?php

require_once 'Models/conexion.php';

class AlimentosDB
{
 
    private const SEQ_NAME = 'SEQ_MARCA_ALIMENTO_ID';

    private function conn() {
        $cx = new Conexion();
        return $cx->getConexion(); 
    }

    private static function nv($v) {
        return ($v === '' || !isset($v)) ? null : $v;
    }

    private function nextId(): ?int
    {
        if (self::SEQ_NAME === '') return null;
        $cn = $this->conn();
        $sql = "SELECT " . self::SEQ_NAME . ".NEXTVAL AS ID FROM DUAL";
        $st = oci_parse($cn, $sql);
        if (!@oci_execute($st)) {
            $e = oci_error($st);
            error_log('[AlimentosDB::nextId] ' . ($e['message'] ?? 'Error'));
            oci_free_statement($st);
            return null;
        }
        $row = oci_fetch_assoc($st);
        oci_free_statement($st);
        return $row && isset($row['ID']) ? (int)$row['ID'] : null;
    }

    /* ===== Listados / Lecturas ===== */

    public function listarAlimentos(): array
    {
        $sql = "SELECT a.ID_MARCA_ALIMENTO, a.NOMBRE, a.DESCRIPCION, a.ID_ESTADO,
                       e.NOMBRE_ESTADO
                  FROM FIDE_MARCA_ALIMENTO_TB a
             LEFT JOIN FIDE_ESTADOS_TB e ON e.ID_ESTADO = a.ID_ESTADO
              ORDER BY a.ID_MARCA_ALIMENTO";
        $cn = $this->conn();
        $st = oci_parse($cn, $sql);
        @oci_execute($st);
        $out = [];
        while ($r = oci_fetch_assoc($st)) $out[] = $r;
        oci_free_statement($st);
        return $out;
    }

    public function obtenerAlimentoPorId(int $id): ?array
    {
        $sql = "SELECT ID_MARCA_ALIMENTO, NOMBRE, DESCRIPCION, ID_ESTADO
                  FROM FIDE_MARCA_ALIMENTO_TB
                 WHERE ID_MARCA_ALIMENTO = :id";
        $cn = $this->conn();
        $st = oci_parse($cn, $sql);
        oci_bind_by_name($st, ":id", $id);
        $ok = @oci_execute($st);
        if (!$ok) { oci_free_statement($st); return null; }
        $row = oci_fetch_assoc($st) ?: null;
        oci_free_statement($st);
        return $row;
    }

    /* ===== Escrituras usando el paquete ===== */

    public function insertarAlimento(string $nombre, string $descripcion = '', $id_estado = null): bool
    {
        $cn = $this->conn();
        $id = $this->nextId(); // si SEQ_NAME = '' y tienes trigger, puedes setear $id = null y ajustar el SP
        if (self::SEQ_NAME !== '' && $id === null) return false;

        $pl = "BEGIN
            FIDE_PROYECTO_FINAL_PCK.FIDE_INSERTAR_MARCA_ALIMENTO_SP(
                :p_id, :p_nombre, :p_desc, :p_estado
            );
        END;";
        $st = oci_parse($cn, $pl);
        // Si usas trigger y tu SP permite NULL, puedes pasar null en :p_id
        oci_bind_by_name($st, ":p_id", $id);
        oci_bind_by_name($st, ":p_nombre", $nombre);
        oci_bind_by_name($st, ":p_desc", $descripcion);
        $id_estado = self::nv($id_estado); oci_bind_by_name($st, ":p_estado", $id_estado);

        $ok = @oci_execute($st, OCI_NO_AUTO_COMMIT);
        if ($ok) oci_commit($cn); else oci_rollback($cn);
        oci_free_statement($st);
        return $ok;
    }

    public function actualizarAlimento(int $id, string $nombre, string $descripcion = '', $id_estado = null): bool
    {
        $cn = $this->conn();
        $pl = "BEGIN
            FIDE_PROYECTO_FINAL_PCK.FIDE_MODIFICAR_MARCA_ALIMENTO_SP(
                :p_id, :p_nombre, :p_desc, :p_estado
            );
        END;";
        $st = oci_parse($cn, $pl);
        oci_bind_by_name($st, ":p_id", $id);
        oci_bind_by_name($st, ":p_nombre", $nombre);
        oci_bind_by_name($st, ":p_desc", $descripcion);
        $id_estado = self::nv($id_estado); oci_bind_by_name($st, ":p_estado", $id_estado);

        $ok = @oci_execute($st, OCI_NO_AUTO_COMMIT);
        if ($ok) oci_commit($cn); else oci_rollback($cn);
        oci_free_statement($st);
        return $ok;
    }

    public function eliminarAlimento(int $id): bool
    {
        $cn = $this->conn();
        $pl = "BEGIN
            FIDE_PROYECTO_FINAL_PCK.FIDE_ELIMINAR_MARCA_ALIMENTO_SP(:p_id);
        END;";
        $st = oci_parse($cn, $pl);
        oci_bind_by_name($st, ":p_id", $id);

        $ok = @oci_execute($st, OCI_NO_AUTO_COMMIT);
        if ($ok) oci_commit($cn); else oci_rollback($cn);
        oci_free_statement($st);
        return $ok;
    }

    /* ===== Catálogos ===== */

    public function listarEstados(): array
    {
        $sql = "SELECT ID_ESTADO AS ID, NOMBRE_ESTADO AS NOMBRE
                  FROM FIDE_ESTADOS_TB
              ORDER BY NOMBRE_ESTADO";
        $cn = $this->conn();
        $st = oci_parse($cn, $sql);
        @oci_execute($st);
        $out = [];
        while ($r = oci_fetch_assoc($st)) $out[] = $r;
        oci_free_statement($st);
        return $out;
    }
}
