document.getElementById('loginForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const data = {
        usuario: document.getElementById('usuario').value,
        contrasena: document.getElementById('contrasena').value
    };

    fetch('../../controlador/LoginController.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
    })
    .then(res => res.json())
    .then(res => {
        if (res.success) {
            localStorage.setItem('rol', res.rol);
            localStorage.setItem('usuario', res.usuario);

            alert('Bienvenido, ' + res.nombre_completo + ' (' + res.rol + ')');

            switch (res.rol) {
                case 'Administrador':
                    window.location.href = '../admin/menu.php';
                    break;
                case 'Contabilidad':
                    window.location.href = '../contabilidad/vista_viajes.php';
                    break;
                case 'Logistica':
                    window.location.href = '../logistica/vista_viajes.php';
                    break;
                default:
                    alert('Rol no reconocido');
            }
        } else {
            alert(res.message);
        }
    })
    .catch(error => console.error('Error:', error));
});
