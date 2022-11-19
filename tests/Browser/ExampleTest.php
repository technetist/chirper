<?php

use Laravel\Dusk\Browser;

beforeEach(function () {
    $this->withoutVite();
});

it('asserts_the_website_loads', function () {
    $this->withoutVite();

    $this->browse(function (Browser $browser) {
        $browser->visit('/')
            ->assertSee('Laravel');
    });
});
