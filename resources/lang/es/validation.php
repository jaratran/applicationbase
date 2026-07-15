<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Mensajes por defecto
    |--------------------------------------------------------------------------
    |
    | Laravel ya trae esto en castellano si usas APP_LOCALE=es
    |
    */
    'accepted'             => 'El campo :attribute debe ser aceptado.',
    'active_url'           => 'El campo :attribute no es una URL válida.',
    'after'                => 'El campo :attribute debe ser una fecha posterior a :date.',
    'after_or_equal'       => 'El campo :attribute debe ser una fecha posterior o igual a :date.',
    'alpha'                => 'El campo :attribute solo puede contener letras.',
    'alpha_dash'           => 'El campo :attribute solo puede contener letras, números, guiones y guiones bajos.',
    'alpha_num'            => 'El campo :attribute solo puede contener letras y números.',
    'array'                => 'El campo :attribute debe ser un arreglo.',
    'before'               => 'El campo :attribute debe ser una fecha anterior a :date.',
    'before_or_equal'      => 'El campo :attribute debe ser una fecha anterior o igual a :date.',
    'between'              => [
        'numeric' => 'El campo :attribute debe estar entre :min y :max.',
        'file'    => 'El archivo :attribute debe pesar entre :min y :max kilobytes.',
        'string'  => 'El campo :attribute debe tener entre :min y :max caracteres.',
        'array'   => 'El campo :attribute debe tener entre :min y :max elementos.',
    ],
    'boolean'              => 'El campo :attribute debe ser verdadero o falso.',
    'confirmed'            => 'La confirmación de :attribute no coincide.',
    'date'                 => 'El campo :attribute no corresponde a una fecha válida.',
    'date_equals'          => 'El campo :attribute debe ser una fecha igual a :date.',
    'date_format'          => 'El campo :attribute no corresponde al formato :format.',
    'different'            => 'El campo :attribute y :other deben ser diferentes.',
    'digits'               => 'El campo :attribute debe tener :digits dígitos.',
    'digits_between'       => 'El campo :attribute debe tener entre :min y :max dígitos.',
    'dimensions'           => 'El campo :attribute tiene dimensiones de imagen no válidas.',
    'distinct'             => 'El campo :attribute tiene un valor duplicado.',
    'email'                => 'El campo :attribute debe ser una dirección de correo válida.',
    'ends_with'            => 'El campo :attribute debe finalizar con uno de los siguientes valores: :values.',
    'exists'               => 'El campo :attribute seleccionado no existe.',
    'file'                 => 'El campo :attribute debe ser un archivo.',
    'filled'               => 'El campo :attribute debe tener un valor.',
    'gt'                   => [
        'numeric' => 'El campo :attribute debe ser mayor que :value.',
        'file'    => 'El archivo :attribute debe pesar más de :value kilobytes.',
        'string'  => 'El campo :attribute debe tener más de :value caracteres.',
        'array'   => 'El campo :attribute debe tener más de :value elementos.',
    ],
    'gte'                  => [
        'numeric' => 'El campo :attribute debe ser mayor o igual que :value.',
        'file'    => 'El archivo :attribute debe pesar :value kilobytes o más.',
        'string'  => 'El campo :attribute debe tener :value caracteres o más.',
        'array'   => 'El campo :attribute debe tener :value elementos o más.',
    ],
    'image'                => 'El campo :attribute debe ser una imagen.',
    'in'                   => 'El campo :attribute seleccionado no es válido.',
    'in_array'             => 'El campo :attribute no existe en :other.',
    'integer'              => 'El campo :attribute debe ser un número entero.',
    'ip'                   => 'El campo :attribute debe ser una dirección IP válida.',
    'ipv4'                 => 'El campo :attribute debe ser una dirección IPv4 válida.',
    'ipv6'                 => 'El campo :attribute debe ser una dirección IPv6 válida.',
    'json'                 => 'El campo :attribute debe ser una cadena JSON válida.',
    'lt'                   => [
        'numeric' => 'El campo :attribute debe ser menor que :value.',
        'file'    => 'El archivo :attribute debe pesar menos de :value kilobytes.',
        'string'  => 'El campo :attribute debe tener menos de :value caracteres.',
        'array'   => 'El campo :attribute debe tener menos de :value elementos.',
    ],
    'lte'                  => [
        'numeric' => 'El campo :attribute debe ser menor o igual que :value.',
        'file'    => 'El archivo :attribute debe pesar :value kilobytes o menos.',
        'string'  => 'El campo :attribute debe tener :value caracteres o menos.',
        'array'   => 'El campo :attribute no debe tener más de :value elementos.',
    ],
    'max'                  => [
        'numeric' => 'El campo :attribute no debe ser mayor que :max.',
        'file'    => 'El archivo :attribute no debe pesar más de :max kilobytes.',
        'string'  => 'El campo :attribute no debe tener más de :max caracteres.',
        'array'   => 'El campo :attribute no debe tener más de :max elementos.',
    ],
    'mimes'                => 'El campo :attribute debe ser un archivo de tipo: :values.',
    'mimetypes'            => 'El campo :attribute debe ser un archivo de tipo: :values.',
    'min'                  => [
        'numeric' => 'El campo :attribute debe ser al menos :min.',
        'file'    => 'El archivo :attribute debe pesar al menos :min kilobytes.',
        'string'  => 'El campo :attribute debe tener al menos :min caracteres.',
        'array'   => 'El campo :attribute debe tener al menos :min elementos.',
    ],
    'not_in'               => 'El campo :attribute seleccionado no es válido.',
    'not_regex'            => 'El formato del campo :attribute no es válido.',
    'numeric'              => 'El campo :attribute debe ser un número.',
    'password'             => 'La contraseña es incorrecta.',
    'present'              => 'El campo :attribute debe estar presente.',
    'regex'                => 'El formato del campo :attribute no es válido.',
    'required'             => 'El campo :attribute es obligatorio.',
    'required_if'          => 'El campo :attribute es obligatorio cuando :other es :value.',
    'required_unless'      => 'El campo :attribute es obligatorio a menos que :other esté en :values.',
    'required_with'        => 'El campo :attribute es obligatorio cuando :values está presente.',
    'required_with_all'    => 'El campo :attribute es obligatorio cuando todos los :values están presentes.',
    'required_without'     => 'El campo :attribute es obligatorio cuando :values no está presente.',
    'required_without_all' => 'El campo :attribute es obligatorio cuando ninguno de los :values está presente.',
    'same'                 => 'Los campos :attribute y :other deben coincidir.',
    'size'                 => [
        'numeric' => 'El campo :attribute debe ser :size.',
        'file'    => 'El archivo :attribute debe pesar :size kilobytes.',
        'string'  => 'El campo :attribute debe tener :size caracteres.',
        'array'   => 'El campo :attribute debe contener :size elementos.',
    ],
    'starts_with'          => 'El campo :attribute debe comenzar con uno de los siguientes valores: :values.',
    'string'               => 'El campo :attribute debe ser una cadena de caracteres.',
    'timezone'             => 'El campo :attribute debe ser una zona válida.',
    'unique'               => 'El campo :attribute ya ha sido registrado.',
    'uploaded'             => 'El campo :attribute no se pudo subir.',
    'url'                  => 'El formato del campo :attribute no es válido.',
    'uuid'                 => 'El campo :attribute debe ser un UUID válido.',

    /*
    |--------------------------------------------------------------------------
    | Mensajes personalizados
    |--------------------------------------------------------------------------
    |
    | Aquí puedes especificar mensajes personalizados para atributos específicos
    | utilizando la convención "attribute.rule" para nombrar las líneas.
    |
    | Forma:
    |    'attribute-name' => [
    |        'rule-name' => 'mensaje-personalizado',
    |    ],
    |
    */
    'custom' => [
        // Validaciones personalizadas - EmpresaController
        'tipo_empresa_id' => [
            'required' => 'Debe seleccionar un tipo de empresa.',
            'integer' => 'El identificador del tipo de empresa no es válido.',
            'exists' => 'El tipo de empresa seleccionado no existe en el sistema.',
        ],
        'rut_empresa' => [
            'required' => 'Debe ingresar el RUT de la empresa.',
            'string' => 'El RUT debe ser una cadena de texto.',
            'max' => 'El RUT no debe exceder los :max caracteres.',
        ],
        'razon_social' => [
            'required' => 'Debe ingresar la razón social de la empresa.',
            'string' => 'La razón social debe ser una cadena de texto.',
            'max' => 'La razón social no debe exceder los :max caracteres.',
        ],
        'direccion' => [
            'required' => 'Debe ingresar la dirección de la empresa.',
            'string' => 'La dirección debe ser una cadena de texto.',
            'max' => 'La dirección no debe exceder los :max caracteres.',
        ],
        'comuna_id' => [
            'required' => 'Debe seleccionar una comuna.',
            'integer' => 'El identificador de comuna no es válido.',
            'exists' => 'La comuna seleccionada no existe en el sistema.',
        ],
        'telefono' => [
            'string' => 'El teléfono debe ser una cadena de texto.',
            'max' => 'El teléfono no debe exceder los :max caracteres.',
        ],
        'email_contacto' => [
            'email' => 'Debe ingresar un correo electrónico válido.',
            'max' => 'El correo electrónico no debe exceder los :max caracteres.',
        ],
        'telefono_contacto' => [
            'string' => 'El teléfono de contacto debe ser una cadena de texto.',
            'max' => 'El teléfono de contacto no debe exceder los :max caracteres.',
        ],

        // Validaciones personalizadas - SucursalController
        'zona_id' => [
            'required' => 'Debe seleccionar una zona.',
            'integer' => 'El identificador de zona no es válido.',
            'exists' => 'La zona seleccionada no existe en el sistema.',
        ],
        'nombre_sucursal' => [
            'required' => 'Debe ingresar el nombre de la sucursal.',
            'string' => 'El nombre de la sucursal debe ser una cadena de texto.',
            'max' => 'El nombre de la sucursal no debe exceder los :max caracteres.',
        ],
        'codigo_siep' => [
            'integer' => 'El código SIEP debe ser un número entero.',
        ],
        'tipo_sucursal_id' => [
            'required' => 'Debe seleccionar un tipo de sucursal.',
            'integer' => 'El identificador del tipo de sucursal no es válido.',
            'exists' => 'El tipo de sucursal seleccionado no existe en el sistema.',
        ],
        'comuna_id' => [
            'required' => 'Debe seleccionar una comuna.',
            'integer' => 'El identificador de comuna no es válido.',
            'exists' => 'La comuna seleccionada no existe en el sistema.',
        ],
        'telefono' => [
            'string' => 'El teléfono debe ser una cadena de texto.',
            'max' => 'El teléfono no debe exceder los :max caracteres.',
        ],
        'email' => [
            'email' => 'Debe ingresar un correo electrónico válido.',
            'max' => 'El correo electrónico no debe exceder los :max caracteres.',
        ],
        'km' => [
            'integer' => 'La distancia en kilómetros debe ser un número entero.',
        ],
        'tiempo_estimado_viaje' => [
            'numeric' => 'El tiempo estimado de viaje debe ser un número.',
            'min' => 'El tiempo estimado no puede ser negativo.',
            'max' => 'El tiempo estimado no debe superar :max minutos.',
        ],

        // Validaciones personalizadas - UsuarioController
        'rut_usuario' => [
            'required' => 'Debe ingresar el RUT del usuario.',
            'string' => 'El RUT debe ser una cadena de texto.',
            'max' => 'El RUT no debe exceder los :max caracteres.',
            'unique' => 'Este RUT ya está registrado en el sistema.',
        ],
        'nombre_usuario' => [
            'required' => 'Debe ingresar el nombre del usuario.',
            'string' => 'El nombre debe ser una cadena de texto.',
            'max' => 'El nombre no debe exceder los :max caracteres.',
        ],
        'apellidos_usuario' => [
            'required' => 'Debe ingresar los apellidos del usuario.',
            'string' => 'Los apellidos deben ser una cadena de texto.',
            'max' => 'Los apellidos no deben exceder los :max caracteres.',
        ],
        'rol_id' => [
            'required' => 'Debe seleccionar un rol.',
            'integer' => 'El identificador del rol no es válido.',
            'exists' => 'El rol seleccionado no existe en el sistema.',
        ],
        'email' => [
            'required' => 'Debe ingresar un correo electrónico.',
            'email' => 'Debe ingresar un correo electrónico válido.',
            'max' => 'El correo electrónico no debe exceder los :max caracteres.',
            'unique' => 'Este correo ya está registrado en el sistema.',
        ],
        'telefono' => [
            'required' => 'Debe ingresar un número de teléfono.',
            'string' => 'El número de teléfono debe ser una cadena de texto.',
            'max' => 'El teléfono no debe exceder los :max caracteres.',
        ],
        'comuna' => [
            'required' => 'Debe seleccionar una comuna.',
            'integer' => 'El identificador de comuna no es válido.',
            'exists' => 'La comuna seleccionada no existe en el sistema.',
        ],
        'direccion' => [
            'required' => 'Debe ingresar una dirección.',
            'string' => 'La dirección debe ser una cadena de texto.',
            'max' => 'La dirección no debe exceder los :max caracteres.',
        ],
        'avatar' => [
            'mimetypes' => 'El avatar debe ser una imagen en formato JPG, PNG, GIF o BMP.',
            'max' => 'El avatar no debe superar los 2MB.',
            'processing_error' => 'Error al procesar el avatar: :message',
        ],

        // Validaciones personalizadas - ParameterController
        'titulo_design' => [
            'required' => 'Debe ingresar el título del sitio.',
            'string' => 'El título debe ser una cadena de texto.',
            'max' => 'El título no debe exceder los :max caracteres.',
        ],
        'custom_primary' => [
            'required' => 'Debe definir el color primario.',
            'string' => 'El color primario debe ser una cadena de texto.',
            'max' => 'El color primario no debe exceder los :max caracteres.',
        ],
        'custom_secondary' => [
            'required' => 'Debe definir el color secundario.',
            'string' => 'El color secundario debe ser una cadena de texto.',
            'max' => 'El color secundario no debe exceder los :max caracteres.',
        ],
        'custom_success' => [
            'required' => 'Debe definir el color de éxito.',
            'string' => 'El color de éxito debe ser una cadena de texto.',
            'max' => 'El color de éxito no debe exceder los :max caracteres.',
        ],
        'custom_warning' => [
            'required' => 'Debe definir el color de advertencia.',
            'string' => 'El color de advertencia debe ser una cadena de texto.',
            'max' => 'El color de advertencia no debe exceder los :max caracteres.',
        ],
        'custom_danger' => [
            'required' => 'Debe definir el color de peligro.',
            'string' => 'El color de peligro debe ser una cadena de texto.',
            'max' => 'El color de peligro no debe exceder los :max caracteres.',
        ],
        'custom_info' => [
            'required' => 'Debe definir el color de información.',
            'string' => 'El color de información debe ser una cadena de texto.',
            'max' => 'El color de información no debe exceder los :max caracteres.',
        ],
        'fondo_pantalla_design' => [
            'image' => 'El fondo debe ser una imagen válida.',
            'mimes' => 'Formato no permitido. Use jpeg, png, jpg, gif, svg o webp.',
            'max' => 'El fondo no debe superar los 2MB.',
        ],
        'logo_design' => [
            'image' => 'El logo debe ser una imagen válida.',
            'mimes' => 'Formato no permitido. Use jpeg, png, jpg, gif, svg o webp.',
            'max' => 'El logo no debe superar los 2MB.',
        ],
        'emblema_design' => [
            'image' => 'El emblema debe ser una imagen válida.',
            'mimes' => 'Formato no permitido. Use jpeg, png, jpg, gif, svg o webp.',
            'max' => 'El emblema no debe superar los 2MB.',
        ],
        'favicon_design' => [
            'image' => 'El favicon debe ser una imagen válida.',
            'mimes' => 'Formato no permitido. Use jpeg, png, jpg, ico, svg o webp.',
            'max' => 'El favicon no debe superar 1MB.',
        ],
        'support_email' => [
            'email' => 'El correo de soporte debe ser válido.',
            'max' => 'El correo de soporte no debe exceder los :max caracteres.',
        ],
        'support_telefono' => [
            'string' => 'El teléfono de soporte debe ser una cadena de texto.',
            'max' => 'El teléfono de soporte no debe exceder los :max caracteres.',
        ],
        'audit_email' => [
            'email' => 'El correo de auditoría debe ser válido.',
            'max' => 'El correo de auditoría no debe exceder los :max caracteres.',
        ],
        'audit_email_enabled' => [
            'boolean' => 'El valor de habilitación de auditoría debe ser verdadero o falso.',
        ],
        'verification_expiration_time' => [
            'integer' => 'El tiempo de expiración debe ser un número entero.',
            'min' => 'Debe ingresar un valor mínimo de 1 minuto.',
            'max' => 'El tiempo de expiración no puede superar 1440 minutos.',
        ],
        'allow_profile_editing' => [
            'boolean' => 'El permiso de edición de perfil debe ser verdadero o falso.',
        ],

        // Validaciones personalizadas - ProfileController
        'rut_usuario' => [
            'required' => 'Debe ingresar el RUT del usuario.',
            'string' => 'El RUT debe ser una cadena de texto.',
            'max' => 'El RUT no debe exceder los :max caracteres.',
            'unique' => 'Este RUT ya está registrado en el sistema.',
        ],
        'nombre_usuario' => [
            'required' => 'Debe ingresar el nombre del usuario.',
            'string' => 'El nombre debe ser una cadena de texto.',
            'max' => 'El nombre no debe exceder los :max caracteres.',
        ],
        'apellidos_usuario' => [
            'required' => 'Debe ingresar los apellidos del usuario.',
            'string' => 'Los apellidos deben ser una cadena de texto.',
            'max' => 'Los apellidos no deben exceder los :max caracteres.',
        ],
        'email' => [
            'required' => 'Debe ingresar un correo electrónico.',
            'email' => 'Debe ingresar un correo electrónico válido.',
            'max' => 'El correo electrónico no debe exceder los :max caracteres.',
            'unique' => 'Este correo ya está registrado en el sistema.',
        ],
        'telefono' => [
            'required' => 'Debe ingresar un número de teléfono.',
            'string' => 'El número de teléfono debe ser una cadena de texto.',
            'max' => 'El teléfono no debe exceder los :max caracteres.',
        ],
        'comuna_id' => [
            'required' => 'Debe seleccionar una comuna.',
            'integer' => 'El identificador de comuna no es válido.',
            'exists' => 'La comuna seleccionada no existe en el sistema.',
        ],
        'direccion' => [
            'required' => 'Debe ingresar una dirección.',
            'string' => 'La dirección debe ser una cadena de texto.',
            'max' => 'La dirección no debe exceder los :max caracteres.',
        ],
        'sucursal_id' => [
            'required' => 'Debe seleccionar una sucursal.',
            'integer' => 'El identificador de sucursal no es válido.',
            'exists' => 'La sucursal seleccionada no existe en el sistema.',
        ],
        'empresa_id' => [
            'required' => 'Debe seleccionar una empresa.',
            'integer' => 'El identificador de empresa no es válido.',
            'exists' => 'La empresa seleccionada no existe en el sistema.',
        ],
        'password' => [
            'min'           => 'La contraseña debe tener al menos :min caracteres.',
            'regex'         => 'Debe contener al menos un número, una mayúscula y una minúscula.',
            'required_with' => 'Las contraseñas ingresadas no coinciden.',
            'same'          => 'Las contraseñas ingresadas no coinciden.',
        ],
        'confirmPassword' => [
            'required' => 'Debe confirmar la nueva contraseña.',
        ],

        // Validaciones personalizadas - AuthController
        'token' => [
            'required' => 'El enlace de activación no es válido o ha expirado.',
        ],
        'email' => [
            'required' => 'Debe ingresar un correo electrónico.',
            'email' => 'Debe ingresar un correo electrónico válido.',
            'max' => 'El correo electrónico no debe exceder los :max caracteres.',
            'unique' => 'Este correo ya está registrado en el sistema.',
        ],
        'password' => [
            'required'  => 'Debe ingresar una contraseña.',
            'min'       => 'La contraseña debe tener al menos :min caracteres.',
            'confirmed' => 'La confirmación de la contraseña no coincide.',
        ],

        // Validaciones personalizadas - LoginController
        'email' => [
            'required' => 'Debe ingresar su correo electrónico.',
            'email' => 'El correo electrónico ingresado no es válido.',
        ],
        'password' => [
            'required' => 'Debe ingresar su contraseña.',
        ],

        // Validaciones personalizadas - ResetPasswordController
        'password' => [
            'required'      => 'La contraseña es obligatoria.',
            'min'           => 'La contraseña debe tener al menos :min caracteres.',
            'regex'         => 'Debe contener al menos un número, una mayúscula y una minúscula.',
            'confirmed'     => 'Las contraseñas ingresadas no coinciden.',
            'required_with' => 'Las contraseñas ingresadas no coinciden.',
            'same'          => 'Las contraseñas ingresadas no coinciden.',
        ],
        'password_confirmation' => [
            'required' => 'Debe confirmar la nueva contraseña.',
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Mensajes de Atributos
    |--------------------------------------------------------------------------
    |
    | Las siguientes líneas se utilizan para intercambiar atributos de marcador
    | de posición con algo más amigable para el lector, como "Correo electrónico"
    | en lugar de "email".
    |
    */
    'attributes' => [

        // Mensajes de validación para tabla caché
        'key' => 'clave',
        'value' => 'valor',
        'expiration' => 'expiración',

        // Mensajes de validación para tabla caché_locks
        'key' => 'clave',
        'owner' => 'propietario',
        'expiration' => 'expiración',

        // Mensajes de validación para tabla catalogo_relaciones
        'valor_origen_id' => 'valor origen',
        'valor_destino_id' => 'valor destino',
        'tipo_relacion_id' => 'tipo de relación',

        // Mensajes de validación para tabla catalogos
        'catalogo_id' => 'categoria padre',
        'nombre' => 'nombre',
        'orden' => 'orden',
        'activo' => 'activo',

        // Mensajes de validación para tabla comunas
        'nombre' => 'nombre',
        'region_id' => 'región de comuna',

        // Mensajes de validación para tabla design_parameters
        'titulo_design' => 'título',
        'logo_design' => 'logo',
        'emblema_design' => 'emblema',
        'favicon_design' => 'favicon',
        'fondo_pantalla_design' => 'fondo de pantalla',
        'custom_primary' => 'color primario',
        'custom_secondary' => 'color secundario',
        'custom_success' => 'color de éxito',
        'custom_warning' => 'color de advertencia',
        'custom_danger' => 'color de peligro',
        'custom_info' => 'color de información',

        // Mensajes de validación para tabla empresas
        'tipo_empresa_id' => 'tipo de empresa',
        'rut_empresa' => 'RUT de la empresa',
        'razon_social' => 'razón social',
        'direccion' => 'dirección',
        'comuna_id' => 'comuna',
        'telefono' => 'teléfono',
        'email_contacto' => 'correo de contacto',
        'telefono_contacto' => 'teléfono de contacto',
        'activo' => 'activo',
        'observacion_inactividad' => 'observación de inactividad',

        // Mensajes de validación para tabla failed_jobs
        'uuid' => 'UUID',
        'connection' => 'conexión',
        'queue' => 'cola',
        'payload' => 'carga útil',
        'exception' => 'excepción',
        'failed_at' => 'fecha de fallo',

        // Mensajes de validación para tabla job_batches
        'id' => 'ID',
        'name' => 'nombre',
        'total_jobs' => 'trabajos totales',
        'pending_jobs' => 'trabajos pendientes',
        'failed_jobs' => 'trabajos fallidos',
        'failed_job_ids' => 'IDs de trabajos fallidos',
        'options' => 'opciones',
        'cancelled_at' => 'fecha de cancelación',
        'created_at' => 'fecha de creación',
        'finished_at' => 'fecha de finalización',

        // Mensajes de validación para tabla jobs
        'queue' => 'cola',
        'payload' => 'carga útil',
        'attempts' => 'intentos',
        'reserved_at' => 'fecha de reserva',
        'available_at' => 'fecha de disponibilidad',
        'created_at' => 'fecha de creación',

        // Mensajes de validación para tabla maquilas
        'empresa_id' => 'empresa',
        'sucursal_id' => 'sucursal',
        'fecha_inicio' => 'fecha de inicio',
        'activo' => 'activo',
        'observaciones' => 'observaciones',

        // Mensajes de validación para tabla migrations
        'migration' => 'migración',
        'batch' => 'lote',

        // Mensajes de validación para tabla operational_parameters
        'support_email' => 'correo de soporte',
        'support_telefono' => 'teléfono de soporte',
        'audit_email' => 'correo de auditoría',
        'audit_email_enabled' => 'habilitación de auditoría de correos',
        'verification_expiration_time' => 'tiempo de expiración de verificación',
        'allow_profile_editing' => 'permiso de edición de perfil',

        // Mensajes de validación para tabla password_resets
        'email' => 'correo electrónico',
        'token' => 'token de recuperación',
        'created_at' => 'fecha de solicitud',

        // Mensajes de validación para tabla regiones
        'nombre' => 'nombre',
        'orden' => 'orden',

        // Mensajes de validación para tabla sessions
        'user_id' => 'usuario',
        'ip_address' => 'dirección IP',
        'user_agent' => 'agente de usuario',
        'payload' => 'carga útil',
        'last_activity' => 'última actividad',

        // Mensajes de validación para tabla sucursales
        'zona_id' => 'zona',
        'nombre_sucursal' => 'nombre de la sucursal',
        'codigo_siep' => 'código SIEP',
        'tipo_sucursal_id' => 'tipo de sucursal',
        'comuna_id' => 'comuna',
        'telefono' => 'teléfono',
        'email' => 'correo electrónico',
        'km' => 'kilómetros',
        'tiempo_estimado_viaje' => 'tiempo estimado de viaje',
        'activo' => 'activo',
        'observacion_inactividad' => 'observación de inactividad',

        // Mensajes de validación para tabla users
        'rut_usuario' => 'RUT del usuario',
        'nombre_usuario' => 'nombres',
        'apellidos_usuario' => 'apellidos',
        'rol_id' => 'rol',
        'empresa_id' => 'empresa',
        'sucursal_id' => 'sucursal',
        'telefono' => 'teléfono',
        'email' => 'correo electrónico',
        'email_verified_at' => 'fecha de verificación del correo',
        'avatar' => 'avatar',
        'comuna_id' => 'comuna',
        'direccion' => 'dirección',
        'es_admin' => 'privilegios de administrador',
        'activo' => 'activo',
        'observacion_inactividad' => 'observación de inactividad',
        'fecha_login' => 'fecha de último ingreso',
        'remember_token' => 'token de sesión',
        'password' => 'contraseña',

        // Mensajes de validación para confirmación de contraseña (VerificationController y ResetPasswordController)
        'password_confirmation' => 'confirmación de contraseña',

        // Mensajes de validación para confirmación de contraseña ProfileController
        'confirmPassword' => 'confirmación de contraseña',

    ],
];
