<?php

require_once 'Models/conexion.php';

class AnimalesDB
{
    
    private const SEQ_NAME = 'ID_ANIMAL_SEQ';


    

    private function conn()
    {
        $cx = new Conexion();
        return $cx->getConexion(); 
    }

    private static function nv($v)
    {
        return ($v === '' || !isset($v)) ? null : $v;
    }

    /** Pide NEXTVAL a la secuencia */
    private function nextId(): ?int
    {
        if (self::SEQ_NAME === '') return null;
        $cn = $this->conn();
        $st = oci_parse($cn, "SELECT " . self::SEQ_NAME . ".NEXTVAL AS ID FROM DUAL");
        if (!@oci_execute($st)) {
            $e = oci_error($st);
            error_log('[nextId] ' . ($e['message'] ?? 'Error'));
            oci_free_statement($st);
            return null;
        }
        $row = oci_fetch_assoc($st);
        oci_free_statement($st);
        return $row && isset($row['ID']) ? (int)$row['ID'] : null;
    }

    /* ========= Lecturas ========= */

    /**
     * Listado para la tabla: usa la VISTA con nombres resueltos.
     * OJO: OCI devuelve claves en MAYÚSCULAS -> NOMBRE_ANIMAL, ESTADO_NOMBRE, etc.
     */
    public function obtenerAnimales(): array
    {
        $sql = "SELECT 
                    ID_ANIMAL,
                    NOMBRE_ANIMAL,
                    TO_CHAR(FECHA_INGRESO,'YYYY-MM-DD') AS FECHA_INGRESO,
                    EDAD,
                    PESO,
                    ID_ESTADO,
                    ESTADO_NOMBRE
                FROM FIDE_ANIMAL_V
                ORDER BY ID_ANIMAL";
        $cn = $this->conn();
        $st = oci_parse($cn, $sql);
        $ok = @oci_execute($st);
        $out = [];
        if ($ok) {
            while ($r = oci_fetch_assoc($st)) $out[] = $r;
        } else {
            $e = oci_error($st);
            error_log('[obtenerAnimales] ' . ($e['message'] ?? 'Error'));
        }
        oci_free_statement($st);
        return $out;
    }

    
    public function obtenerAnimalPorId(int $id): ?array
    {
        $sql = "SELECT 
                    a.ID_ANIMAL,
                    a.NOMBRE_ANIMAL,
                    TO_CHAR(a.FECHA_INGRESO,'YYYY-MM-DD') AS FECHA_INGRESO,
                    a.EDAD,
                    a.PESO,
                    a.OBSERVACION,
                    a.ID_GENERO,
                    v.GENERO_NOMBRE,
                    a.ID_TIPO,
                    v.TIPO_NOMBRE,
                    a.ID_HABITAT,
                    v.HABITAT_NOMBRE,
                    a.ID_ESTADO,
                    v.ESTADO_NOMBRE,
                    a.ID_RUTINA,
                    a.ID_MARCA_ALIMENTO
                FROM FIDE_ANIMAL_TB a
                LEFT JOIN FIDE_ANIMAL_V v ON v.ID_ANIMAL = a.ID_ANIMAL
                WHERE a.ID_ANIMAL = :id";
        $cn = $this->conn();
        $st = oci_parse($cn, $sql);
        oci_bind_by_name($st, ":id", $id);
        $ok = @oci_execute($st);

        if (!$ok) {
            $e = oci_error($st);
            error_log('[obtenerAnimalPorId] ' . ($e['message'] ?? 'Error'));
            oci_free_statement($st);
            return null;
        }
        $row = oci_fetch_assoc($st) ?: null;
        oci_free_statement($st);
        return $row;
    }

    /* ========= Escrituras por PAQUETE ========= */

    public function insertarAnimal(
        string $nombre,
        ?string $fecha_ingreso,
        $edad,
        $peso,
        string $observacion,
        $id_genero,
        $id_tipo,
        $id_habitat,
        $id_estado,
        $id_rutina,            
        $id_marca_alimento,    
        string $usuario = 'WEB'
    ): bool {
        $cn = $this->conn();
        $id = $this->nextId(); 
        if ($id === null) {
            error_log('[insertarAnimal] No se pudo obtener NEXTVAL de la secuencia');
            return false;
        }

        $plsql = "BEGIN
            FIDE_PROYECTO_FINAL_PCK.FIDE_INSERTAR_ANIMAL_SP(
                :p_id, :p_nombre,
                CASE WHEN :p_fing IS NULL OR :p_fing = '' THEN NULL ELSE TO_DATE(:p_fing,'YYYY-MM-DD') END,
                :p_edad, :p_peso, :p_obs,
                :p_genero, :p_tipo, :p_habitat, :p_estado
            );
        END;";

        $st = oci_parse($cn, $plsql);
        oci_bind_by_name($st, ":p_id",      $id);
        oci_bind_by_name($st, ":p_nombre",  $nombre);
        oci_bind_by_name($st, ":p_fing",    $fecha_ingreso);
        $edad = self::nv($edad);  oci_bind_by_name($st, ":p_edad", $edad);
        $peso = self::nv($peso);  oci_bind_by_name($st, ":p_peso", $peso);
        oci_bind_by_name($st, ":p_obs",     $observacion);
        $id_genero  = self::nv($id_genero);   oci_bind_by_name($st, ":p_genero",  $id_genero);
        $id_tipo    = self::nv($id_tipo);     oci_bind_by_name($st, ":p_tipo",    $id_tipo);
        $id_habitat = self::nv($id_habitat);  oci_bind_by_name($st, ":p_habitat", $id_habitat);
        $id_estado  = self::nv($id_estado);   oci_bind_by_name($st, ":p_estado",  $id_estado);

        $ok = @oci_execute($st, OCI_NO_AUTO_COMMIT);
        if (!$ok) {
            $e = oci_error($st);
            error_log('[insertarAnimal] ' . ($e['message'] ?? 'Error'));
            oci_rollback($cn);
        } else {
            oci_commit($cn);
        }
        oci_free_statement($st);
        return $ok;
    }

    public function actualizarAnimal(
        int $id_animal,
        string $nombre,
        ?string $fecha_ingreso,
        $edad,
        $peso,
        string $observacion,
        $id_genero,
        $id_tipo,
        $id_habitat,
        $id_estado,
        $id_rutina,            
        $id_marca_alimento,    
        string $usuario = 'WEB'
    ): bool {
        $cn = $this->conn();

        $plsql = "BEGIN
            FIDE_PROYECTO_FINAL_PCK.FIDE_MODIFICAR_ANIMAL_SP(
                :p_id, :p_nombre,
                CASE WHEN :p_fing IS NULL OR :p_fing = '' THEN NULL ELSE TO_DATE(:p_fing,'YYYY-MM-DD') END,
                :p_edad, :p_peso, :p_obs,
                :p_genero, :p_tipo, :p_habitat, :p_estado
            );
        END;";

        $st = oci_parse($cn, $plsql);
        oci_bind_by_name($st, ":p_id",      $id_animal);
        oci_bind_by_name($st, ":p_nombre",  $nombre);
        oci_bind_by_name($st, ":p_fing",    $fecha_ingreso);
        $edad = self::nv($edad);  oci_bind_by_name($st, ":p_edad", $edad);
        $peso = self::nv($peso);  oci_bind_by_name($st, ":p_peso", $peso);
        oci_bind_by_name($st, ":p_obs",     $observacion);
        $id_genero  = self::nv($id_genero);   oci_bind_by_name($st, ":p_genero",  $id_genero);
        $id_tipo    = self::nv($id_tipo);     oci_bind_by_name($st, ":p_tipo",    $id_tipo);
        $id_habitat = self::nv($id_habitat);  oci_bind_by_name($st, ":p_habitat", $id_habitat);
        $id_estado  = self::nv($id_estado);   oci_bind_by_name($st, ":p_estado",  $id_estado);

        $ok = @oci_execute($st, OCI_NO_AUTO_COMMIT);
        if (!$ok) {
            $e = oci_error($st);
            error_log('[actualizarAnimal] ' . ($e['message'] ?? 'Error'));
            oci_rollback($cn);
        } else {
            oci_commit($cn);
        }
        oci_free_statement($st);
        return $ok;
    }

    public function eliminarAnimal(int $id, string $usuario = 'WEB'): bool
    {
        $cn = $this->conn();

        $plsql = "BEGIN
            FIDE_PROYECTO_FINAL_PCK.FIDE_ELIMINAR_ANIMAL_SP(:p_id);
        END;";

        $st = oci_parse($cn, $plsql);
        oci_bind_by_name($st, ":p_id", $id);

        $ok = @oci_execute($st, OCI_NO_AUTO_COMMIT);
        if (!$ok) {
            $e = oci_error($st);
            error_log('[eliminarAnimal] ' . ($e['message'] ?? 'Error'));
            oci_rollback($cn);
        } else {
            oci_commit($cn);
        }
        oci_free_statement($st);
        return $ok;
    }

    /* ========= vistas ========= */

    private function simpleList(string $sql, string $tag): array
    {
        $cn = $this->conn();
        $st = oci_parse($cn, $sql);
        $ok = @oci_execute($st);
        $out = [];
        if (!$ok) {
            $e = oci_error($st);
            error_log("[$tag] " . ($e['message'] ?? 'Error'));
            oci_free_statement($st);
            return [];
        }
        while ($row = oci_fetch_assoc($st)) {
            $out[] = $row; 
        }
        oci_free_statement($st);
        return $out;
    }

    public function listarGeneros(): array
    {
        return $this->simpleList(
            "SELECT ID, NOMBRE FROM FIDE_GENERO_V ORDER BY NOMBRE",
            "listarGeneros"
        );
    }

    public function listarTipos(): array
    {
        return $this->simpleList(
            "SELECT ID, NOMBRE FROM FIDE_TIPO_V ORDER BY NOMBRE",
            "listarTipos"
        );
    }

    public function listarHabitats(): array
    {
        return $this->simpleList(
            "SELECT ID, NOMBRE FROM FIDE_HABITAT_V ORDER BY NOMBRE",
            "listarHabitats"
        );
    }

    public function listarEstados(): array
    {
        
        return $this->simpleList(
            "SELECT ID, NOMBRE FROM FIDE_ESTADO_V ORDER BY NOMBRE",
            "listarEstados"
        );
    }

    public function listarRutinas(): array
    {
        return $this->simpleList(
            "SELECT ID, NOMBRE FROM FIDE_RUTINA_V ORDER BY NOMBRE",
            "listarRutinas"
        );
    }

    public function listarMarcas(): array
    {
        return $this->simpleList(
            "SELECT ID, NOMBRE FROM FIDE_MARCA_ALIMENTO_V ORDER BY NOMBRE",
            "listarMarcas"
        );
    }
}
