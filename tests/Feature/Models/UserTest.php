<?php

use App\Models\User;

it('can create a user instance and fill attributes', function () {
    $user = new User([
        'name' => 'Jonh Doe',
        'email' => 'jonhdoe@example.com',
        'password' => 'secret',
    ]);

    expect($user->name)->toBe('Jonh Doe');
    expect($user->email)->toBe('jonhdoe@example.com');
});
