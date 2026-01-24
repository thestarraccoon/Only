<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used during authentication for various
    | messages that we need to display to the user. You are free to modify
    | these language lines according to your application's requirements.
    |
    */

    'failed' => 'Вход не удался. Проверьте логин и/или пароль',
    'throttle' => 'Слишком много попыток. Пожалуйста, повторите через :seconds seconds.',

    'success' => 'Успешный вход.',
    'logout_success' => "Вы вышли из системы",
    'register_success' => 'Регистрация прошла успешно',

    'email' => [
        'required' => 'Email обязателен для заполнения',
        'email' => 'Введите корректный email',
        'unique' => 'Пользователь с таким email уже существует',
    ],

    'password' => [
        'required' => 'Пароль обязателен для заполнения',
        'password' => 'Некорректный пароль.',
        'confirmed' => 'Пароли не совпадают',
    ],

    'name' => [
        'required' => 'Имя обязательно для заполнения',
    ],
];
