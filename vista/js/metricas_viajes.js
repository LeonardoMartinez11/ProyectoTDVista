// Gráfico de Gastos por Tipo
const ctxGastos = document.getElementById('gastosChart').getContext('2d');
const gastosChart = new Chart(ctxGastos, {
    type: 'pie',
    data: {
        labels: gastoLabels,
        datasets: [{
            label: 'Gastos por Tipo',
            data: gastoData,
            backgroundColor: [
                'rgba(255, 99, 132, 0.5)',
                'rgba(54, 162, 235, 0.5)',
                'rgba(255, 206, 86, 0.5)',
                'rgba(75, 192, 192, 0.5)',
                'rgba(153, 102, 255, 0.5)'
            ],
            borderColor: [
                'rgba(255, 99, 132, 1)',
                'rgba(54, 162, 235, 1)',
                'rgba(255, 206, 86, 1)',
                'rgba(75, 192, 192, 1)',
                'rgba(153, 102, 255, 1)'
            ],
            borderWidth: 1
        }]
    },
    options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } }
});

// Gráfico Comparativa Ingresos vs Gastos vs Ganancia Neta
const ctxComparativa = document.getElementById('comparativaChart').getContext('2d');
const comparativaChart = new Chart(ctxComparativa, {
    type: 'bar',
    data: {
        labels: ['Ingresos', 'Gastos', 'Ganancia Neta'],
        datasets: [{
            label: 'Q',
            data: [ingresoTotal, totalGastos, gananciaNeta],
            backgroundColor: [
                'rgba(54, 162, 235, 0.5)',
                'rgba(255, 99, 132, 0.5)',
                'rgba(75, 192, 192, 0.5)'
            ],
            borderColor: [
                'rgba(54, 162, 235, 1)',
                'rgba(255, 99, 132, 1)',
                'rgba(75, 192, 192, 1)'
            ],
            borderWidth: 1
        }]
    },
    options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
});

// Exportar PDF
document.getElementById("exportPDF").addEventListener("click", () => {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF('p', 'mm', 'a4');

    let y = 15;

    // --- Título Principal ---
    doc.setFont("helvetica", "bold");
    doc.setFontSize(18);
    doc.text(`Métricas del Viaje #${idViaje}`, 105, y, { align: 'center' });
    y += 12;

    // --- Datos del Viaje ---
    doc.setFont("helvetica", "normal");
    doc.setFontSize(12);
    const infoViaje = [
        `Chofer: ${nombreChofer}`,
        `Unidad: ${placaUnidad}`,
        `Origen: ${origen}`,
        `Destino: ${destino}`,
        `Kilómetros: ${kilometros}`,
        `Combustible: ${combustible} G`,
        `Ingreso Total: Q${ingresoTotal.toFixed(2)}`,
        `Pago al Chofer: Q${pagoChofer.toFixed(2)}`,
        `Gastos Totales: Q${totalGastos.toFixed(2)}`,
        `Ganancia Neta: Q${gananciaNeta.toFixed(2)}`,
        `Rentable: ${rentable}`,
        `Rentabilidad: ${rentabilidadPorc.toFixed(2)}%`
    ];
    infoViaje.forEach(line => { doc.text(line, 15, y); y += 7; });

    // Línea separadora sutil
    doc.setDrawColor(180);
    doc.setLineWidth(0.3);
    doc.line(10, y, 200, y);
    y += 5;

    // --- Gráficos con fondo suave y bordes ---
    const addChartWithBorder = (chartObj, posY) => {
        const img = chartObj.toBase64Image();
        // Fondo tenue
        doc.setFillColor(245, 245, 245);
        doc.roundedRect(10, posY - 2, 190, 82, 3, 3, 'F');
        // Imagen del gráfico
        doc.addImage(img, 'PNG', 15, posY, 180, 80);
    };

    addChartWithBorder(gastosChart, y);
    y += 85;
    addChartWithBorder(comparativaChart, y);

    // --- Nueva página para tabla de gastos ---
    doc.addPage();
    doc.setFontSize(16);
    doc.setFont("helvetica", "bold");
    doc.setTextColor(54, 162, 235); // Color corporativo
    doc.text("Detalle de Gastos", 105, 15, { align: 'center' });

    doc.autoTable({
        startY: 25,
        head: [["Tipo de Gasto", "Monto (Q)"]],
        body: gastoLabels.map((tipo, i) => [tipo, gastoData[i].toFixed(2)]),
        theme: 'grid',
        headStyles: {
            fillColor: [54, 162, 235],
            textColor: 255,
            fontStyle: 'bold'
        },
        alternateRowStyles: { fillColor: [245, 245, 245] },
        styles: { cellPadding: 4, fontSize: 11 },
        tableLineWidth: 0.2
    });

    // --- Guardar PDF ---
    doc.save(`Viaje_${idViaje}.pdf`);
});
