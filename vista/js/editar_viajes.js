function agregarGasto() {
    const cont = document.getElementById('gastos_container');
    const div = document.createElement('div');
    div.innerHTML = `
        <select name="tipo_gasto[]">
            <option value="">-- tipo --</option>
            <?php foreach ($tiposGasto as $t): ?>
                <option value="<?= $t['id_tipo'] ?>"><?= htmlspecialchars($t['descripcion']) ?></option>
            <?php endforeach; ?>
        </select>
        <input type="text" name="descripcion[]" placeholder="Descripción">
        <input type="number" step="0.01" name="monto[]" placeholder="Monto">
    `;
    cont.appendChild(div);
}

  document.querySelectorAll('.moneda').forEach(input => {
            input.addEventListener('input', () => {
                let value = parseFloat(input.value);
                if (!isNaN(value)) {
                    input.value = value.toFixed(2);
                }
            });
        });
