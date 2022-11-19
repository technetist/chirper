<?php

use App\Models\User;

it('confirms the password screen can be rendered', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('password.confirm'));

    $response->assertStatus(200);
});

it('tests that a password can be confirmed', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('password.confirm'), ['password' => 'password']);

    $response->assertRedirect();
    $response->assertSessionHasNoErrors();
});

it('tests that a password is not confirmed with an invalid password', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('password.confirm'), ['password' => 'wrong-password']);

    $response->assertSessionHasErrors();
});
