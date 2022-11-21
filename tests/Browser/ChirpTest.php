<?php

use App\Models\Chirp;
use App\Models\User;
use Laravel\Dusk\Browser;

use function Pest\Faker\faker;

it('asserts the chirps index page loads', function () {
    $this->withoutVite();

    $this->browse(function (Browser $browser) {
        $browser->loginAs(User::factory()->create())->visit(route('chirps.index'))
            ->assertInputPresent('chirp-input');
    });
});

it('asserts a chirp can be submitted', function () {
    $this->withoutVite();

    $this->browse(function (Browser $browser) use (&$user, &$message) {
        $browser->loginAs($user = User::factory()->create())
            ->visit(route('chirps.index'))
            ->type('chirp-input', $message = faker()->sentence)
            ->screenshot('input done')
            ->press('CHIRP');
    });

    sleep(1);

    $this->assertDatabaseHas(Chirp::class, [
        'user_id' => $user->id,
        'message' => $message,
    ]);
});

it('asserts a chirp can be edited', function () {
    $this->withoutVite();

    $chirp = Chirp::factory()->create([
        'user_id' => $user = User::factory()->create(),
        'message' => $old_message = 'Original Message',
    ]);

    $this->browse(function (Browser $browser) use ($user, &$message, $chirp) {
        $browser->loginAs($user)
            ->visit(route('chirps.index'))
            ->type('chirp-input', $message = faker()->sentence)
            ->screenshot('input done')
            ->click("@edit-button-$chirp->id")
            ->press('Edit')
            ->type('chirp-edit', $message = faker()->sentence)
            ->press('SAVE');
    });

    sleep(1);

    $this->assertDatabaseHas(Chirp::class, [
        'user_id' => $user->id,
        'message' => $message,
    ]);

    $this->assertDatabaseMissing(Chirp::class, [
        'user_id' => $user->id,
        'message' => $old_message,
    ]);
});

it('asserts a chirp can be deleted', function () {
    $this->withoutVite();

    $chirp = Chirp::factory()->create([
        'user_id' => $user = User::factory()->create(),
        'message' => 'Original Message',
    ]);

    $this->browse(function (Browser $browser) use ($user, $chirp) {
        $browser->loginAs($user)
            ->visit(route('chirps.index'))
            ->type('chirp-input', faker()->sentence)
            ->screenshot('input done')
            ->click("@edit-button-$chirp->id")
            ->press('Delete');
    });

    sleep(1);

    $this->assertDatabaseMissing('chirps', [
        'id' => $chirp->id
    ]);
});
