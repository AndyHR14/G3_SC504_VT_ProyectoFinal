<?php

require_once 'Models/conexion.php';

class ClientesDB
{
   
    private const SEQ_USUARIO = 'ID_USUARIO_SEQ';

    private function conn() {
        $cx = new Conexion();
        return $cx->getConexion(); // OCI8
    }

    private static function nv($v) {
        return ($v === '' || !isset($v)) ? null : $v;
    }

    private function nextUsuarioId(): int
    {
        $cn = $this->conn();
        if (self::SEQ_USUARIO !== '') {
            $st = oci_parse($cn, "SELECT " . self::SEQ_USUARIO . ".NEXTVAL AS ID FROM DUAL");
            if (@oci_execute($st)) {
                $row = oci_fetch_assoc($st);
                oci_free_statement($st);
                if ($row && isset($row['ID'])) return (int)$row['ID'];
            }
            oci_free_statement($st);
        }
        $st = oci_parse($cn, "SELECT NVL(MAX(ID_USUARIO),0)+1 AS ID FROM FIDE_USUARIO_TB");
        @oci_execute($st);
        $row = oci_fetch_assoc($st);
        oci_free_statement($st);
        return (int)($row['ID'] ?? 1);
    }

    /* ========= Lecturas ========= */

    public function listar(): array
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
                    v.ID_DIRECCION,
                    v.DETALLE_DIRECCION,
                    v.ID_ESTADO,
                    v.ESTADO_NOMBRE
                FROM FIDE_USUARIO_CLIENTE_V v
                ORDER BY v.ID_USUARIO";
        $cn = $this->conn();
        $st = oci_parse($cn, $sql);
        @oci_execute($st);
        $out = [];
        while ($r = oci_fetch_assoc($st)) $out[] = $r;
        oci_free_statement($st);
        return $out;
    }


    public function obtenerPorId(int $id): ?array
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
                    v.ID_DIRECCION,
                    v.DETALLE_DIRECCION,
                    v.ID_ESTADO,
                    v.ESTADO_NOMBRE
                FROM FIDE_USUARIO_CLIENTE_V v
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

    /* ========= Escrituras (SP) ========= */

    public function insertar(
        string $nombre,
        ?string $fecha_registro, // 'YYYY-MM-DD'
        ?string $telefono,
        ?string $correo,
        $id_estado,
        $id_rol,
        $id_direccion
    ): bool {
        $cn = $this->conn();
        $id = $this->nextUsuarioId();

        $pl = "BEGIN
          FIDE_PROYECTO_FINAL_PCK.FIDE_INSERTAR_USUARIO_SP(
            :p_id,
            :p_nombre,
            TO_DATE(:p_fecha,'YYYY-MM-DD'),
            :p_tel,
            :p_correo,
            :p_estado,
            :p_rol
          );
        END;";
        $st = oci_parse($cn, $pl);
        oci_bind_by_name($st, ':p_id',     $id);
        oci_bind_by_name($st, ':p_nombre', $nombre);
        $fecha_registro = self::nv($fecha_registro); oci_bind_by_name($st, ':p_fecha',  $fecha_registro);
        oci_bind_by_name($st, ':p_tel',    $telefono);
        oci_bind_by_name($st, ':p_correo', $correo);
        $id_estado = self::nv($id_estado); oci_bind_by_name($st, ':p_estado', $id_estado);
        $id_rol    = self::nv($id_rol);    oci_bind_by_name($st, ':p_rol',    $id_rol);

        $ok = @oci_execute($st, OCI_NO_AUTO_COMMIT);

        // Si quieres guardar ID_DIRECCION también, haz un UPDATE posterior (no hay SP):
        if ($ok && self::nv($id_direccion) !== null) {
            $upd = oci_parse($cn, "UPDATE FIDE_USUARIO_TB SET ID_DIRECCION = :dir WHERE ID_USUARIO = :id");
            oci_bind_by_name($upd, ':dir', $id_direccion);
            oci_bind_by_name($upd, ':id',  $id);
            $ok = @oci_execute($upd, OCI_NO_AUTO_COMMIT) && $ok;
            oci_free_statement($upd);
        }

        if ($ok) oci_commit($cn); else oci_rollback($cn);
        oci_free_statement($st);
        return $ok;
    }

    public function actualizar(
        int $id,
        string $nombre,
        ?string $fecha_registro, // 'YYYY-MM-DD'
        ?string $telefono,
        ?string $correo,
        $id_estado,
        $id_rol,
        $id_direccion
    ): bool {
        $cn = $this->conn();
        $pl = "BEGIN
          FIDE_PROYECTO_FINAL_PCK.FIDE_MODIFICAR_USUARIO_SP(
            :p_id,
            :p_nombre,
            TO_DATE(:p_fecha,'YYYY-MM-DD'),
            :p_tel,
            :p_correo,
            :p_estado,
            :p_rol
          );
        END;";
        $st = oci_parse($cn, $pl);
        oci_bind_by_name($st, ':p_id',     $id);
        oci_bind_by_name($st, ':p_nombre', $nombre);
        $fecha_registro = self::nv($fecha_registro); oci_bind_by_name($st, ':p_fecha',  $fecha_registro);
        oci_bind_by_name($st, ':p_tel',    $telefono);
        oci_bind_by_name($st, ':p_correo', $correo);
        $id_estado = self::nv($id_estado); oci_bind_by_name($st, ':p_estado', $id_estado);
        $id_rol    = self::nv($id_rol);    oci_bind_by_name($st, ':p_rol',    $id_rol);

        $ok = @oci_execute($st, OCI_NO_AUTO_COMMIT);

        if ($ok) {
            $upd = oci_parse($cn, "UPDATE FIDE_USUARIO_TB
                                      SET ID_DIRECCION = :dir
                                    WHERE ID_USUARIO  = :id");
            $id_direccion = self::nv($id_direccion);
            oci_bind_by_name($upd, ':dir', $id_direccion);
            oci_bind_by_name($upd, ':id',  $id);
            $ok = @oci_execute($upd, OCI_NO_AUTO_COMMIT) && $ok;
            oci_free_statement($upd);
        }

        if ($ok) oci_commit($cn); else oci_rollback($cn);
        oci_free_statement($st);
        return $ok;
    }

    public function eliminar(int $id): bool
    {
        $cn = $this->conn();
        $pl = "BEGIN FIDE_PROYECTO_FINAL_PCK.FIDE_ELIMINAR_USUARIO_SP(:p_id); END;";
        $st = oci_parse($cn, $pl);
        oci_bind_by_name($st, ':p_id', $id);
        $ok = @oci_execute($st, OCI_NO_AUTO_COMMIT);
        if ($ok) oci_commit($cn); else oci_rollback($cn);
        oci_free_statement($st);
        return $ok;
    }

    /* ========= Catálogos ========= */

    public function listarEstados(): array
    {
        $cn = $this->conn();
        $st = oci_parse($cn, "SELECT ID_ESTADO AS ID, NOMBRE_ESTADO AS NOMBRE FROM FIDE_ESTADOS_TB ORDER BY NOMBRE_ESTADO");
        @oci_execute($st);
        $out=[]; while($r=oci_fetch_assoc($st)) $out[]=$r; oci_free_statement($st); return $out;
    }

    public function listarRoles(): array
    {
        $cn = $this->conn();
        $st = oci_parse($cn, "SELECT ID_ROL AS ID, NOMBRE_ROL AS NOMBRE FROM FIDE_ROL_TB ORDER BY NOMBRE_ROL");
        @oci_execute($st);
        $out=[]; while($r=oci_fetch_assoc($st)) $out[]=$r; oci_free_statement($st); return $out;
    }

    public function listarDirecciones(): array
    {
        $cn = $this->conn();
        $st = oci_parse($cn, "SELECT ID_DIRECCION AS ID, DETALLE_DIRECCION AS NOMBRE FROM FIDE_DIRECCION_TB ORDER BY DETALLE_DIRECCION");
        @oci_execute($st);
        $out=[]; while($r=oci_fetch_assoc($st)) $out[]=$r; oci_free_statement($st); return $out;
    }
}
