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
        <div class="form-group col-md-4">
            <label for="notify_admins" class="form-label">Correos de Notificación</label>
            <div class="form-check mt-2">
                <input class="form-check-input" type="checkbox" style="transform: scale(1.4); transform-origin: center;"
                        id="notify_admins_as_coordinators" name="notify_admins_as_coordinators"
                        value="1" {{ old('notify_admins_as_coordinators', $operationalParameter->notify_admins_as_coordinators ?? false) ? 'checked' : '' }}>

                <label class="form-check-label" for="notify_admins_as_coordinators">
                    Incluir a los administradores IT (útil para validaciones).
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

<!--
<div class="bg-light border rounded p-3 mb-4">
    <h5>Velocidad Media de Camiones</h5>
    <div class="row">
        <div class="form-group col-md-4">
            <label for="average_truck_speed" class="form-label">Velocidad Media (km/h)</label>
            <input
                type="number"
                step="1"
                min="50"
                class="form-control"
                id="average_truck_speed"
                name="average_truck_speed"
                value="{{ old('average_truck_speed', $operationalParameter->average_truck_speed ?? '') }}"
                placeholder="Ej: 50">
        </div>
    </div>
</div>
-->

<div class="bg-light border rounded p-3 mb-4">
    <h5>Duraciones de Tránsito (XII Región)</h5>
    <div class="row">
        <div class="form-group col-md-4">
            <label for="terrestrial_transit_duration_days" class="form-label">
                Duración Tránsito Terrestre (días)
            </label>
            <input
                type="number"
                step="1"
                min="1"
                max="30"
                class="form-control"
                id="terrestrial_transit_duration_days"
                name="terrestrial_transit_duration_days"
                value="{{ old('terrestrial_transit_duration_days', $operationalParameter->terrestrial_transit_duration_days) }}"
                placeholder="Ej: 3">
        </div>

		<div class="form-group col-md-4">
            <label for="maritime_transit_duration_days" class="form-label">
                Duración Tránsito Marítimo (días)
            </label>
            <input
                type="number"
                step="1"
                min="1"
                max="30"
                class="form-control"
                id="maritime_transit_duration_days"
                name="maritime_transit_duration_days"
                value="{{ old('maritime_transit_duration_days', $operationalParameter->maritime_transit_duration_days) }}"
                placeholder="Ej: 3">
        </div>

<!--
		<div class="form-group col-md-4">
			<label for="combined_transit_duration_days" class="form-label">
				Duración Tránsito Combinado (días)
			</label>
			<input
				type="number"
				step="1"
				min="1"
				max="30"
				class="form-control"
				id="combined_transit_duration_days"
				name="combined_transit_duration_days"
				value="{{ old('combined_transit_duration_days', $operationalParameter->combined_transit_duration_days) }}"
				placeholder="Ej: 3">
		</div>
-->

		<div class="form-group col-md-4">
			<label for="combined_transit_duration_days" class="form-label">
				Demora entre arribo a puerto y ETA Calculada (horas)
			</label>
			<input
				type="number"
				step="1"
				min="0"
				max="23"
				class="form-control"
				id="delay_arribo_eta_hours"
				name="delay_arribo_eta_hours"
				value="{{ old('delay_arribo_eta_hours', $operationalParameter->delay_arribo_eta_hours) }}"
				placeholder="Ej: 2">
		</div>

	</div>
</div>

<div class="bg-light border rounded p-3 mb-4">
    <h5>Programa Diario</h5>
    <div class="row">
        <div class="form-group col-md-4">
            <label for="daily_program_execution_time" class="form-label">Hora de Emisión del Programa Diario</label>
            <input type="time" class="form-control" id="daily_program_execution_time" name="daily_program_execution_time" value="{{ old('daily_program_execution_time', isset($operationalParameter->daily_program_execution_time) ? \Carbon\Carbon::createFromFormat('H:i:s', $operationalParameter->daily_program_execution_time)->format('H:i') : '') }}">
        </div>
        <div class="form-group col-md-4 pt-3">
            <div class="form-check mt-4">
                <input class="form-check-input" type="checkbox" style="transform: scale(1.4); transform-origin: center;"
                        id="auto_emit_daily_program" name="auto_emit_daily_program" value="1" {{ old('auto_emit_daily_program', $operationalParameter->auto_emit_daily_program ?? false) ? 'checked' : '' }}>
                <label class="form-check-label" for="auto_emit_daily_program">
                    Emisión Automática del Programa Diario
                </label>
            </div>
        </div>
    </div>
</div>
