<?php

it('asserts the homepage loads', fn() => $this->get('/')->assertOk());
