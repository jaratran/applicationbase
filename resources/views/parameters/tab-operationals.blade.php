<div class="bg-light border rounded p-3 mb-4">
    <h5>Soporte y Contacto</h5>
    <div class="row">
        <div class="form-group col-md-4">
            <label for="support_email" class="form-label">Correo de Soporte IT</label>
            <input type="email" class="form-control" id="support_email" name="support_email" value="{{ old('support_email', $operationalParameter->support_email ?? '') }}">
        </div>
        <div class="form-group col-md-4">
            <label for="support_telefono" class="form-label">Teléfono de Soporte IT</label>
            <input type="text" class="form-control" id="support_telefono" name="support_telefono" value="{{ old('support_telefono', $operationalParameter->support_telefono ?? '') }}" placeholder="+569 1234 5678">
        </div>
    </div>
</div>

<div class="bg-light border rounded p-3 mb-4">
    <h5>Auditoría de Correos</h5>
    <div class="row">
        <div class="form-group col-md-4">
            <label for="audit_email" class="form-label">Correo de Auditoría</label>
            <input type="email" class="form-control" id="audit_email" name="audit_email" value="{{ old('audit_email', $operationalParameter->audit_email ?? '') }}">
        </div>
        <div class="form-group col-md-4 pt-3">
            <div class="form-check mt-4">
                <input class="form-check-input" type="checkbox" style="transform: scale(1.4); transform-origin: center;"
                        id="audit_email_enabled" name="audit_email_enabled" value="1" {{ old('audit_email_enabled', $operationalParameter->audit_email_enabled ?? false) ? 'checked' : '' }}>
                <label class="form-check-label" for="audit_email_enabled">
                    Activar Envío de Copia de Correos
                </label>
            </div>
        </div>
    </div>
</div>

<div class="bg-light border rounded p-3 mb-4">
    <h5>Expiración de Vigencia de Correo de Verificación y Bienvenida</h5>
    <div class="row">
        <div class="form-group col-md-4">
            <label for="verification_expiration_time" class="form-label">Minutos de Vigencia de los Correos</label>
            <input
                type="number"
                step="1"
                min="1"
                max="1440"
                class="form-control"
                id="verification_expiration_time"
                name="verification_expiration_time"
                value="{{ old('verification_expiration_time', $operationalParameter->verification_expiration_time ?? '') }}"
                placeholder="Ej: 60">
        </div>
    </div>
</div>

<div class="bg-light border rounded p-3 mb-4">
    <h5>Configuración de Perfil de Usuario</h5>
    <div class="row">
        <div class="form-group col-md-4 pt-3">
            <div class="form-check mt-4">
                <input class="form-check-input" type="checkbox" style="transform: scale(1.4); transform-origin: center;"
                        id="allow_profile_editing" name="allow_profile_editing" value="1" {{ old('allow_profile_editing', $operationalParameter->allow_profile_editing ?? true) ? 'checked' : '' }}>
                <label class="form-check-label" for="allow_profile_editing">
                    Permitir Edición de Perfil de Usuario
                </label>
            </div>
        </div>
    </div>
</div>
