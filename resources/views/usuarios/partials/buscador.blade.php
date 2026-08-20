<div class="card mb-4 border-0 shadow-sm">
    <div class="card-body">
        <form action="{{ route('usuarios.buscar') }}" method="GET" class="form-inline justify-content-center">
            <div class="input-group w-75">
                <div class="input-group-prepend">
                    <span class="input-group-text bg-umss-navy text-white">
                        <i class="fas fa-id-card"></i>
                    </span>
                </div>
                <input type="text" 
                       name="ci" 
                       class="form-control form-control-lg" 
                       placeholder="Ingrese la Cédula de Identidad (C.I.)..." 
                       value="{{ request('ci') }}" 
                       required autocomplete="off">
                <div class="input-group-append">
                    <button type="submit" class="btn btn-umss px-4">
                        <i class="fas fa-search mr-1"></i> Buscar
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>