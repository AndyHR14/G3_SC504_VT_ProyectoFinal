<?php
require_once __DIR__ . '/../Models/facturas_db.php';

class FacturasController
{
    public function index()
    {
        $m = new FacturasDB();
        $facturas = $m->listarFacturas();
        require __DIR__ . '/../Views/facturas/list.php';
    }

    public function crear()
    {
        $m = new FacturasDB();
        $usuarios    = $m->listarUsuarios();
        $metodosPago = $m->listarMetodosPago();
        $estados     = $m->listarEstados();
        $productos   = $m->listarProductos();
        $factura = null;
        $detalles = [];
        $titulo_form = "Registrar Factura";
        require __DIR__ . '/../Views/facturas/form.php';
    }

    public function guardar()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $m = new FacturasDB();
            $ok = $m->insertarFactura(
                $_POST['FECHA_REGISTRO'] ?? null,
                $_POST['MONTO_TOTAL'] ?? null,
                $_POST['SUBTOTAL'] ?? null,
                $_POST['IVA'] ?? null,
                $_POST['DESCUENTO'] ?? null,
                $_POST['ID_USUARIO'] ?? null,
                $_POST['ID_METODO_PAGO'] ?? null,
                $_POST['ID_ESTADO'] ?? null
            );
            header('Location: index.php?mod=facturas&action=listarFacturas' . ($ok ? '' : '&msg=error'));
            exit;
        }
        header('Location: index.php?mod=facturas&action=listarFacturas&msg=error'); exit;
    }

    public function editar()
    {
        $id = $_GET['id'] ?? null;
        if (!is_numeric($id)) {
            header('Location: index.php?mod=facturas&action=listarFacturas&msg=id_no_valido'); exit;
        }
        $m = new FacturasDB();
        $factura = $m->obtenerFacturaPorId((int)$id);
        if (!$factura) {
            header('Location: index.php?mod=facturas&action=listarFacturas&msg=no_encontrado'); exit;
        }
        $detalles    = $m->listarDetalles((int)$id);
        $usuarios    = $m->listarUsuarios();
        $metodosPago = $m->listarMetodosPago();
        $estados     = $m->listarEstados();
        $productos   = $m->listarProductos();
        $titulo_form = "Actualizar Factura";
        require __DIR__ . '/../Views/facturas/form.php';
    }

    public function actualizar()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ID_FACTURA'])) {
            $m = new FacturasDB();
            $ok = $m->actualizarFactura(
                (int)$_POST['ID_FACTURA'],
                $_POST['FECHA_REGISTRO'] ?? null,
                $_POST['MONTO_TOTAL'] ?? null,
                $_POST['SUBTOTAL'] ?? null,
                $_POST['IVA'] ?? null,
                $_POST['DESCUENTO'] ?? null,
                $_POST['ID_USUARIO'] ?? null,
                $_POST['ID_METODO_PAGO'] ?? null,
                $_POST['ID_ESTADO'] ?? null
            );
            header('Location: index.php?mod=facturas&action=listarFacturas' . ($ok ? '&msg=actualizado' : '&msg=error'));
            exit;
        }
        header('Location: index.php?mod=facturas&action=listarFacturas&msg=error'); exit;
    }

    public function eliminar()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ID_FACTURA'])) {
            $m = new FacturasDB();
            $ok = $m->eliminarFactura((int)$_POST['ID_FACTURA']);
            header('Location: index.php?mod=facturas&action=listarFacturas' . ($ok ? '&msg=eliminado' : '&msg=error'));
            exit;
        }
        header('Location: index.php?mod=facturas&action=listarFacturas&msg=error'); exit;
    }


    public function guardarDetalle()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ID_FACTURA'])) {
            $m = new FacturasDB();
            $ok = $m->upsertDetalle(
                (int)$_POST['ID_FACTURA'],
                (int)$_POST['ID_PRODUCTO'],
                $_POST['CANTIDAD'] ?? null,
                $_POST['PRECIO_UNITARIO'] ?? null,
                $_POST['TOTAL'] ?? null,
                $_POST['ID_ESTADO_DET'] ?? null
            );
            header('Location: index.php?mod=facturas&action=editarFactura&id='.(int)$_POST['ID_FACTURA'] . ($ok ? '&msg=detalle_ok' : '&msg=error'));
            exit;
        }
        header('Location: index.php?mod=facturas&action=listarFacturas&msg=error'); exit;
    }

    public function eliminarDetalle()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ID_FACTURA'], $_POST['ID_PRODUCTO'])) {
            $m = new FacturasDB();
            $ok = $m->eliminarDetalle((int)$_POST['ID_FACTURA'], (int)$_POST['ID_PRODUCTO']);
            header('Location: index.php?mod=facturas&action=editarFactura&id='.(int)$_POST['ID_FACTURA'] . ($ok ? '&msg=detalle_borrado' : '&msg=error'));
            exit;
        }
        header('Location: index.php?mod=facturas&action=listarFacturas&msg=error'); exit;
    }
}
