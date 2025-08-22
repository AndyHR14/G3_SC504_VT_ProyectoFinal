<?php
if (!function_exists('h')) {
    function h($s)
    {
        return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8');
    }
}
$editing = isset($animal) && is_array($animal);
function opt($id, $nombre, $sel)
{
    $s = (string)$sel === (string)$id ? ' selected' : '';
    return "<option value=\"" . h($id) . "\"$s>" . h($nombre) . "</option>";
}
$ID_GENERO = $animal['ID_GENERO'] ?? '';
$ID_TIPO = $animal['ID_TIPO'] ?? '';
$ID_HABITAT = $animal['ID_HABITAT'] ?? '';
$ID_ESTADO = $animal['ID_ESTADO'] ?? '';
$ID_RUTINA = $animal['ID_RUTINA'] ?? '';
$ID_MARCA = $animal['ID_MARCA_ALIMENTO'] ?? '';
?>
<link rel="stylesheet" href="public/styles.css">

<div class="container">
    <div class="toolbar">
        <h1><?= h($titulo_form ?? ($editing ? 'Actualizar' : 'Registrar')) . ($editing ? ' #' . h($animal['ID_ANIMAL']) : '') ?></h1>
        <a class="btn" href="index.php?mod=animales&action=listarAnimales">← Volver</a>
    </div>

    <form method="post" action="index.php?mod=animales&action=<?= $editing ? 'actualizarAnimal' : 'guardarAnimal' ?>" class="card form">
        <?php if ($editing): ?><input type="hidden" name="ID_ANIMAL" value="<?= h($animal['ID_ANIMAL']) ?>"><?php endif; ?>

        <div class="form-grid">
            <label>Nombre <input name="NOMBRE_ANIMAL" value="<?= h($animal['NOMBRE_ANIMAL'] ?? '') ?>" required></label>
            <label>Fecha ingreso <input type="date" name="FECHA_INGRESO" value="<?= h($animal['FECHA_INGRESO'] ?? '') ?>"></label>
            <label>Edad <input type="number" name="EDAD" step="1" value="<?= h($animal['EDAD'] ?? '') ?>"></label>
            <label>Peso (kg) <input type="number" name="PESO" step="0.01" value="<?= h($animal['PESO'] ?? '') ?>"></label>

            <label>Género
                <select name="ID_GENERO">
                    <option value="">-- Seleccione --</option><?php foreach (($generos ?? []) as $g) echo opt($g['ID'], $g['NOMBRE'], $ID_GENERO); ?>
                </select>
            </label>
            <label>Tipo
                <select name="ID_TIPO">
                    <option value="">-- Seleccione --</option><?php foreach (($tipos ?? []) as $t) echo opt($t['ID'], $t['NOMBRE'], $ID_TIPO); ?>
                </select>
            </label>
            <label>Hábitat
                <select name="ID_HABITAT">
                    <option value="">-- Seleccione --</option><?php foreach (($habitats ?? []) as $hbt) echo opt($hbt['ID'], $hbt['NOMBRE'], $ID_HABITAT); ?>
                </select>
            </label>
            <label>Estado
                <select name="ID_ESTADO">
                    <option value="">-- Seleccione --</option><?php foreach (($estados ?? []) as $es) echo opt($es['ID'], $es['NOMBRE'], $ID_ESTADO); ?>
                </select>
            </label>
            <label>Rutina
                <select name="ID_RUTINA">
                    <option value="">-- Seleccione --</option><?php foreach (($rutinas ?? []) as $ru) echo opt($ru['ID'], $ru['NOMBRE'], $ID_RUTINA); ?>
                </select>
            </label>
            <label>Marca alimento
                <select name="ID_MARCA_ALIMENTO">
                    <option value="">-- Seleccione --</option><?php foreach (($marcas ?? []) as $ma) echo opt($ma['ID'], $ma['NOMBRE'], $ID_MARCA); ?>
                </select>
            </label>

            <label style="grid-column:1/-1">Observación
                <textarea name="OBSERVACION"><?= h($animal['OBSERVACION'] ?? '') ?></textarea>
            </label>

            <label>Usuario bitácora <input name="USUARIO" placeholder="tu usuario"></label>
        </div>

        <div class="mt-16" style="display:flex;gap:10px">
            <button class="btn btn--primary" type="submit"><?= $editing ? 'Actualizar' : 'Guardar' ?></button>
            <a class="btn btn--ghost" href="index.php?mod=animales&action=listarAnimales">Cancelar</a>
        </div>
    </form>
</div>