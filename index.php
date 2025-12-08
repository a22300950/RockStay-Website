<?php 
require_once 'php/config.php'; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Amethysta&family=Montaga&display=swap" rel="stylesheet">
    <title>RockStay- Inicio</title>

    <div class="container-fluid toolbar">
        <div class="col-6 text-center">
            <h5>@RockStay - Inicio</h5>
        </div>
        <div class="col-6 text-end botones">
            <a href="index.php" class="link-dark link-underline link-underline-opacity-0 ms-5">Inicio</a>
            <a href="reservas.php" class="link-dark link-underline link-underline-opacity-0 ms-5">Reservas</a>
            <a href="calificaciones.php" class="link-dark link-underline link-underline-opacity-0 ms-5">Calificaciones</a>
            
            <?php if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true): ?>
                <span class="text-white ms-5">¡Hola, <?php echo htmlspecialchars($_SESSION['nombre']); ?>!</span>
                <a href="php/logout.php" class="btn btn-dark btn-sm ms-3">Cerrar Sesión</a>
            <?php else: ?>
                <a href="inicio.html" class="btn btn-secondary btn-sm ms-5">Login/Registro</a>
            <?php endif; ?>
        </div>
    </div>
</head>
<body>
    <div class="container-fluid seccion1">
        <div class="row w-75">
            <div class="col">
                <div class="card">
                    <div class="card-body d-flex justify-content-between align-items-center m-5">         
                        <div>
                            <h5 class="card-title pb-3">Bienvenido - Reserva tu estancia perfecta</h5>
                            <p class="card-text pb-3">Explora habitaciones, paquetes y promociones. Reserva fácil y seguro</p>
                            <a href="reservas.php" class="btn btn-outline-dark">Reserva</a>
                            <a href="calificaciones.php" class="btn btn-secondary">Calificaciones</a>
                        </div>
                        <img src="res/img/icon/palm.png" alt="palm.png">            
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid w-50 seccion2">
        <div class="container text-center mt-5">
            <div class="row g-2 align-items-stretch">
                <div class="col-6">
                    <div class="p-3 h-100">
                        <div class="card h-100">
                            <div class="card-body text-start">          
                                <div>
                                    <h5 class="card-title pb-3">Informacion del hotel</h5>
                                    <p class="card-text pb-3">Paseo de los Cocoteros 19, villa 8, Nuevo Vallarta, C.P. 63735 </p>
                                </div>           
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="p-3 h-100">
                        <div class="card h-100">
                            <div class="card-body text-start">          
                                <div>
                                    <h5 class="card-title pb-3">Reservas</h5>
                                    <p class="card-text pb-3">Nuestro motor de reservas en line aplicara automaticamente todas las ofewrtas combinables para ofrecerte la mejor tarifa posible</p>
                                </div>           
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="p-3 h-100">
                        <div class="card h-100">
                            <div class="card-body text-start">          
                                <div>
                                    <h5 class="card-title pb-3">Promociones</h5>
                                    <p class="card-text pb-3">Descuentos por temporada, paquetes todo incluido y ofertas por anticipado</p>
                                </div>           
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="p-3 h-100">
                        <div class="card h-100">
                            <div class="card-body text-start">          
                                <div>
                                    <h5 class="card-title pb-3">Contacto</h5>
                                    <p class="card-text pb-3">Hotel: +52-332-226-8470</p>
                                </div>           
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>