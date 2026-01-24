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

    'failed' => 'Login failed. Please check login credentials.',
    'throttle' => 'Too many login attempts. Please try again in :seconds seconds.',

    'success' => 'Login success.',
    'logout_success' => 'You have been logged out.',
    'register_success' => 'You have been registered.',

    'email' => [
        'required' => 'Email is required.',
        'email' => 'Enter a valid email address.',
        'unique' => 'A user with this email already exists',
    ],

    'password' => [
        'required' => 'Password is required.',
        'password' => 'The provided password is incorrect.',
        'confirmed' => 'Passwords do not match',
    ],

    'name' => [
        'required' => 'Name is required',
    ],

    'position_id' => [
        'required' => 'Position is required',
        'exists' => 'The specified position does not exist',
    ],
];
