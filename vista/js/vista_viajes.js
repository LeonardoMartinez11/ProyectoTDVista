// Inicialización 
document.addEventListener('DOMContentLoaded', function() {
    initializeVistaViajes();
});

function initializeVistaViajes() {
    // Animación suave al cargar la página
    document.body.style.opacity = '0';
    setTimeout(() => {
        document.body.style.transition = 'opacity 0.5s ease-in';
        document.body.style.opacity = '1';
    }, 100);

    // Configurar filtros
    setupFilters();
    
    // Configurar animaciones de entrada
    setupEntryAnimations();
    
    // Configurar efectos de hover mejorados
    setupHoverEffects();
    
    // Configurar búsqueda en tiempo real
    setupRealTimeSearch();

    // Mostrar notificación de bienvenida (opcional)
    showWelcomeMessage();
}

function setupFilters() {
    const searchInput = document.getElementById('search');
    const estadoSelect = document.getElementById('estado');
    const fechaInput = document.getElementById('fecha');
    const filterBtn = document.querySelector('.btn-filter');

    // Búsqueda en tiempo real
    if (searchInput) {
        searchInput.addEventListener('input', debounce(function() {
            filterTable();
        }, 300));
    }

    // Filtro por estado
    if (estadoSelect) {
        estadoSelect.addEventListener('change', function() {
            filterTable();
        });
    }

    // Filtro por fecha
    if (fechaInput) {
        fechaInput.addEventListener('change', function() {
            filterTable();
        });
    }

    // Botón de filtro (limpiar filtros)
    if (filterBtn) {
        filterBtn.addEventListener('click', function() {
            clearAllFilters();
        });
    }
}

function filterTable() {
    const searchTerm = document.getElementById('search')?.value.toLowerCase() || '';
    const estadoFilter = document.getElementById('estado')?.value.toLowerCase() || '';
    const fechaFilter = document.getElementById('fecha')?.value || '';
    const rows = document.querySelectorAll('.viaje-row');
    
    let visibleRows = 0;

    rows.forEach(row => {
        let isVisible = true;
        
        // Filtro de búsqueda (busca en chofer, destino, placa)
        if (searchTerm) {
            const searchableText = row.textContent.toLowerCase();
            if (!searchableText.includes(searchTerm)) {
                isVisible = false;
            }
        }

        // Filtro por estado
        if (estadoFilter) {
            const rowEstado = row.dataset.estado?.toLowerCase() || '';
            if (!rowEstado.includes(estadoFilter)) {
                isVisible = false;
            }
        }

        // Filtro por fecha
        if (fechaFilter) {
            const fechaCells = row.querySelectorAll('td')[3]; // Columna de fechas
            if (fechaCells) {
                const fechaTexto = fechaCells.textContent;
                if (!fechaTexto.includes(fechaFilter)) {
                    isVisible = false;
                }
            }
        }

        // Mostrar/ocultar fila
        if (isVisible) {
            row.classList.remove('hidden');
            row.classList.add('filtered');
            visibleRows++;
        } else {
            row.classList.add('hidden');
            row.classList.remove('filtered');
        }
    });

    // Actualizar contador de resultados
    updateResultsCounter(visibleRows, rows.length);

    // Mostrar mensaje si no hay resultados
    showNoResultsMessage(visibleRows === 0);
}

function clearAllFilters() {
    // Limpiar inputs
    const searchInput = document.getElementById('search');
    const estadoSelect = document.getElementById('estado');
    const fechaInput = document.getElementById('fecha');

    if (searchInput) searchInput.value = '';
    if (estadoSelect) estadoSelect.value = '';
    if (fechaInput) fechaInput.value = '';

    // Mostrar todas las filas
    const rows = document.querySelectorAll('.viaje-row');
    rows.forEach(row => {
        row.classList.remove('hidden', 'filtered');
    });

    // Ocultar mensaje de no resultados
    showNoResultsMessage(false);
    
    // Actualizar contador
    updateResultsCounter(rows.length, rows.length);

    // Mostrar notificación
    showNotification('Filtros limpiados', 'info');
}

function updateResultsCounter(visible, total) {
    let counter = document.getElementById('results-counter');
    
    if (!counter) {
        counter = document.createElement('div');
        counter.id = 'results-counter';
        counter.style.cssText = `
            text-align: center;
            padding: 10px;
            color: #4a5568;
            font-size: 0.9rem;
            font-weight: 600;
        `;
        
        const tableContainer = document.querySelector('.table-container');
        if (tableContainer) {
            tableContainer.insertBefore(counter, tableContainer.firstChild);
        }
    }

    if (visible === total) {
        counter.textContent = `Mostrando ${total} viajes`;
    } else {
        counter.textContent = `Mostrando ${visible} de ${total} viajes`;
    }
}

function showNoResultsMessage(show) {
    let noResultsMsg = document.getElementById('no-results-message');
    
    if (show && !noResultsMsg) {
        noResultsMsg = document.createElement('div');
        noResultsMsg.id = 'no-results-message';
        noResultsMsg.innerHTML = `
            <div class="empty-state">
                <div class="icon">🔍</div>
                <h3>No se encontraron resultados</h3>
                <p>Intenta ajustar los filtros de búsqueda</p>
                <button onclick="clearAllFilters()" class="btn-primary">Limpiar Filtros</button>
            </div>
        `;
        
        const tableWrapper = document.querySelector('.table-wrapper');
        if (tableWrapper) {
            tableWrapper.appendChild(noResultsMsg);
        }
    } else if (!show && noResultsMsg) {
        noResultsMsg.remove();
    }
}

function setupEntryAnimations() {
    // Animación escalonada para las filas de la tabla
    const rows = document.querySelectorAll('.modern-table tbody tr');
    rows.forEach((row, index) => {
        row.style.animationDelay = `${index * 0.1}s`;
        row.style.animation = 'fadeInUp 0.6s ease-out both';
    });

    // Animación para las cards de estadísticas
    const statCards = document.querySelectorAll('.stat-card');
    statCards.forEach((card, index) => {
        card.style.animationDelay = `${index * 0.2}s`;
    });
}

function setupHoverEffects() {
    // Efecto de hover mejorado para las filas
    const rows = document.querySelectorAll('.modern-table tbody tr');
    
    rows.forEach(row => {
        row.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.02)';
            this.style.zIndex = '2';
            this.style.boxShadow = '0 8px 25px rgba(0, 0, 0, 0.15)';
        });

        row.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
            this.style.zIndex = '1';
            this.style.boxShadow = 'none';
        });
    });

    // Efecto para los botones de acción
    const actionButtons = document.querySelectorAll('.btn-action');
    actionButtons.forEach(btn => {
        btn.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-3px) scale(1.05)';
        });

        btn.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });
}

function setupRealTimeSearch() {
    const searchInput = document.getElementById('search');
    
    if (searchInput) {
        // Agregar icono de búsqueda
        if (!searchInput.parentNode.querySelector('.search-icon')) {
            const searchIcon = document.createElement('span');
            searchIcon.className = 'search-icon';
            searchIcon.innerHTML = '🔍';
            searchIcon.style.cssText = `
                position: absolute;
                right: 15px;
                top: 50%;
                transform: translateY(-50%);
                color: #718096;
                pointer-events: none;
            `;
            
            searchInput.parentNode.style.position = 'relative';
            searchInput.parentNode.appendChild(searchIcon);
            searchInput.style.paddingRight = '45px';
        }

        // Efecto de búsqueda activa
        searchInput.addEventListener('focus', function() {
            this.parentNode.classList.add('search-active');
        });

        searchInput.addEventListener('blur', function() {
            if (!this.value) {
                this.parentNode.classList.remove('search-active');
            }
        });
    }
}

function showWelcomeMessage() {
    const rows = document.querySelectorAll('.viaje-row');
    const totalViajes = rows.length;
    
    if (totalViajes > 0) {
        setTimeout(() => {
            showNotification(`Se encontraron ${totalViajes} viajes registrados`, 'success');
        }, 1000);
    }
}

// Funciones de utilidad
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

function showNotification(message, type = 'info') {
    // Remover notificación anterior si existe
    const existingNotification = document.querySelector('.notification');
    if (existingNotification) {
        existingNotification.remove();
    }

    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.textContent = message;
    
    // Agregar estilos si no existen
    if (!document.getElementById('notification-styles')) {
        const style = document.createElement('style');
        style.id = 'notification-styles';
        style.textContent = `
            .notification {
                position: fixed;
                top: 20px;
                right: 20px;
                padding: 15px 20px;
                border-radius: 12px;
                color: white;
                font-weight: 600;
                z-index: 1000;
                animation: slideInRight 0.3s ease-out;
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
                backdrop-filter: blur(10px);
            }
            .notification-info { 
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
            }
            .notification-success { 
                background: linear-gradient(135deg, #48bb78 0%, #38a169 100%); 
            }
            .notification-error { 
                background: linear-gradient(135deg, #f56565 0%, #e53e3e 100%); 
            }
            .notification-warning {
                background: linear-gradient(135deg, #ed8936 0%, #dd6b20 100%);
            }
            .search-active {
                transform: scale(1.02);
                transition: transform 0.3s ease;
            }
            @keyframes slideInRight {
                from { transform: translateX(100%); opacity: 0; }
                to { transform: translateX(0); opacity: 1; }
            }
        `;
        document.head.appendChild(style);
    }
    
    document.body.appendChild(notification);
    
    // Remover después de 4 segundos
    setTimeout(() => {
        notification.style.animation = 'slideInRight 0.3s ease-out reverse';
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 300);
    }, 4000);
}

// Funciones para manejo de estados
function updateViajeStatus(viajeId, newStatus) {
    // Encontrar la fila del viaje
    const rows = document.querySelectorAll('.viaje-row');
    const viajeRow = Array.from(rows).find(row => {
        const editLink = row.querySelector('a[href*="editar_viaje.php"]');
        return editLink && editLink.href.includes(`id=${viajeId}`);
    });

    if (viajeRow) {
        // Actualizar el badge de estado
        const statusBadge = viajeRow.querySelector('.status-badge');
        if (statusBadge) {
            // Remover clases anteriores
            statusBadge.className = 'status-badge';
            
            // Agregar nueva clase según el estado
            const statusClass = getStatusClass(newStatus);
            statusBadge.classList.add(statusClass);
            statusBadge.textContent = newStatus;

            // Actualizar data attribute
            viajeRow.dataset.estado = newStatus.toLowerCase();

            // Animación de actualización
            statusBadge.style.animation = 'pulse 0.6s ease-in-out';
            setTimeout(() => {
                statusBadge.style.animation = '';
            }, 600);
        }

        showNotification(`Estado del viaje actualizado a: ${newStatus}`, 'success');
    }
}

function getStatusClass(status) {
    const statusLower = status.toLowerCase();
    switch (statusLower) {
        case 'activo':
        case 'en progreso':
            return 'activo';
        case 'completado':
        case 'terminado':
            return 'completado';
        case 'cancelado':
            return 'cancelado';
        case 'pendiente':
            return 'pendiente';
        default:
            return 'pendiente';
    }
}

// Función para exportar datos (opcional)
function exportToCSV() {
    const table = document.querySelector('.modern-table');
    if (!table) return;

    let csv = [];
    const rows = table.querySelectorAll('tr');

    for (let i = 0; i < rows.length; i++) {
        const row = [];
        const cols = rows[i].querySelectorAll('td, th');

        for (let j = 0; j < cols.length - 1; j++) { // -1 para excluir columna de acciones
            let cellText = cols[j].textContent.trim();
            // Limpiar texto y escapar comillas
            cellText = cellText.replace(/"/g, '""');
            row.push('"' + cellText + '"');
        }

        csv.push(row.join(','));
    }

    // Crear y descargar archivo
    const csvContent = csv.join('\n');
    const blob = new Blob([csvContent], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    
    const a = document.createElement('a');
    a.href = url;
    a.download = `viajes_${new Date().toISOString().split('T')[0]}.csv`;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    window.URL.revokeObjectURL(url);

    showNotification('Exportación completada', 'success');
}

// Función para refrescar datos (si tienes endpoint AJAX)
function refreshData() {
    showNotification('Actualizando datos...', 'info');
    setTimeout(() => {
        showNotification('Datos actualizados', 'success');
    }, 1500);
}

// Función para manejo de teclado
document.addEventListener('keydown', function(e) {
    // Ctrl + F para enfocar búsqueda
    if (e.ctrlKey && e.key === 'f') {
        e.preventDefault();
        const searchInput = document.getElementById('search');
        if (searchInput) {
            searchInput.focus();
            searchInput.select();
        }
    }
    
    // Escape para limpiar filtros
    if (e.key === 'Escape') {
        const searchInput = document.getElementById('search');
        if (searchInput && document.activeElement === searchInput) {
            clearAllFilters();
            searchInput.blur();
        }
    }
});

// Funciones globales para usar desde PHP/HTML
window.ViajesTable = {
    updateStatus: updateViajeStatus,
    refresh: refreshData,
    export: exportToCSV,
    clearFilters: clearAllFilters,
    showNotification: showNotification
};
// ----------------------
// INICIALIZACIÓN
// ----------------------
document.addEventListener('DOMContentLoaded', function() {
    initializeVistaViajes();
});

let currentPage = 1;
const rowsPerPage = 5;

function initializeVistaViajes() {
    // Animación suave al cargar la página
    document.body.style.opacity = '0';
    setTimeout(() => {
        document.body.style.transition = 'opacity 0.5s ease-in';
        document.body.style.opacity = '1';
    }, 100);

    // Configurar filtros
    setupFilters();

    // Configurar animaciones de entrada
    setupEntryAnimations();

    // Configurar efectos de hover mejorados
    setupHoverEffects();

    // Configurar búsqueda en tiempo real
    setupRealTimeSearch();

    // Mostrar notificación de bienvenida (opcional)
    showWelcomeMessage();

    // Inicializar paginación
    paginateTable();
}

// ----------------------
// FILTROS Y BÚSQUEDA
// ----------------------
function setupFilters() {
    const searchInput = document.getElementById('search');
    const estadoSelect = document.getElementById('estado');
    const fechaInput = document.getElementById('fecha');
    const filterBtn = document.querySelector('.btn-filter');

    if (searchInput) searchInput.addEventListener('input', debounce(filterAndPaginate, 300));
    if (estadoSelect) estadoSelect.addEventListener('change', filterAndPaginate);
    if (fechaInput) fechaInput.addEventListener('change', filterAndPaginate);
    if (filterBtn) filterBtn.addEventListener('click', () => {
        clearAllFilters();
        filterAndPaginate();
    });
}

function filterAndPaginate() {
    filterTable();      // aplica filtros normales
    currentPage = 1;    // reinicia a página 1
    paginateTable();    // aplica paginación
}

function filterTable() {
    const searchTerm = document.getElementById('search')?.value.toLowerCase() || '';
    const estadoFilter = document.getElementById('estado')?.value.toLowerCase() || '';
    const fechaFilter = document.getElementById('fecha')?.value || '';
    const rows = document.querySelectorAll('.viaje-row');

    let visibleRows = 0;

    rows.forEach(row => {
        let isVisible = true;

        // Búsqueda
        if (searchTerm && !row.textContent.toLowerCase().includes(searchTerm)) isVisible = false;

        // Estado
        const rowEstado = row.dataset.estado?.toLowerCase() || '';
        if (estadoFilter && !rowEstado.includes(estadoFilter)) isVisible = false;

        // Fecha
        if (fechaFilter) {
            const fechaCell = row.querySelectorAll('td')[3];
            const fechaTexto = fechaCell ? fechaCell.textContent : '';
            if (!fechaTexto.includes(fechaFilter)) isVisible = false;
        }

        if (isVisible) {
            row.classList.remove('hidden');
            row.classList.add('filtered');
            visibleRows++;
        } else {
            row.classList.add('hidden');
            row.classList.remove('filtered');
        }
    });

    updateResultsCounter(visibleRows, rows.length);
    showNoResultsMessage(visibleRows === 0);
}

function clearAllFilters() {
    ['search','estado','fecha'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.value = '';
    });
    document.querySelectorAll('.viaje-row').forEach(row => row.classList.remove('hidden','filtered'));
    showNoResultsMessage(false);
    updateResultsCounter(document.querySelectorAll('.viaje-row').length, document.querySelectorAll('.viaje-row').length);
    showNotification('Filtros limpiados', 'info');
}

// ----------------------
// PAGINACIÓN FINAL
// ----------------------
(function() {
    let currentPage = 1;
    const rowsPerPage = 10;

    function paginate() {
        // Solo filas visibles según filtros
        const rows = Array.from(document.querySelectorAll('.viaje-row'))
            .filter(row => !row.classList.contains('hidden'));

        const totalRows = rows.length;
        const totalPages = Math.ceil(totalRows / rowsPerPage);

        // Ocultar todas las filas visibles
        document.querySelectorAll('.viaje-row').forEach(row => row.style.display = 'none');

        // Mostrar solo las filas de la página actual
        const start = (currentPage - 1) * rowsPerPage;
        const end = start + rowsPerPage;
        rows.slice(start, end).forEach(row => row.style.display = '');

        renderPagination(totalPages);
    }

    function renderPagination(totalPages) {
        const paginationDiv = document.querySelector('.pagination');
        if (!paginationDiv) return;

        paginationDiv.innerHTML = '';

        // Botón Anterior
        const prev = document.createElement('a');
        prev.href = '#';
        prev.textContent = '« Anterior';
        prev.onclick = e => {
            e.preventDefault();
            if (currentPage > 1) currentPage--;
            paginate();
        };
        paginationDiv.appendChild(prev);

        // Números de página
        for (let i = 1; i <= totalPages; i++) {
            const pageLink = document.createElement('a');
            pageLink.href = '#';
            pageLink.textContent = i;
            if (i === currentPage) pageLink.classList.add('current');
            pageLink.onclick = e => {
                e.preventDefault();
                currentPage = i;
                paginate();
            };
            paginationDiv.appendChild(pageLink);
        }

        // Botón Siguiente
        const next = document.createElement('a');
        next.href = '#';
        next.textContent = 'Siguiente »';
        next.onclick = e => {
            e.preventDefault();
            if (currentPage < totalPages) currentPage++;
            paginate();
        };
        paginationDiv.appendChild(next);
    }

    // Reiniciar paginación cada vez que se filtren filas
    const originalFilter = filterTable;
    filterTable = function() {
        originalFilter();  // aplica filtros
        currentPage = 1;   // volver a la página 1
        paginate();        // actualizar paginación
    };

    // Inicializar al cargar
    document.addEventListener('DOMContentLoaded', () => {
        paginate();
    });

    // Exponer función global si necesitas refrescar
    window.paginateTable = paginate;
})();

// ----------------------
// TOTAL INGRESOS DEL MES ACTUAL (Automático)
// ----------------------
function updateIngresosMes() {
    const rows = document.querySelectorAll('.viaje-row');
    
    // Detectar automáticamente la tarjeta de ingresos mes
    const statCards = document.querySelectorAll('.stat-card');
    let ingresosElement = null;
    statCards.forEach(card => {
        if (card.querySelector('h3')?.textContent.toLowerCase().includes('ingresos mes')) {
            ingresosElement = card.querySelector('.number');
        }
    });
    if (!ingresosElement) return;

    let totalMes = 0;
    const now = new Date();
    const currentMonth = now.getMonth();
    const currentYear = now.getFullYear();

    rows.forEach(row => {
        const estado = row.dataset.estado?.toLowerCase() || '';
        if (estado === 'finalizado') {
            let fechaCell, ingresoCell;
            // Detectar columna fecha y columna ingreso automáticamente
            row.querySelectorAll('td').forEach((td, index) => {
                const headerText = document.querySelectorAll('.modern-table th')[index]?.textContent.toLowerCase();
                if (headerText.includes('fecha')) fechaCell = td;
                if (headerText.includes('ingreso')) ingresoCell = td;
            });

            if (fechaCell && ingresoCell) {
                const fecha = new Date(fechaCell.textContent);
                if (fecha.getMonth() === currentMonth && fecha.getFullYear() === currentYear) {
                    const ingreso = parseFloat(ingresoCell.textContent.replace(/[^0-9.-]+/g,"")) || 0;
                    totalMes += ingreso;
                }
            }
        }
    });

    ingresosElement.textContent = `Q${totalMes.toFixed(2)}`;
}

// Ejecutar al cargar la página
document.addEventListener('DOMContentLoaded', updateIngresosMes);

// Ejecutar al filtrar la tabla
const originalFilterTable = filterTable;
filterTable = function() {
    originalFilterTable();
    updateIngresosMes();
};