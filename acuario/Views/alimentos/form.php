<?php
if (!function_exists('h')) {
    function h($s){ return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8'); }
}
$editing = isset($alimento) && is_array($alimento);
?>
<link rel="stylesheet" href="public/styles.css">

<div class="container">
    <div class="toolbar">
        <h1><?= h($titulo_form ?? ($editing ? 'Actualizar' : 'Registrar')) . ($editing ? ' #' . h($alimento['ID_MARCA_ALIMENTO']) : '') ?></h1>
        <a class="btn" href="index.php?mod=alimentos&action=listarMarcas">← Volver</a>
    </div>

    <form method="post" action="index.php?mod=alimentos&action=<?= $editing ? 'actualizarMarca' : 'guardarMarca' ?>" class="card form">
        <?php if ($editing): ?>
            <input type="hidden" name="ID_MARCA_ALIMENTO" value="<?= h($alimento['ID_MARCA_ALIMENTO']) ?>">
        <?php endif; ?>

        <div class="form-grid">
            <label>Nombre
                <input name="NOMBRE" value="<?= h($alimento['NOMBRE'] ?? '') ?>" required>
            </label>

            <label style="grid-column:1/-1">Descripción
                <textarea name="DESCRIPCION"><?= h($alimento['DESCRIPCION'] ?? '') ?></textarea>
            </label>

            <label>Estado
                <select name="ID_ESTADO" required>
                    <option value="">-- Seleccione --</option>
                    <?php
                    $sel = $alimento['ID_ESTADO'] ?? '';
                    foreach (($estados ?? []) as $es) {
                        $s = ((string)$sel === (string)$es['ID']) ? ' selected' : '';
                        echo '<option value="'.h($es['ID']).'"'.$s.'>'.h($es['NOMBRE']).'</option>';
                    }
                    ?>
                </select>
            </label>
        </div>

        <div class="mt-16" style="display:flex;gap:10px">
            <button class="btn btn--primary" type="submit"><?= $editing ? 'Actualizar' : 'Guardar' ?></button>
            <a class="btn btn--ghost" href="index.php?mod=alimentos&action=listarMarcas">Cancelar</a>
        </div>
    </form>
</div>
