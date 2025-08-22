<?php

require_once 'Models/conexion.php';

class ProveedoresDB
{
    
    private const SEQ_EMPRESA = 'ID_EMPRESA_SEQ';

    private function conn() {
        $cx = new Conexion();
        return $cx->getConexion(); 
    }

    private static function nv($v) {
        return ($v === '' || !isset($v)) ? null : $v;
    }

    private function nextEmpresaId(): int
    {
        $cn = $this->conn();
        if (self::SEQ_EMPRESA !== '') {
            $st = oci_parse($cn, "SELECT " . self::SEQ_EMPRESA . ".NEXTVAL AS ID FROM DUAL");
            if (@oci_execute($st)) {
                $row = oci_fetch_assoc($st);
                oci_free_statement($st);
                if ($row && isset($row['ID'])) return (int)$row['ID'];
            }
            oci_free_statement($st);
        }
        // Fallback sin secuencia: MAX+1
        $st = oci_parse($cn, "SELECT NVL(MAX(ID_EMPRESA),0)+1 AS ID FROM FIDE_EMPRESA_TB");
        @oci_execute($st);
        $row = oci_fetch_assoc($st);
        oci_free_statement($st);
        return (int)($row['ID'] ?? 1);
    }

    /* =============== Lecturas =============== */

    public function listar(): array
    {
         $sql = "SELECT ID_EMPRESA,
                   NOMBRE_EMPRESA,
                   TELEFONO,
                   CORREO,
                   ID_DIRECCION,
                   ID_ESTADO,
                   DETALLE_DIRECCION,
                   ESTADO_NOMBRE
             FROM FIDE_EMPRESA_V
             ORDER BY ID_EMPRESA";
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

    public function obtenerPorId(int $id): ?array
    {
        $sql = "SELECT e.ID_EMPRESA,
                       e.NOMBRE_EMPRESA,
                       e.TELEFONO,
                       e.CORREO,
                       e.ID_DIRECCION,
                       e.ID_ESTADO
                  FROM FIDE_EMPRESA_TB e
                 WHERE e.ID_EMPRESA = :id";
        $cn = $this->conn();
        $st = oci_parse($cn, $sql);
        oci_bind_by_name($st, ':id', $id);
        $ok = @oci_execute($st);
        if (!$ok) { oci_free_statement($st); return null; }
        $row = oci_fetch_assoc($st) ?: null;
        oci_free_statement($st);
        return $row;
    }

    
    public function insertar(
        string $nombre,
        ?string $telefono,
        ?string $correo,
        $id_direccion,
        $id_estado
    ): bool {
        $cn = $this->conn();
        $id = $this->nextEmpresaId();

        $pl = "BEGIN
          FIDE_PROYECTO_FINAL_PCK.FIDE_INSERTAR_EMPRESA_SP(
            :p_id, :p_nombre, :p_tel, :p_correo, :p_dir, :p_estado
          );
        END;";
        $st = oci_parse($cn, $pl);
        oci_bind_by_name($st, ':p_id',     $id);
        oci_bind_by_name($st, ':p_nombre', $nombre);
        oci_bind_by_name($st, ':p_tel',    $telefono);
        oci_bind_by_name($st, ':p_correo', $correo);
        $id_direccion = self::nv($id_direccion); oci_bind_by_name($st, ':p_dir',    $id_direccion);
        $id_estado    = self::nv($id_estado);    oci_bind_by_name($st, ':p_estado', $id_estado);

        $ok = @oci_execute($st, OCI_NO_AUTO_COMMIT);
        if ($ok) oci_commit($cn); else oci_rollback($cn);
        oci_free_statement($st);
        return $ok;
    }

    public function actualizar(
        int $id,
        string $nombre,
        ?string $telefono,
        ?string $correo,
        $id_direccion,
        $id_estado
    ): bool {
        $cn = $this->conn();
        $pl = "BEGIN
          FIDE_PROYECTO_FINAL_PCK.FIDE_MODIFICAR_EMPRESA_SP(
            :p_id, :p_nombre, :p_tel, :p_correo, :p_dir, :p_estado
          );
        END;";
        $st = oci_parse($cn, $pl);
        oci_bind_by_name($st, ':p_id',     $id);
        oci_bind_by_name($st, ':p_nombre', $nombre);
        oci_bind_by_name($st, ':p_tel',    $telefono);
        oci_bind_by_name($st, ':p_correo', $correo);
        $id_direccion = self::nv($id_direccion); oci_bind_by_name($st, ':p_dir',    $id_direccion);
        $id_estado    = self::nv($id_estado);    oci_bind_by_name($st, ':p_estado', $id_estado);

        $ok = @oci_execute($st, OCI_NO_AUTO_COMMIT);
        if ($ok) oci_commit($cn); else oci_rollback($cn);
        oci_free_statement($st);
        return $ok;
    }

    public function eliminar(int $id): bool
    {
        $cn = $this->conn();
        $pl = "BEGIN FIDE_PROYECTO_FINAL_PCK.FIDE_ELIMINAR_EMPRESA_SP(:p_id); END;";
        $st = oci_parse($cn, $pl);
        oci_bind_by_name($st, ':p_id', $id);
        $ok = @oci_execute($st, OCI_NO_AUTO_COMMIT);
        if ($ok) oci_commit($cn); else oci_rollback($cn);
        oci_free_statement($st);
        return $ok;
    }



    public function listarDirecciones(): array
    {
        $cn = $this->conn();
        $st = oci_parse($cn, "SELECT ID_DIRECCION AS ID, DETALLE_DIRECCION AS NOMBRE
                                FROM FIDE_DIRECCION_TB
                            ORDER BY DETALLE_DIRECCION");
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
