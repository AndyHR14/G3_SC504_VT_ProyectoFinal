<?php

require_once 'Models/conexion.php';

class HorariosDB
{
    
    private const SEQ_NAME = 'ID_HORARIO_SEQ';

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
        $st  = oci_parse($cn, "SELECT " . self::SEQ_NAME . ".NEXTVAL AS ID FROM DUAL");
        if (!@oci_execute($st)) { oci_free_statement($st); return null; }
        $row = oci_fetch_assoc($st);
        oci_free_statement($st);
        return $row && isset($row['ID']) ? (int)$row['ID'] : null;
    }

    /* ===== Listados / Lecturas ===== */

    public function listarHorarios(): array
    {
        $sql = "SELECT h.ID_HORARIO, h.DIA,
                       TO_CHAR(h.HORA_INICIO,'HH24:MI') AS HORA_INICIO,
                       TO_CHAR(h.HORA_FINAL, 'HH24:MI') AS HORA_FINAL,
                       h.ID_USUARIO, u.NOMBRE AS NOMBRE_USUARIO,
                       h.ID_ESTADO, e.NOMBRE_ESTADO
                  FROM FIDE_HORARIO_TB h
             LEFT JOIN FIDE_USUARIO_TB u ON u.ID_USUARIO = h.ID_USUARIO
             LEFT JOIN FIDE_ESTADOS_TB e ON e.ID_ESTADO  = h.ID_ESTADO
              ORDER BY h.ID_HORARIO";
        $cn = $this->conn();
        $st = oci_parse($cn, $sql);
        @oci_execute($st);
        $out = [];
        while ($r = oci_fetch_assoc($st)) $out[] = $r;
        oci_free_statement($st);
        return $out;
    }

    public function obtenerHorarioPorId(int $id): ?array
    {
        $sql = "SELECT ID_HORARIO, DIA,
                       TO_CHAR(HORA_INICIO,'HH24:MI') AS HORA_INICIO,
                       TO_CHAR(HORA_FINAL,'HH24:MI')  AS HORA_FINAL,
                       ID_USUARIO, ID_ESTADO
                  FROM FIDE_HORARIO_TB
                 WHERE ID_HORARIO = :id";
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

    public function insertarHorario(
        string $dia,
        string $hora_inicio, // 'HH:MM'
        string $hora_final,  // 'HH:MM'
        $id_usuario,
        $id_estado
    ): bool {
        $cn = $this->conn();
        $id = $this->nextId(); // si usas trigger, pon SEQ_NAME=''
        if (self::SEQ_NAME !== '' && $id === null) return false;

        $pl = "BEGIN
          FIDE_PROYECTO_FINAL_PCK.FIDE_INSERTAR_HORARIO_SP(
            :p_id, :p_dia,
            TO_DATE(:p_hini,'HH24:MI'),
            TO_DATE(:p_hfin,'HH24:MI'),
            :p_usuario, :p_estado
          );
        END;";
        $st = oci_parse($cn, $pl);
        oci_bind_by_name($st, ':p_id',      $id);
        oci_bind_by_name($st, ':p_dia',     $dia);
        oci_bind_by_name($st, ':p_hini',    $hora_inicio);
        oci_bind_by_name($st, ':p_hfin',    $hora_final);
        $id_usuario = self::nv($id_usuario); oci_bind_by_name($st, ':p_usuario', $id_usuario);
        $id_estado  = self::nv($id_estado);  oci_bind_by_name($st, ':p_estado',  $id_estado);

        $ok = @oci_execute($st, OCI_NO_AUTO_COMMIT);
        if ($ok) oci_commit($cn); else oci_rollback($cn);
        oci_free_statement($st);
        return $ok;
    }

    public function actualizarHorario(
        int $id,
        string $dia,
        string $hora_inicio, // 'HH:MM'
        string $hora_final,  // 'HH:MM'
        $id_usuario,
        $id_estado
    ): bool {
        $cn = $this->conn();
        $pl = "BEGIN
          FIDE_PROYECTO_FINAL_PCK.FIDE_MODIFICAR_HORARIO_SP(
            :p_id, :p_dia,
            TO_DATE(:p_hini,'HH24:MI'),
            TO_DATE(:p_hfin,'HH24:MI'),
            :p_usuario, :p_estado
          );
        END;";
        $st = oci_parse($cn, $pl);
        oci_bind_by_name($st, ':p_id',      $id);
        oci_bind_by_name($st, ':p_dia',     $dia);
        oci_bind_by_name($st, ':p_hini',    $hora_inicio);
        oci_bind_by_name($st, ':p_hfin',    $hora_final);
        $id_usuario = self::nv($id_usuario); oci_bind_by_name($st, ':p_usuario', $id_usuario);
        $id_estado  = self::nv($id_estado);  oci_bind_by_name($st, ':p_estado',  $id_estado);

        $ok = @oci_execute($st, OCI_NO_AUTO_COMMIT);
        if ($ok) oci_commit($cn); else oci_rollback($cn);
        oci_free_statement($st);
        return $ok;
    }

    public function eliminarHorario(int $id): bool
    {
        $cn = $this->conn();
        $pl = "BEGIN
          FIDE_PROYECTO_FINAL_PCK.FIDE_ELIMINAR_HORARIO_SP(:p_id);
        END;";
        $st = oci_parse($cn, $pl);
        oci_bind_by_name($st, ':p_id', $id);

        $ok = @oci_execute($st, OCI_NO_AUTO_COMMIT);
        if ($ok) oci_commit($cn); else oci_rollback($cn);
        oci_free_statement($st);
        return $ok;
    }

    /* ===== Catálogos ===== */

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

    public function listarUsuarios(): array
    {
        $cn = $this->conn();
        $st = oci_parse($cn, "SELECT ID_USUARIO AS ID, NOMBRE
                                FROM FIDE_USUARIO_TB
                            ORDER BY NOMBRE");
        @oci_execute($st);
        $out = [];
        while ($r = oci_fetch_assoc($st)) $out[] = $r;
        oci_free_statement($st);
        return $out;
    }
}
