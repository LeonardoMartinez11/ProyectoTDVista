<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../diseño/menu.css">
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center mb-5"><i class="fa-solid fa-user-shield me-2"></i>Panel de Administración</h2>

        <div class="row g-4 justify-content-center">
            <!-- Usuarios -->
            <div class="col-md-3">
                <div class="card text-center p-4 shadow-sm">
                    <i class="fa-solid fa-users"></i>
                    <div class="card-body">
                        <h5 class="card-title mb-3">Gestión de Usuarios</h5>
                        <p class="card-text text-muted">Crear, editar o eliminar usuarios del sistema.</p>
                        <a href="admin_usuarios.php" class="btn btn-primary">Entrar</a>
                    </div>
                </div>
            </div>

            <!-- Unidades -->
            <div class="col-md-3">
                <div class="card text-center p-4 shadow-sm">
                    <i class="fa-solid fa-truck"></i>
                    <div class="card-body">
                        <h5 class="card-title mb-3">Gestión de Unidades</h5>
                        <p class="card-text text-muted">Registrar, modificar o dar de baja unidades.</p>
                        <a href="admin_unidades.php" class="btn btn-primary">Entrar</a>
                    </div>
                </div>
            </div>

            <!-- Choferes -->
            <div class="col-md-3">
                <div class="card text-center p-4 shadow-sm">
                    <i class="fa-solid fa-id-card"></i>
                    <div class="card-body">
                        <h5 class="card-title mb-3">Gestión de Choferes</h5>
                        <p class="card-text text-muted">Administrar choferes activos o inactivos.</p>
                        <a href="admin_choferes.php" class="btn btn-primary">Entrar</a>
                    </div>
                </div>
            </div>

        <!-- Botón de salida -->
        <div class="text-center mt-5">
            <a href="../../index.html" class="btn btn-outline-danger px-4 py-2">
                <i class="fa-solid fa-right-from-bracket me-2"></i>Salir
            </a>
        </div>

        <footer class="text-muted mt-4">
            <p>© 2025 Transportes Dubón — Panel de Administración</p>
        </footer>
    </div>
</body>
</html>
