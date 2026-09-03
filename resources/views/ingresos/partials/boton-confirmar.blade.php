<!-- Banner de Espacio Seleccionado (Oculto hasta hacer clic en un casillero) -->
<div id="banner-espacio-seleccionado" class="banner-seleccionado mb-3 hidden d-flex justify-content-between align-items-center">
   <div>
      <small class="text-uppercase font-weight-bold block text-muted">ESPACIO SELECCIONADO</small>
      <h3 class="m-0 font-weight-bold text-success" id="texto-numero-espacio">A00</h3>
   </div>
   <button type="button" class="btn btn-link text-muted p-0" onclick="resetSeleccion()" style="text-decoration: none;">
      <em class="fa fa-times fa-lg"></em>
   </button>
</div>

<!-- Botón de Confirmación Principal -->
<div class="form-group mt-3">
   <button type="submit"
           id="btn-confirmar"
           class="btn btn-block btn-lg font-weight-bold text-white shadow-sm"
           style="background-color: #0b1b3d; border-color: #0b1b3d;"
           disabled>
      <span id="btn-confirmar-texto">✓ Confirmar ingreso</span>
   </button>
</div>