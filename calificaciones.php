<?php
require_once 'php/config.php';

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: inicio.html?error=requiere_login");
    exit;
}

$usuario_id = $_SESSION["id"];
$reservas_historial = [];
$reservas_elegibles_calificar = [];
$link = $GLOBALS['link']; 

$sql_historial = "SELECT r.id, r.fecha_entrada, r.noches, r.paquete_nombre, r.precio_total, r.estado, h.nombre as habitacion_nombre
                  FROM reservas r
                  JOIN habitaciones h ON r.habitacion_id = h.id
                  WHERE r.usuario_id = ? 
                  ORDER BY r.fecha_reserva DESC";

if ($stmt_historial = mysqli_prepare($link, $sql_historial)) {
    mysqli_stmt_bind_param($stmt_historial, "i", $usuario_id);
    mysqli_stmt_execute($stmt_historial);
    $result_historial = mysqli_stmt_get_result($stmt_historial);
    while ($row = mysqli_fetch_assoc($result_historial)) {
        $reservas_historial[] = $row;
    }
    mysqli_stmt_close($stmt_historial);
}

$sql_elegibles = "SELECT r.id, r.fecha_entrada, r.fecha_salida, h.nombre as habitacion_nombre
                  FROM reservas r
                  JOIN habitaciones h ON r.habitacion_id = h.id
                  LEFT JOIN calificaciones c ON r.id = c.reserva_id
                  WHERE r.usuario_id = ?
                  AND r.estado = 'confirmada'
                  AND r.fecha_salida < CURDATE() 
                  AND c.reserva_id IS NULL       
                  ORDER BY r.fecha_salida DESC";

if ($stmt_elegibles = mysqli_prepare($link, $sql_elegibles)) {
    mysqli_stmt_bind_param($stmt_elegibles, "i", $usuario_id);
    mysqli_stmt_execute($stmt_elegibles);
    $result_elegibles = mysqli_stmt_get_result($stmt_elegibles);
    while ($row = mysqli_fetch_assoc($result_elegibles)) {
        $reservas_elegibles_calificar[] = $row;
    }
    mysqli_stmt_close($stmt_elegibles);
}

mysqli_close($link);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calificaciones de Hotel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="styles/calificaciones.css">
</head>
<body>

    <div class="container-fluid toolbar">
        <h5>@Hotel - Calificaciones</h5>
        <div class="d-flex align-items-center">
            <span class="text-white me-3">Bienvenido, **<?php echo htmlspecialchars($_SESSION['nombre']); ?>**!</span>
            <a href="logout.php" class="btn btn-outline-light btn-sm">Cerrar Sesión</a>
        </div>
    </div>

    <div class="container py-5">
        
        <div class="content-box mb-5">
            <h4 class="mb-3">Historial de reservaciones</h4>
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Fecha de Entrada y Noches</th>
                            <th>Habitación</th>
                            <th>Paquete</th>
                            <th>Total Pagado</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody id="historial-body">
                        <?php if (!empty($reservas_historial)): ?>
                            <?php foreach ($reservas_historial as $reserva): 
                                $badge_class = ($reserva['estado'] == 'confirmada') ? 'bg-success' : (($reserva['estado'] == 'pendiente') ? 'bg-warning' : 'bg-danger');
                            ?>
                                <tr>
                                    <td>
                                        <?php echo date('d/m/Y', strtotime($reserva['fecha_entrada'])); ?> 
                                        (<?php echo $reserva['noches']; ?> Noches)
                                    </td>
                                    <td><?php echo htmlspecialchars($reserva['habitacion_nombre']); ?></td> 
                                    <td><?php echo htmlspecialchars($reserva['paquete_nombre']); ?></td>
                                    <td>$<?php echo number_format($reserva['precio_total'], 2); ?></td>
                                    <td>
                                        <span class="badge <?php echo $badge_class; ?>">
                                            <?php echo ucfirst($reserva['estado']); ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted">Aún no tienes reservaciones registradas.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="content-box">
            <h4 class="mb-4">Calificaciones Pendientes</h4>
            
            <form action="guardar_calificacion.php" method="POST">
                <div class="mb-3">
                    <label for="reserva-select" class="form-label">Selecciona una Reserva para Calificar</label>
                    <select class="form-select form-select-lg" id="reserva-select" name="reserva_id" required>
                        <option value="" disabled selected>Selecciona una reserva para calificar</option>
                        
                        <?php if (!empty($reservas_elegibles_calificar)): ?>
                            <?php foreach ($reservas_elegibles_calificar as $reserva_elegible): ?>
                                <option value="<?php echo $reserva_elegible['id']; ?>">
                                    <?php echo htmlspecialchars($reserva_elegible['habitacion_nombre']); ?> (Salida: <?php echo date('d/m/Y', strtotime($reserva_elegible['fecha_salida'])); ?>)
                                </option>
                            <?php endforeach; ?>
                        <?php else: ?>
                             <option value="" disabled>No hay reservas elegibles pendientes de calificar.</option>
                        <?php endif; ?>
                    </select>
                </div>
                
                <div class="mb-3">
                    <label for="calificacion-input" class="form-label">Calificación (1 - 5)</label>
                    <input type="number" class="form-control form-control-lg" id="calificacion-input" name="calificacion" min="1" max="5" placeholder="Ingresa tu calificación (ej: 5)" required>
                </div>
                <div class="mb-3">
                    <label for="comentario-input" class="form-label">Comentario (Opcional)</label>
                    <textarea class="form-control" id="comentario-input" name="comentario" rows="3"></textarea>
                </div>

                <div class="text-end mt-4">
                    <button type="submit" class="btn btn-dark" 
                        <?php echo empty($reservas_elegibles_calificar) ? 'disabled' : ''; ?>
                    >Enviar Calificación</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>