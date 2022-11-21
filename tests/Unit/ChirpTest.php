<?php

use App\Models\Chirp;

use App\Models\User;
use App\Notifications\NewChirp;

it('asserts a chirp can be made', function () {
    $chirp = Chirp::factory()->create();

    $this->assertModelExists($chirp);
});

it('asserts users can have many chirps', function () {
    $user = User::factory()->create();
    Chirp::factory(4)->create([
        'user_id' => $user->id,
    ]);

    $this->assertCount(4, $user->chirps);
});

it('asserts a message is sent to users when a chirp is made', function () {
    Notification::fake();

    User::factory(4)->create();

    $this->actingAs(User::factory()->create())
        ->post(route('chirps.store'), ['message' => 'Notification Test']);

    Notification::assertSentTo(
        [User::find(1)], NewChirp::class
    );

    Notification::assertCount(User::all()->count() - 1);
});
