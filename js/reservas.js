let currentReservation = {};


const IVA_RATE = 0.16;

const ALL_INCLUSIVE_DAILY_COST = 100.00; 


function showSection(sectionNumber) {
    if (sectionNumber === 2) {
        const checkIn = document.getElementById('fechaEntrada').value;
        const checkOut = document.getElementById('fechaSalida').value;
        const dateIn = new Date(checkIn);
        const dateOut = new Date(checkOut);
        
        if (!checkIn || !checkOut || dateIn >= dateOut) {
            alert("Por favor, selecciona una fecha de entrada y salida válida. La salida debe ser posterior a la entrada.");
            return; 
        }
    }
    
    for (let i = 1; i <= 4; i++) {
        const section = document.getElementById(`section-${i}`);
        if (section) {
            section.style.display = 'none';
        }
    }
    
    const targetSection = document.getElementById(`section-${sectionNumber}`);
    if (targetSection) {
        targetSection.style.display = 'block';
    }

    if (sectionNumber === 4) {
        collectDataAndCalculate();
    }
}

function collectDataAndCalculate() {
    const checkIn = document.getElementById('fechaEntrada').value;
    const checkOut = document.getElementById('fechaSalida').value;
    
    const dateIn = new Date(checkIn);
    const dateOut = new Date(checkOut);
    const diffTime = Math.abs(dateOut - dateIn);
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    const noches = diffDays > 0 ? diffDays : 1;

    currentReservation.checkIn = checkIn;
    currentReservation.checkOut = checkOut;
    currentReservation.noches = noches;

    const selectedRoom = document.querySelector('input[name="room"]:checked');
    let roomPrice = parseFloat(selectedRoom ? (selectedRoom.dataset.price || 0) : 0); 
    let habitacionId = parseInt(selectedRoom ? selectedRoom.value : 0);
    let roomName = selectedRoom ? selectedRoom.dataset.roomName : 'No seleccionada';
    
    currentReservation.habitacion_id = habitacionId;

    const selectedPackage = document.querySelector('input[name="paquete_tipo"]:checked');
    const packageType = selectedPackage ? selectedPackage.value : 'standard';
    const packageName = selectedPackage ? selectedPackage.dataset.packageName : 'Estándar';
    
    currentReservation.paquete_tipo = packageType;
    currentReservation.paquete_nombre = packageName;
    
    let packageCost = 0;
    let extrasList = ''; 
    let selectedExtraIds = []; 

    if (packageType === 'custom') {
        const selectedExtras = document.querySelectorAll('.extra-option:checked');
        
        selectedExtras.forEach(extra => {
            const extraPrice = parseFloat(extra.dataset.price);
            const extraId = parseInt(extra.value);
            const extraLabel = document.querySelector(`label[for="${extra.id}"]`).textContent.split('(')[0].trim();
            
            packageCost += extraPrice;
            extrasList += `<li class="text-muted small">${extraLabel} - $${extraPrice.toFixed(2)}</li>`;
            selectedExtraIds.push(extraId);
        });
    } else if (packageType === 'all_inclusive') {
        if (habitacionId !== 3) {
            packageCost = ALL_INCLUSIVE_DAILY_COST * noches;
            extrasList = `<li class="text-muted small">Todo Incluido: $${ALL_INCLUSIVE_DAILY_COST.toFixed(2)} x ${noches} noches</li>`;
        } else {
            extrasList = `<li class="text-muted small">Servicio incluido en la Suite Todo Incluido.</li>`;
        }
    }
    
    currentReservation.extra_ids = selectedExtraIds;
    currentReservation.package_cost = packageCost;
    currentReservation.room_name = roomName;

    const roomSubtotal = roomPrice * noches;
    const subtotal = roomSubtotal + packageCost;
    const iva = subtotal * IVA_RATE;
    const total = subtotal + iva;

    currentReservation.subtotal = subtotal;
    currentReservation.iva = iva;
    currentReservation.total = total;

    document.getElementById('summary-dates').textContent = `${checkIn} a ${checkOut} (${noches} noches)`;
    document.getElementById('summary-room-price').textContent = `${roomName} - $${roomPrice.toFixed(2)} x ${noches} noches = $${roomSubtotal.toFixed(2)}`;
    document.getElementById('summary-package-details').innerHTML = `
        <strong>${packageName}:</strong>
        <ul class="list-unstyled mb-0">${extrasList || '<li class="text-muted small">Sin extras adicionales.</li>'}</ul>
    `;
    
    document.getElementById('summary-subtotal').textContent = `$${subtotal.toFixed(2)}`;
    document.getElementById('summary-iva').textContent = `$${iva.toFixed(2)}`;
    document.getElementById('summary-total').textContent = `$${total.toFixed(2)}`;

    document.getElementById('input-fecha-entrada').value = checkIn;
    document.getElementById('input-fecha-salida').value = checkOut;
    document.getElementById('input-habitacion-id').value = habitacionId;
    document.getElementById('input-paquete-tipo').value = packageType;

    const extraIdsInput = document.getElementById('input-extra-ids-json');
    if (extraIdsInput) {
        extraIdsInput.value = JSON.stringify(selectedExtraIds);
    }
}

document.addEventListener('DOMContentLoaded', () => {
    showSection(1); 
    
    const customRadio = document.getElementById('radio-custom-package');
    const extrasContainer = document.getElementById('extras-container');
    const packageOptions = document.querySelectorAll('.package-option');

    packageOptions.forEach(radio => {
        radio.addEventListener('change', () => {
            if (customRadio.checked) {
                extrasContainer.style.display = 'block';
            } else {
                extrasContainer.style.display = 'none';
                document.querySelectorAll('.extra-option').forEach(checkbox => {
                    checkbox.checked = false;
                });
            }
        });
    });
});