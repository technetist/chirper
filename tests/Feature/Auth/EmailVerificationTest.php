<?php

use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Verified;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;

it('tests email verification screen can be rendered', function () {
    $user = User::factory()->unverified()->create();

    $response = $this->actingAs($user)->get(route('verification.notice'));

    $response->assertStatus(200);
});

it('tests that emails can be verified', function () {
    $initialDispatcher = Event::getFacadeRoot();
    Event::fake();
    Model::setEventDispatcher($initialDispatcher);

    $user = User::factory()->unverified()->create();

    $verificationUrl = URL::temporarySignedRoute(
        'verification.verify',
        now()->addMinutes(60),
        [
            'id'   => $user->id,
            'hash' => sha1($user->email),
        ]
    );

    $response = $this->actingAs($user)->get($verificationUrl);

    Event::assertDispatched(Verified::class);

    $this->assertTrue($user->fresh()->hasVerifiedEmail());
    $response->assertRedirect(RouteServiceProvider::HOME . '?verified=1');
});

it('tests that an email can not be verified with invalid hash', function () {
    $user = User::factory()->create(['email_verified_at' => null]);

    $verificationUrl = URL::temporarySignedRoute(
        'verification.verify',
        now()->addMinutes(60),
        [
            'id'   => $user->id,
            'hash' => sha1('wrong-hash'),
        ]
    );

    $this->actingAs($user)->get($verificationUrl);

    $this->assertFalse($user->fresh()->hasVerifiedEmail());
});
