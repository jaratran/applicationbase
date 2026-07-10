<div class="bg-light border rounded p-3 mb-4">
    <h5>Titulos</h5>
    <div class="row">
        <div class="form-group col-md-4">
            <label class="my-1" for="titulo_design">Titulo del Sitio</label>
            <input id="titulo_design" type="text" class="form-control" name="titulo_design" value="{{$designParameter->titulo_design}}" required>
        </div>
    </div>
</div>
<div class="bg-light border rounded p-3 mb-4">
    <h5>Colores</h5>
    <!-- Nuevos campos de colores personalizados -->
    <div class="row">
        <div class="col-md-4 mb-3">
            <label for="custom_primary" class="form-label">Color Primario Personalizado</label>
            <div class="input-group">
                <input type="color" class="form-control form-control-color" id="custom_primary" name="custom_primary" value="{{ $designParameter->custom_primary ?? '#0d6efd' }}" title="Elige un color">
                <button class="btn btn-outline-secondary btnResetColor no-guard" type="button" data-color="#0d6efd" data-target="custom_primary" title="Reestablecer a color Bootstrap">
                    <i class="fa fa-undo"></i>
                </button>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <label for="custom_secondary" class="form-label">Color Secundario Personalizado</label>
            <div class="input-group">
                <input type="color" class="form-control form-control-color" id="custom_secondary" name="custom_secondary" value="{{ $designParameter->custom_secondary ?? '#6c757d' }}" title="Elige un color">
                <button class="btn btn-outline-secondary btnResetColor no-guard" type="button" data-color="#6c757d" data-target="custom_secondary" title="Reestablecer a color Bootstrap">
                    <i class="fa fa-undo"></i>
                </button>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <label for="custom_info" class="form-label">Color Info Personalizado</label>
            <div class="input-group">
                <input type="color" class="form-control form-control-color" id="custom_info" name="custom_info" value="{{ $designParameter->custom_info ?? '#0dcaf0' }}" title="Elige un color">
                <button class="btn btn-outline-secondary btnResetColor no-guard" type="button" data-color="#0dcaf0" data-target="custom_info" title="Reestablecer a color Bootstrap">
                    <i class="fa fa-undo"></i>
                </button>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <label for="custom_success" class="form-label">Color Success Personalizado</label>
            <div class="input-group">
                <input type="color" class="form-control form-control-color" id="custom_success" name="custom_success" value="{{ $designParameter->custom_success ?? '#198754' }}" title="Elige un color">
                <button class="btn btn-outline-secondary btnResetColor no-guard" type="button" data-color="#198754" data-target="custom_success" title="Reestablecer a color Bootstrap">
                    <i class="fa fa-undo"></i>
                </button>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <label for="custom_warning" class="form-label">Color Warning Personalizado</label>
            <div class="input-group">
                <input type="color" class="form-control form-control-color" id="custom_warning" name="custom_warning" value="{{ $designParameter->custom_warning ?? '#ffc107' }}" title="Elige un color">
                <button class="btn btn-outline-secondary btnResetColor no-guard" type="button" data-color="#ffc107" data-target="custom_warning" title="Reestablecer a color Bootstrap">
                    <i class="fa fa-undo"></i>
                </button>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <label for="custom_danger" class="form-label">Color Danger Personalizado</label>
            <div class="input-group">
                <input type="color" class="form-control form-control-color" id="custom_danger" name="custom_danger" value="{{ $designParameter->custom_danger ?? '#dc3545' }}" title="Elige un color">
                <button class="btn btn-outline-secondary btnResetColor no-guard" type="button" data-color="#dc3545" data-target="custom_danger" title="Reestablecer a color Bootstrap">
                    <i class="fa fa-undo"></i>
                </button>
            </div>
        </div>
    </div>
</div>
<div class="bg-light border rounded p-3">
    <h5>Imagenes</h5>
    <div class="row">
        <div class="form-group col-md-4">
            <label class="mb-1" for="favicon_design">Favicon</label>
            <input id="favicon_design" type="file" class="form-control" name="favicon_design" value="{{$designParameter->favicon_design}}">
            <img class="my-1" src="{{asset('/config/'.$designParameter->favicon_design)}}" alt="Favicon" style="width: 120px;height:auto;">
        </div>
        <div class="form-group col-md-4">
            <label class="my-1" for="logo_design">Logotipo del Sitio</label>
            <input id="logo_design" type="file" class="form-control" name="logo_design" value="{{$designParameter->logo_design}}">
            <img class="my-1" src="{{asset('/config/'.$designParameter->logo_design)}}" alt="Logo" style="width: 120px;height:auto;">
        </div>
        <div class="form-group col-md-4">
            <label class="my-1" for="emblema_design">Emblema del Sitio</label>
            <input id="emblema_design" type="file" class="form-control" name="emblema_design" value="{{$designParameter->emblema_design}}">
            <img class="my-1" src="{{asset('/config/'.$designParameter->emblema_design)}}" alt="Emblema" style="width: 120px;height:auto;">
        </div>
        </div>
    <div class="row my-3">
        <div class="form-group col-md-4">
            <label class="my-1" for="fondo_pantalla_design">Fondo de Pantalla</label>
            <input id="fondo_pantalla_design" type="file" class="form-control" name="fondo_pantalla_design" value="{{$designParameter->fondo_pantalla_design}}">
            <img class="my-1" src="{{asset('/config/'.$designParameter->fondo_pantalla_design)}}" alt="Fondo" style="width: 120px;height:auto;">
        </div>
    </div>
</div>