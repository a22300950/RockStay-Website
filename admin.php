<?php
// admin.php
require_once 'php/config.php';

// Verificar que el usuario esté logueado Y que sea administrador
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || !$_SESSION['es_admin']) {
    // Redirigir si no está logueado o no es administrador
    header("location: inicio.html?error=acceso_denegado_admin");
    exit;
}

$nombre_admin = $_SESSION['nombre'];
$link = $GLOBALS['link'];
$reservas = [];

// Lógica para obtener TODAS las reservas (para la sección principal)
$sql = "SELECT r.id, u.nombre as cliente_nombre, r.fecha_entrada, r.fecha_salida, r.noches, 
               h.nombre as habitacion_nombre, r.precio_total, r.estado 
        FROM reservas r
        JOIN usuarios u ON r.usuario_id = u.id
        JOIN habitaciones h ON r.habitacion_id = h.id
        ORDER BY r.fecha_reserva DESC";

$result = mysqli_query($link, $sql);
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $reservas[] = $row;
    }
}
// La conexión se cierra al final del HTML si no se usa más

// ... (El resto del HTML sigue abajo) ...
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Administrador</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="styles/admin.css">
</head>
<body>

    <div class="container-fluid toolbar">
        <h5>@Hotel - Panel administrador</h5>
        <a href="index.html" class="btn btn-outline-light btn-sm">Volver a Inicio</a>
    </div>

    <div class="container py-5">
        <div class="row">

            <div class="col-md-4 mb-4">
                <div class="content-box mb-4">
                    <h4 class="mb-3">Gestión rápida</h4>
                    <div class="d-grid">
                        <button class="btn btn-secondary text-white btn-sm">Actualizar disponibilidad</button>
                    </div>
                </div>

                <div class="content-box">
                    <h4 class="mb-3">Servicios adicionales</h4>
                    <form id="servicios-form">
                        <div class="mb-3 text-muted small">
                            Servicios disponibles (cargar desde BD).
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-secondary text-white btn-sm">Guardar</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="table-responsive">
    <table class="table table-striped table-hover align-middle">
        <thead class="table-light">
            <tr>
                <th>ID</th>
                <th>Cliente</th>
                <th>Habitación</th>
                <th>Fechas</th>
                <th>Total</th>
                <th>Estado</th>
                <th>Acción</th>
            </tr>
        </thead>
        <tbody id="reservas-body">
            <?php if (!empty($reservas)): ?>
                <?php foreach ($reservas as $reserva): ?>
                    <tr>
                        <td><?php echo $reserva['id']; ?></td>
                        <td><?php echo htmlspecialchars($reserva['cliente_nombre']); ?></td>
                        <td><?php echo htmlspecialchars($reserva['habitacion_nombre']); ?></td>
                        <td><?php echo date('d/m/Y', strtotime($reserva['fecha_entrada'])) . ' a ' . date('d/m/Y', strtotime($reserva['fecha_salida'])); ?></td>
                        <td>$<?php echo number_format($reserva['precio_total'], 2); ?></td>
                        <td>
                            <span class="badge 
                                <?php 
                                    if ($reserva['estado'] == 'confirmada') echo 'bg-success';
                                    else if ($reserva['estado'] == 'pendiente') echo 'bg-warning';
                                    else echo 'bg-danger';
                                ?>
                            ">
                                <?php echo ucfirst($reserva['estado']); ?>
                            </span>
                        </td>
                        <td>
                            <form action="php/admin_action.php" method="POST" class="d-inline">
                                <input type="hidden" name="reserva_id" value="<?php echo $reserva['id']; ?>">
                                <?php if ($reserva['estado'] != 'cancelada'): ?>
                                    <input type="hidden" name="action" value="cancelar">
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Seguro que quieres CANCELAR esta reserva?')">Cancelar</button>
                                <?php endif; ?>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" class="text-center text-muted">No hay reservas registradas.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>