document.addEventListener('DOMContentLoaded', function () {
    let saldoActual = 0;

    // Al hacer clic en el botón "Recargar" de la tabla
    document.querySelectorAll('.btn-abrir-recarga').forEach(button => {
        button.addEventListener('click', function () {
            const id = this.dataset.id;
            const nombre = this.dataset.nombre;
            const rfid = this.dataset.rfid;
            saldoActual = parseFloat(this.dataset.saldo) || 0;

            document.getElementById('modalUsuarioId').value = id;
            document.getElementById('modalNombreTitular').textContent = nombre;
            document.getElementById('modalRfidCode').textContent = rfid;
            document.getElementById('modalSaldoActual').textContent = saldoActual;

            // Resetear campos
            resetForm();
            $('#modalRecargarTarjeta').modal('show');
        });
    });

    // Evento al escribir monto
    const montoInput = document.getElementById('montoInput');
    montoInput.addEventListener('input', function () {
        actualizarCalculos(parseFloat(this.value) || 0);
        marcarBotonFrecuente(this.value);
    });

    // Evento al hacer clic en un botón de monto frecuente
    document.querySelectorAll('.btn-monto').forEach(btn => {
        btn.addEventListener('click', function () {
            const monto = parseFloat(this.dataset.monto);
            montoInput.value = monto;
            actualizarCalculos(monto);
            marcarBotonFrecuente(monto);
        });
    });

    function actualizarCalculos(monto) {
        const vistaPrevia = document.getElementById('vistaPreviaBox');
        const btnConfirmar = document.getElementById('btnConfirmar');

        if (monto > 0) {
            const total = saldoActual + monto;
            document.getElementById('calcSaldoActual').textContent = saldoActual;
            document.getElementById('calcMontoIngresado').textContent = monto;
            document.getElementById('totalNuevoSaldo').textContent = total;
            document.getElementById('btnMontoText').textContent = monto;

            vistaPrevia.style.display = 'block';
            btnConfirmar.disabled = false;
        } else {
            vistaPrevia.style.display = 'none';
            btnConfirmar.disabled = true;
            document.getElementById('btnMontoText').textContent = '0';
        }
    }

    function marcarBotonFrecuente(monto) {
        document.querySelectorAll('.btn-monto').forEach(btn => {
            if (parseFloat(btn.dataset.monto) === parseFloat(monto)) {
                btn.classList.remove('btn-outline-secondary');
                btn.classList.add('btn-success');
            } else {
                btn.classList.remove('btn-success');
                btn.classList.add('btn-outline-secondary');
            }
        });
    }

    function resetForm() {
        montoInput.value = '';
        actualizarCalculos(0);
        marcarBotonFrecuente(0);
    }
});