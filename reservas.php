<?php
require_once 'php/config.php'; 

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: inicio.html?error=reservas_requiere_login");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservas de Hotel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="styles/reservas.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Amethysta&family=Montaga&display=swap" rel="stylesheet">
    
    <div class="container-fluid toolbar">
        <h5>@RockStay - Reservas</h5>
        <a href="index.php" class="btn btn-outline-light btn-sm">Volver a Inicio</a>
    </div>

    <script src="https://www.paypal.com/sdk/js?client-id=YOUR_CLIENT_ID&currency=USD"></script>

</head>
<body>

<div class="container pt-5">
    <div id="section-1" class="reservation-card">
    <h2 class="mb-4">1.- Selección de fechas</h2>
    <form class="row g-3" id="form-fechas">
        <div class="col-md-6">
            <label for="fechaEntrada" class="form-label">Fecha de entrada</label>
            <div class="input-group">
                <input type="date" class="form-control" id="fechaEntrada" name="fecha_entrada" required>
            </div>
        </div>
        <div class="col-md-6">
            <label for="fechaSalida" class="form-label">Fecha de salida</label>
            <div class="input-group">
                <input type="date" class="form-control" id="fechaSalida" name="fecha_salida" required>
            </div>
        </div>
        <div class="col-12 mb-3">
            <label for="huespedes" class="form-label">Huéspedes</label>
            <select id="huespedes" class="form-select" name="huespedes">
                <option value="1">1 adulto</option>
                <option value="2">2 adultos</option>
                <option value="3">3 adultos</option>
            </select>
        </div>
        <div class="col-12 text-end">
            <button type="button" class="btn btn-dark" onclick="showSection(2)">Siguiente</button>
        </div>
    </form>
    </div>

    <div id="section-2" class="reservation-card" style="display: none;">
        <h2 class="mb-4">2.- Selección de habitación</h2>
        <div class="list-group">
            <label class="list-group-item d-flex justify-content-between align-items-center">
                <div>
                    <input class="form-check-input me-1 room-option" type="radio" name="room" value="1" data-price="120" data-room-name="Habitación Estándar" checked>
                    <strong>Habitación estándar</strong>
                    <div class="text-muted small">1 cama doble + vista al jardín</div>
                </div>
                <div>
                    <strong>$120 / noche</strong>
                </div>
            </label>
            <label class="list-group-item d-flex justify-content-between align-items-center">
                <div>
                    <input class="form-check-input me-1 room-option" type="radio" name="room" value="2" data-price="180" data-room-name="Habitación Superior">
                    <strong>Habitación superior</strong>
                    <div class="text-muted small">1 cama King Size + balcón</div>
                </div>
                <strong>$180 / noche</strong>
            </label>
            <label class="list-group-item d-flex justify-content-between align-items-center">
                <div> 
                    <input class="form-check-input me-1 room-option" type="radio" name="room" value="3" data-price="300" data-room-name="Suite Todo Incluido">
                    <strong>Suite todo incluido</strong>
                    <div class="text-muted small">Desayuno, almuerzo, cena, spa</div>
                </div>
                <strong>$300 / noche</strong>
            </label>
        </div>
        <div class="d-flex justify-content-between mt-3">
            <button type="button" class="btn btn-secondary" onclick="showSection(1)">Anterior</button>
            <button type="button" class="btn btn-dark" onclick="showSection(3)">Siguiente</button>
        </div>
    </div>

    <div id="section-3" class="reservation-card" style="display: none;">
        <h2 class="mb-4">3.- Tipo de paquete</h2>
        <div class="list-group list-group-flush mb-4">
            <label class="list-group-item">
                <input class="form-check-input me-1 package-option" type="radio" name="paquete_tipo" value="standard" data-package-name="Estándar" checked>
                <strong>Paquete Estándar</strong>
                <div class="text-muted small">Solo habitación y servicios básicos del hotel.</div>
            </label>
            <label class="list-group-item">
                <input class="form-check-input me-1 package-option" type="radio" name="paquete_tipo" value="all_inclusive" data-package-name="Todo Incluido">
                <strong>Paquete Todo Incluido</strong>
                <div class="text-muted small">Todas las comidas y bebidas sin costo extra.</div>
            </label>
            <label class="list-group-item">
                <input class="form-check-input me-1 package-option" type="radio" name="paquete_tipo" value="custom" id="radio-custom-package" data-package-name="Personalizado">
                <strong>Paquete Personalizado</strong>
                <div class="text-muted small">Selecciona los servicios adicionales que desees.</div>
            </label>
        </div>

        <div id="extras-container" class="border p-3 rounded" style="display: none;">
            <h5 class="mb-3">Selecciona tus extras:</h5>
            <div class="form-check">
                <input class="form-check-input extra-option" type="checkbox" name="extras[]" value="1" data-price="80.00" id="extra-spa">
                <label class="form-check-label" for="extra-spa">
                    Acceso a Spa ($80.00)
                </label>
            </div>
            <div class="form-check">
                <input class="form-check-input extra-option" type="checkbox" name="extras[]" value="2" data-price="50.00" id="extra-palapas">
                <label class="form-check-label" for="extra-palapas">
                    Uso de Palapas ($50.00)
                </label>
            </div>
            <div class="form-check">
                <input class="form-check-input extra-option" type="checkbox" name="extras[]" value="3" data-price="60.00" id="extra-cena">
                <label class="form-check-label" for="extra-cena">
                    Cena Romántica ($60.00)
                </label>
            </div>
        </div>

        <div class="mt-4 text-center">
            <button type="button" class="btn btn-dark" onclick="showSection(4)">Siguiente</button>
        </div>
    </div>

    <div id="section-4" class="reservation-card" style="display: none;">
    <h2 class="mb-4">4.- Resumen y Confirmación</h2>

    <form action="php/confirmar_reserva.php" method="POST">
        
        <input type="hidden" name="fecha_entrada" id="input-fecha-entrada">
        <input type="hidden" name="fecha_salida" id="input-fecha-salida">
        <input type="hidden" name="habitacion_id" id="input-habitacion-id"> 
        <input type="hidden" name="paquete_tipo" id="input-paquete-tipo">
        
        <input type="hidden" name="extra_ids_json" id="input-extra-ids-json"> 
        
        <div class="payment-summary bg-light p-3 rounded">
            <div class="mb-3">
                <strong>1.- Selección de fechas</strong>
                <p id="summary-dates" class="mb-0 text-muted">dd/mm/aaaa - dd/mm/aaaa</p>
            </div>
            
            <div class="d-flex justify-content-between mb-2">
                <strong>2.- Selección de habitación</strong>
                <span id="summary-room-price" class="fw-bold"></span>
            </div>
            
            <div class="mb-3">
                <strong>3.- Tipo de paquete</strong>
                <div id="summary-package-details">
                    </div>
            </div>

            <hr>

            <div class="d-flex justify-content-between mt-2">
                <span>Subtotal (sin IVA):</span>
                <span id="summary-subtotal">$0.00</span>
            </div>
            <div class="d-flex justify-content-between">
                <span>Impuesto (IVA 16%):</span>
                <span id="summary-iva">$0.00</span>
            </div>
            <div class="d-flex justify-content-between mb-3">
                <span>Total a pagar:</span>
                <span id="summary-total" class="fw-bold fs-5">$0.00</span>
            </div>
        </div>

        <div id="confirm-button-container" class="mt-4 text-center">
            <button type="submit" class="btn btn-dark btn-lg">Confirmar y Pagar</button>
        </div>
        
    </form>
    
    <div class="mt-3 text-center">
        <button type="button" class="btn btn-link" onclick="showSection(3)">Modificar Paquete</button>
    </div>
</div>

</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="js/reservas.js"></script>

</body>
</html>