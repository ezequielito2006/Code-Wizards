<?php

return [
    // Reglas generales
    'required' => 'Debes completar el campo :attribute.',
    'email' => 'El campo :attribute debe contener una dirección de correo válida.',
    'unique' => 'Ya existe un registro con ese :attribute.',
    'min' => [
        'string' => 'El campo :attribute debe tener al menos :min caracteres.',
    ],
    'regex' => 'El formato del campo :attribute no es válido.',
    'in' => 'El campo :attribute debe ser uno de los siguientes valores permitidos: :values.',
    'confirmed' => 'La confirmación del campo :attribute no coincide.',

    // Traducción de atributos
    'attributes' => [
        'nombre' => 'nombre',
        'apellido' => 'apellido',
        'email' => 'correo electrónico',
        'nombre_usuario' => 'nombre de usuario',
        'password' => 'contraseña',
        'rol' => 'rol',
    ],
];
