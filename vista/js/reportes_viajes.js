document.addEventListener("DOMContentLoaded", () => {
    const searchInput = document.getElementById("search");
    const choferSelect = document.getElementById("chofer");
    const estadoSelect = document.getElementById("estado");
    const fechaDesde = document.getElementById("fecha-desde");
    const fechaHasta = document.getElementById("fecha-hasta");
    const limpiarBtn = document.getElementById("btn-limpiar");
    const filas = document.querySelectorAll(".viaje-row");

    const totalIngresosEl = document.getElementById("total-ingresos");
    const totalPagoChoferEl = document.getElementById("total-pago-chofer");
    const totalGastosEl = document.getElementById("total-gastos");
    const totalRentabilidadEl = document.getElementById("total-rentabilidad");

    // =========================
    // Cálculo de totales
    // =========================
    function calcularTotales() {
        let totalIngresos = 0;
        let totalPagoChofer = 0;
        let totalOtrosGastos = 0;
        let totalRentabilidad = 0;

        filas.forEach(fila => {
            if (fila.style.display !== "none") {
                totalIngresos += parseFloat(fila.dataset.ingresos || 0);
                totalPagoChofer += parseFloat(fila.dataset.pago || 0);
                totalOtrosGastos += parseFloat(fila.dataset.gastos || 0);
                totalRentabilidad += parseFloat(fila.dataset.rentabilidad || 0);
            }
        });

        totalIngresosEl.textContent = `Q${totalIngresos.toFixed(2)}`;
        totalPagoChoferEl.textContent = `Q${totalPagoChofer.toFixed(2)}`;
        totalGastosEl.textContent = `Q${totalOtrosGastos.toFixed(2)}`;
        totalRentabilidadEl.textContent = `Q${totalRentabilidad.toFixed(2)}`;

        actualizarGrafico(totalIngresos, totalPagoChofer, totalOtrosGastos);
    }

    // =========================
    // Filtro de registros
    // =========================
    function filtrar() {
        const texto = searchInput.value.toLowerCase().trim();
        const chofer = choferSelect.value.toLowerCase();
        const estado = estadoSelect.value;
        const desde = fechaDesde.value ? new Date(fechaDesde.value) : null;
        const hasta = fechaHasta.value ? new Date(fechaHasta.value) : null;

        filas.forEach(fila => {
            const textoFila = fila.innerText.toLowerCase();
            const choferFila = fila.dataset.chofer;
            const estadoFila = fila.dataset.estado;
            const fechaFila = fila.dataset.fecha ? new Date(fila.dataset.fecha) : null;

            let visible = true;
            if (texto && !textoFila.includes(texto)) visible = false;
            if (chofer && choferFila !== chofer) visible = false;
            if (estado && estadoFila !== estado) visible = false;
            if (desde && fechaFila && fechaFila < desde) visible = false;
            if (hasta && fechaFila && fechaFila > hasta) visible = false;

            fila.style.display = visible ? "" : "none";
        });

        calcularTotales();
    }

    limpiarBtn.addEventListener("click", () => {
        searchInput.value = "";
        choferSelect.value = "";
        estadoSelect.value = "";
        fechaDesde.value = "";
        fechaHasta.value = "";
        filtrar();
    });

    searchInput.addEventListener("input", filtrar);
    choferSelect.addEventListener("change", filtrar);
    estadoSelect.addEventListener("change", filtrar);
    fechaDesde.addEventListener("change", filtrar);
    fechaHasta.addEventListener("change", filtrar);

    // =========================
    // Exportar a PDF estilo “dashboard”
    // =========================
    document.getElementById("btn-exportar").addEventListener("click", () => {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF("l", "mm", "a4");

        // -------------------
        // Título
        // -------------------
        doc.setFont("helvetica", "bold");
        doc.setFontSize(18);
        doc.setTextColor(41, 128, 185);
        doc.text("Reporte de Viajes", 14, 20);

        // -------------------
        // Totales a la izquierda con fondo
        // -------------------
        const startY = 30;
        const lineHeight = 6;
        const xTotales = 14;

        doc.setFillColor(245, 245, 245);
        doc.roundedRect(xTotales - 2, startY - 6, 70, lineHeight * 4 + 4, 2, 2, 'F');

        doc.setFontSize(12);
        doc.setTextColor(0);
        doc.setFont("helvetica", "bold");
        doc.text(`Total Ingresos: ${totalIngresosEl.textContent}`, xTotales, startY);
        doc.text(`Total Pago Chofer: ${totalPagoChoferEl.textContent}`, xTotales, startY + lineHeight);
        doc.text(`Total Otros Gastos: ${totalGastosEl.textContent}`, xTotales, startY + lineHeight*2);
        doc.text(`Total Rentabilidad: ${totalRentabilidadEl.textContent}`, xTotales, startY + lineHeight*3);

        // -------------------
        // Gráfico a la derecha con borde (proporcional)
        // -------------------
        const canvas = document.getElementById("graficoTotales");
        const imgData = canvas.toDataURL("image/png");
        const imgWidth = 90; // ancho en mm
        const ratio = canvas.height / canvas.width;
        const imgHeight = imgWidth * ratio; // altura proporcional
        const xGraph = 180;
        const yGraph = startY - 10;

        doc.setDrawColor(0);
        doc.rect(xGraph - 2, yGraph - 2, imgWidth + 4, imgHeight + 4);
        doc.addImage(imgData, "PNG", xGraph, yGraph, imgWidth, imgHeight);

        // -------------------
        // Línea divisoria
        // -------------------
        doc.setLineWidth(0.5);
        doc.line(10, startY + imgHeight + 2, 287, startY + imgHeight + 2);

        // -------------------
        // Tabla debajo
        // -------------------
        const headers = [["Chofer", "Unidad", "Ruta", "Fecha inicio", "Fecha fin", "Estado", "Ingresos", "Pago Chofer", "Gastos", "Rentabilidad"]];
        const data = [];
        filas.forEach(fila => {
            if (fila.style.display !== "none") {
                const celdas = Array.from(fila.querySelectorAll("td")).map(td => td.innerText.replace('→', '->'));
                data.push(celdas);
            }
        });

        doc.autoTable({
            startY: startY + imgHeight + 7,
            head: headers,
            body: data,
            theme: 'grid',
            styles: { fontSize: 8, cellWidth: 'wrap', overflow: 'linebreak' },
            headStyles: { fillColor: [52, 152, 219], textColor: 255 },
            alternateRowStyles: { fillColor: [230, 230, 230] },
            margin: { left: 10, right: 10 },
            columnStyles: { 2: { cellWidth: 70 }, 3: { cellWidth: 25 }, 4: { cellWidth: 25 } },
            tableWidth: 'auto'
        });

        doc.save("reporte_viajes.pdf");
    });

    // =========================
    // Gráfico circular
    // =========================
    let grafico;
    function actualizarGrafico(ingresos, pagoChofer, otrosGastos) {
        const canvas = document.getElementById("graficoTotales");
        canvas.style.height = "250px";
        const ctx = canvas.getContext("2d");
        if (grafico) grafico.destroy();

        grafico = new Chart(ctx, {
            type: "pie",
            data: {
                labels: ["Ingresos", "Pago Chofer", "Otros Gastos"],
                datasets: [{
                    data: [ingresos, pagoChofer, otrosGastos],
                    backgroundColor: ["#4caf50", "#f44336", "#ff9800"]
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'bottom' },
                    title: { display: true, text: "Distribución de Ingresos y Gastos" }
                }
            }
        });
    }

    // Inicializar
    filtrar();
});
