document.addEventListener("DOMContentLoaded", () => {
    const tabla = document.querySelector("#tablaUsuarios tbody");
    const form = document.getElementById("formUsuario");
    const btnCancelar = document.getElementById("btnCancelar");
    const url = "../controlador/ControllerAdmin.php";

    const cargarUsuarios = async () => {
        const res = await fetch(`${url}?accion=listar`);
        const data = await res.json();
        tabla.innerHTML = "";
        data.forEach(u => {
            const fila = document.createElement("tr");
            fila.innerHTML = `
                <td>${u.id_usuario}</td>
                <td>${u.nombre}</td>
                <td>${u.correo}</td>
                <td>${u.rol}</td>
                <td>
                    <button class="btn btn-warning btn-sm" onclick="editarUsuario(${u.id_usuario})">Editar</button>
                    <button class="btn btn-danger btn-sm" onclick="eliminarUsuario(${u.id_usuario})">Eliminar</button>
                </td>
            `;
            tabla.appendChild(fila);
        });
    };

    form.addEventListener("submit", async e => {
        e.preventDefault();
        const id = document.getElementById("id_usuario").value;
        const data = {
            nombre: document.getElementById("nombre").value,
            correo: document.getElementById("correo").value,
            password: document.getElementById("password").value,
            rol: document.getElementById("rol").value
        };

        const metodo = id ? "actualizar&id=" + id : "crear";
        const res = await fetch(`${url}?accion=${metodo}`, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(data)
        });

        await res.json();
        alert("Usuario guardado correctamente.");
        form.reset();
        cargarUsuarios();
    });

    btnCancelar.addEventListener("click", () => form.reset());

    window.editarUsuario = async (id) => {
        const res = await fetch(`${url}?accion=listar`);
        const data = await res.json();
        const u = data.find(x => x.id_usuario == id);
        if (u) {
            document.getElementById("id_usuario").value = u.id_usuario;
            document.getElementById("nombre").value = u.nombre;
            document.getElementById("correo").value = u.correo;
            document.getElementById("rol").value = u.rol;
        }
    };

    window.eliminarUsuario = async (id) => {
        if (confirm("¿Deseas eliminar este usuario?")) {
            await fetch(`${url}?accion=eliminar&id=${id}`);
            alert("Usuario eliminado.");
            cargarUsuarios();
        }
    };

    cargarUsuarios();
});
