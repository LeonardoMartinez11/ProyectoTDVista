<?php include_once __DIR__ . "/../includes/header.php"; ?>
<div class="container mt-4">
    <h2 class="mb-4 text-center">Gestión de Usuarios del Sistema</h2>

    <div class="card p-3 shadow-sm mb-4">
        <form id="formUsuario">
            <input type="hidden" id="id_usuario">
            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label">Nombre de Usuario</label>
                    <input type="text" id="nombre" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Correo Electrónico</label>
                    <input type="email" id="correo" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Contraseña</label>
                    <input type="password" id="password" class="form-control" placeholder="********">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label">Rol</label>
                    <select id="rol" class="form-select">
                        <option value="admin">Administrador</option>
                        <option value="logistica">Logística</option>
                        <option value="contabilidad">Contabilidad</option>
                    </select>
                </div>
            </div>

            <button type="submit" class="btn btn-success">Guardar</button>
            <button type="button" class="btn btn-secondary" id="btnCancelar">Cancelar</button>
        </form>
    </div>

    <div class="card p-3 shadow-sm">
        <table class="table table-striped" id="tablaUsuarios">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Correo</th>
                    <th>Rol</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<script src="../js/admin.js"></script>
<?php include_once __DIR__ . "/../includes/footer.php"; ?>
