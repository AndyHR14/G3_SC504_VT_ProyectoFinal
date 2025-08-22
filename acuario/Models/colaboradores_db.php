<?php
// Models/colaboradores_db.php
require_once 'Models/conexion.php';

class ColaboradoresDB
{
    private const SEQ_NAME = 'ID_USUARIO_SEQ';

    
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
        $cn  = $this->conn();
        $sql = "SELECT " . self::SEQ_NAME . ".NEXTVAL AS ID FROM DUAL";
        $st  = oci_parse($cn, $sql);
        if (!@oci_execute($st)) { oci_free_statement($st); return null; }
        $row = oci_fetch_assoc($st);
        oci_free_statement($st);
        return $row && isset($row['ID']) ? (int)$row['ID'] : null;
    }

   
    private function simpleList(string $sql, string $ctx = 'simpleList'): array
    {
        $cn = $this->conn();
        $st = oci_parse($cn, $sql);
        @oci_execute($st);
        $out = [];
        while ($r = oci_fetch_assoc($st)) $out[] = $r;
        oci_free_statement($st);
        return $out;
    }

    

    
    public function listarColaboradores(): array
    {
        $sql = "SELECT 
                    v.ID_USUARIO,
                    v.NOMBRE,
                    TO_CHAR(v.FECHA_REGISTRO,'YYYY-MM-DD') AS FECHA_REGISTRO,
                    v.TELEFONO,
                    v.CORREO,
                    v.ID_ESTADO,
                    v.ID_ROL,
                    v.NOMBRE_ROL,
                    v.ID_ESTADO,
                    v.ESTADO_NOMBRE
                FROM FIDE_USUARIO_COLABORADOR_V v
                ORDER BY v.ID_USUARIO";
        $cn = $this->conn();
        $st = oci_parse($cn, $sql);
        @oci_execute($st);
        $out = [];
        while ($r = oci_fetch_assoc($st)) $out[] = $r;
        oci_free_statement($st);
        return $out;
    }

    
    public function obtenerColaboradorPorId(int $id): ?array
    {
        $sql = "SELECT 
                    v.ID_USUARIO,
                    v.NOMBRE,
                    TO_CHAR(v.FECHA_REGISTRO,'YYYY-MM-DD') AS FECHA_REGISTRO,
                    v.TELEFONO,
                    v.CORREO,
                    v.ID_ESTADO,
                    v.ID_ROL,
                    v.NOMBRE_ROL,
                    v.ID_ESTADO,
                    v.ESTADO_NOMBRE
                FROM FIDE_USUARIO_COLABORADOR_V v
                WHERE v.ID_USUARIO = :id";
        $cn = $this->conn();
        $st = oci_parse($cn, $sql);
        oci_bind_by_name($st, ':id', $id);
        $ok = @oci_execute($st);
        if (!$ok) { oci_free_statement($st); return null; }
        $row = oci_fetch_assoc($st) ?: null;
        oci_free_statement($st);
        return $row;
    }

    /* ===== Escrituras con paquete ===== */

    public function insertarColaborador(
        string $nombre,
        ?string $fecha_registro, // 'YYYY-MM-DD'
        string $telefono = '',
        string $correo = '',
        $id_estado = null,
        $id_rol = null
    ): bool {
        $cn = $this->conn();
        $id = $this->nextId(); // si hay trigger y no quieres calcular ID aquí, pon SEQ_NAME = '' y ajusta el SP
        if (self::SEQ_NAME !== '' && $id === null) return false;

        $pl = "BEGIN
          FIDE_PROYECTO_FINAL_PCK.FIDE_INSERTAR_USUARIO_SP(
            :p_id, :p_nombre,
            TO_DATE(:p_freg,'YYYY-MM-DD'),
            :p_tel, :p_correo, :p_estado, :p_rol
          );
        END;";
        $st = oci_parse($cn, $pl);
        oci_bind_by_name($st, ':p_id',     $id);
        oci_bind_by_name($st, ':p_nombre', $nombre);
        $f = self::nv($fecha_registro); oci_bind_by_name($st, ':p_freg', $f);
        oci_bind_by_name($st, ':p_tel',    $telefono);
        oci_bind_by_name($st, ':p_correo', $correo);
        $id_estado = self::nv($id_estado); oci_bind_by_name($st, ':p_estado', $id_estado);
        $id_rol    = self::nv($id_rol);    oci_bind_by_name($st, ':p_rol', $id_rol);

        $ok = @oci_execute($st, OCI_NO_AUTO_COMMIT);
        if ($ok) oci_commit($cn); else oci_rollback($cn);
        oci_free_statement($st);
        return $ok;
    }

    public function actualizarColaborador(
        int $id,
        string $nombre,
        ?string $fecha_registro,
        string $telefono = '',
        string $correo = '',
        $id_estado = null,
        $id_rol = null
    ): bool {
        $cn = $this->conn();
        $pl = "BEGIN
          FIDE_PROYECTO_FINAL_PCK.FIDE_MODIFICAR_USUARIO_SP(
            :p_id, :p_nombre,
            TO_DATE(:p_freg,'YYYY-MM-DD'),
            :p_tel, :p_correo, :p_estado, :p_rol
          );
        END;";
        $st = oci_parse($cn, $pl);
        oci_bind_by_name($st, ':p_id',     $id);
        oci_bind_by_name($st, ':p_nombre', $nombre);
        $f = self::nv($fecha_registro); oci_bind_by_name($st, ':p_freg', $f);
        oci_bind_by_name($st, ':p_tel',    $telefono);
        oci_bind_by_name($st, ':p_correo', $correo);
        $id_estado = self::nv($id_estado); oci_bind_by_name($st, ':p_estado', $id_estado);
        $id_rol    = self::nv($id_rol);    oci_bind_by_name($st, ':p_rol', $id_rol);

        $ok = @oci_execute($st, OCI_NO_AUTO_COMMIT);
        if ($ok) oci_commit($cn); else oci_rollback($cn);
        oci_free_statement($st);
        return $ok;
    }

    public function eliminarColaborador(int $id): bool
    {
        $cn = $this->conn();
        $pl = "BEGIN
          FIDE_PROYECTO_FINAL_PCK.FIDE_ELIMINAR_USUARIO_SP(:p_id);
        END;";
        $st = oci_parse($cn, $pl);
        oci_bind_by_name($st, ':p_id', $id);

        $ok = @oci_execute($st, OCI_NO_AUTO_COMMIT);
        if ($ok) oci_commit($cn); else oci_rollback($cn);
        oci_free_statement($st);
        return $ok;
    }

    /* ===== Listas desde VISTAS ===== */

    public function listarEstados(): array
    {
        return $this->simpleList(
            "SELECT ID, NOMBRE FROM FIDE_ESTADO_V ORDER BY NOMBRE",
            "listarEstados"
        );
    }

    public function listarRoles(): array
    {
        return $this->simpleList(
            "SELECT ID, NOMBRE FROM FIDE_ROL_V ORDER BY NOMBRE",
            "listarRoles"
        );
    }
}
