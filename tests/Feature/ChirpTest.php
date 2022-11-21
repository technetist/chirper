<?php

use App\Models\Chirp;
use App\Models\User;

use function Pest\Faker\faker;

it('asserts the chirp index page loads',
    fn() => $this->actingAs(User::factory()->create())
        ->get(route('chirps.index'))
        ->assertOk()
);

it('asserts an unauthenticated user is redirected from the chirp page',
    fn() => $this->get(route('chirps.index'))
        ->assertRedirect(route('login'))
);

it('asserts a chirp can be made with a post request', function () {
    $this->actingAs($user = User::factory()->create())
        ->post(route('chirps.store'), ['message' => $message = faker()->sentence]);

    $chirp = Chirp::where([
        'user_id' => $user->id,
        'message' => $message,
    ])->first();

    $this->assertModelExists($chirp);
});

it('asserts a chirp can be updated with a put request', function () {
    $chirp = Chirp::factory()->create([
        'user_id' => $user = User::factory()->create(),
        'message' => $old_message = 'This is the original chirp',
    ]);

    $this->actingAs($user)
        ->put(route('chirps.update', ['chirp' => $chirp]),
            ['message' => $message = 'This is an edited chirp']);

    $chirp = Chirp::where([
        'user_id' => $user->id,
        'message' => $message,
    ])->first();

    $this->assertModelExists($chirp);
    $this->assertEmpty(Chirp::where(['user_id' => $user->id, 'message' => $old_message])->first());
});

it('asserts a chirp can only be edited by the author', function () {
    $chirp = Chirp::factory()->create([
        'message' => 'This is the original chirp',
    ]);

    $this->actingAs(User::factory()->create())
        ->put(route('chirps.update', ['chirp' => $chirp]),
            ['message' => 'This is an edited chirp'])
        ->assertRedirect()
        ->assertSessionHasErrors(['authorization']);
});

it('asserts a chirp can be deleted with a delete request', function () {
    $chirp = Chirp::factory()->create([
        'user_id' => $user = User::factory()->create(),
    ]);

    $this->actingAs($user)
        ->delete(route('chirps.update', ['chirp' => $chirp]));

    $this->assertDatabaseMissing('chirps', ['id' => $chirp->id]);
});

it('asserts a chirp can only be deleted by the author', function () {
    $chirp = Chirp::factory()->create();

    $this->actingAs(User::factory()->create())
        ->delete(route('chirps.update', ['chirp' => $chirp]))
        ->assertRedirect()
        ->assertSessionHasErrors(['authorization']);
});
