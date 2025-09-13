// Inicialización cuando el DOM está listo
document.addEventListener('DOMContentLoaded', function() {
    initializeForm();
});

function initializeForm() {
    const form = document.getElementById('form-crear-viaje');
    const submitBtn = document.querySelector('.submit-btn');
    const btnText = document.querySelector('.btn-text');
    const btnLoading = document.querySelector('.btn-loading');
    
    // Animación suave al cargar la página
    document.body.style.opacity = '0';
    setTimeout(() => {
        document.body.style.transition = 'opacity 0.5s ease-in';
        document.body.style.opacity = '1';
    }, 100);

    // Manejo del envío del formulario
    if (form) {
        form.addEventListener('submit', function(e) {
            // Mostrar estado de carga
            if (submitBtn && btnText && btnLoading) {
                submitBtn.disabled = true;
                btnText.style.display = 'none';
                btnLoading.style.display = 'inline';
            }
            
            // Validar formulario antes del envío
            if (!validateForm()) {
                e.preventDefault();
                resetSubmitButton();
                return false;
            }
        });
    }

    // Configurar validaciones en tiempo real
    setupRealTimeValidation();
    
    // Configurar efectos de interacción
    setupInteractionEffects();
    
    // Configurar fecha mínima (hoy)
    setMinDate();
}

function validateForm() {
    const requiredFields = [
        'id_chofer',
        'id_unidad', 
        'lugar_inicio',
        'lugar_destino',
        'fecha_inicio',
        'ingreso_total',
        'pago_acordado_chofer'
    ];
    
    let isValid = true;
    let firstErrorField = null;

    requiredFields.forEach(fieldName => {
        const field = document.getElementById(fieldName);
        if (field && !field.value.trim()) {
            showFieldError(field, 'Este campo es requerido');
            isValid = false;
            if (!firstErrorField) {
                firstErrorField = field;
            }
        } else if (field) {
            clearFieldError(field);
        }
    });

    // Validaciones específicas
    if (isValid) {
        isValid = validateNumericFields() && validateDateField();
    }

    // Enfocar el primer campo con error
    if (!isValid && firstErrorField) {
        firstErrorField.focus();
        firstErrorField.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    return isValid;
}

function validateNumericFields() {
    const ingresoTotal = document.getElementById('ingreso_total');
    const pagoChofer = document.getElementById('pago_acordado_chofer');
    let isValid = true;

    if (ingresoTotal && parseFloat(ingresoTotal.value) <= 0) {
        showFieldError(ingresoTotal, 'El ingreso total debe ser mayor a 0');
        isValid = false;
    }

    if (pagoChofer && parseFloat(pagoChofer.value) <= 0) {
        showFieldError(pagoChofer, 'El pago acordado debe ser mayor a 0');
        isValid = false;
    }

    // Validar que el pago al chofer no sea mayor al ingreso total
    if (ingresoTotal && pagoChofer && 
        parseFloat(pagoChofer.value) > parseFloat(ingresoTotal.value)) {
        showFieldError(pagoChofer, 'El pago al chofer no puede ser mayor al ingreso total');
        isValid = false;
    }

    return isValid;
}

function validateDateField() {
    const fechaInicio = document.getElementById('fecha_inicio');
    
    if (fechaInicio && fechaInicio.value) {
        const selectedDate = new Date(fechaInicio.value);
        const now = new Date();
        
        // Permitir fechas desde hoy
        if (selectedDate < now.setHours(0, 0, 0, 0)) {
            showFieldError(fechaInicio, 'La fecha no puede ser anterior a hoy');
            return false;
        }
    }
    
    return true;
}

function showFieldError(field, message) {
    // Remover error anterior
    clearFieldError(field);
    
    // Agregar clase de error
    field.classList.add('field-error');
    
    // Crear elemento de mensaje de error
    const errorMsg = document.createElement('div');
    errorMsg.className = 'error-message';
    errorMsg.textContent = message;
    
    // Insertar después del campo
    field.parentNode.insertBefore(errorMsg, field.nextSibling);
    
    // Agregar estilos de error si no existen
    if (!document.getElementById('error-styles')) {
        const style = document.createElement('style');
        style.id = 'error-styles';
        style.textContent = `
            .field-error {
                border-color: #e53e3e !important;
                box-shadow: 0 0 0 3px rgba(229, 62, 62, 0.1) !important;
            }
            .error-message {
                color: #e53e3e;
                font-size: 0.875rem;
                margin-top: 5px;
                animation: errorSlide 0.3s ease-out;
            }
            @keyframes errorSlide {
                from { opacity: 0; transform: translateY(-5px); }
                to { opacity: 1; transform: translateY(0); }
            }
        `;
        document.head.appendChild(style);
    }
}

function clearFieldError(field) {
    field.classList.remove('field-error');
    const errorMsg = field.parentNode.querySelector('.error-message');
    if (errorMsg) {
        errorMsg.remove();
    }
}

function resetSubmitButton() {
    const submitBtn = document.querySelector('.submit-btn');
    const btnText = document.querySelector('.btn-text');
    const btnLoading = document.querySelector('.btn-loading');
    
    if (submitBtn && btnText && btnLoading) {
        submitBtn.disabled = false;
        btnText.style.display = 'inline';
        btnLoading.style.display = 'none';
    }
}

function setupRealTimeValidation() {
    // Validación en tiempo real para campos numéricos
    const numericFields = ['ingreso_total', 'pago_acordado_chofer'];
    
    numericFields.forEach(fieldId => {
        const field = document.getElementById(fieldId);
        if (field) {
            field.addEventListener('input', function() {
                clearFieldError(this);
                // Validar si tiene valor
                if (this.value && parseFloat(this.value) <= 0) {
                    showFieldError(this, 'Debe ser mayor a 0');
                }
            });
            
            field.addEventListener('blur', function() {
                if (this.value) {
                    validateNumericFields();
                }
            });
        }
    });
    
    // Validación para campos requeridos
    const requiredFields = document.querySelectorAll('[required]');
    requiredFields.forEach(field => {
        field.addEventListener('blur', function() {
            if (!this.value.trim()) {
                showFieldError(this, 'Este campo es requerido');
            } else {
                clearFieldError(this);
            }
        });
        
        field.addEventListener('input', function() {
            if (this.value.trim()) {
                clearFieldError(this);
            }
        });
    });
}

function setupInteractionEffects() {
    // Efecto de hover para el formulario
    const formGroups = document.querySelectorAll('.form-group');
    
    formGroups.forEach(group => {
        const input = group.querySelector('input, select, textarea');
        if (input) {
            input.addEventListener('focus', function() {
                group.classList.add('focused');
            });
            
            input.addEventListener('blur', function() {
                group.classList.remove('focused');
            });
        }
    });
    
    // Agregar estilos para efectos de focus
    if (!document.getElementById('interaction-styles')) {
        const style = document.createElement('style');
        style.id = 'interaction-styles';
        style.textContent = `
            .form-group.focused {
                transform: scale(1.02);
                z-index: 1;
            }
            .form-group {
                transition: transform 0.3s ease;
            }
        `;
        document.head.appendChild(style);
    }
}

function setMinDate() {
    const fechaInicio = document.getElementById('fecha_inicio');
    if (fechaInicio) {
        const now = new Date();
        const year = now.getFullYear();
        const month = String(now.getMonth() + 1).padStart(2, '0');
        const day = String(now.getDate()).padStart(2, '0');
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        
        const minDateTime = `${year}-${month}-${day}T${hours}:${minutes}`;
        fechaInicio.min = minDateTime;
    }
}

// Funciones de utilidad
function showNotification(message, type = 'info') {
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
                border-radius: 8px;
                color: white;
                font-weight: 500;
                z-index: 1000;
                animation: slideInRight 0.3s ease-out;
            }
            .notification-info { background: #667eea; }
            .notification-success { background: #48bb78; }
            .notification-error { background: #e53e3e; }
            @keyframes slideInRight {
                from { transform: translateX(100%); opacity: 0; }
                to { transform: translateX(0); opacity: 1; }
            }
        `;
        document.head.appendChild(style);
    }
    
    document.body.appendChild(notification);
    
    // Remover después de 3 segundos
    setTimeout(() => {
        notification.style.animation = 'slideInRight 0.3s ease-out reverse';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

// Prevenir envío accidental del formulario
document.addEventListener('keydown', function(e) {
    if (e.key === 'Enter' && e.target.tagName !== 'TEXTAREA' && e.target.type !== 'submit') {
        e.preventDefault();
    }
});