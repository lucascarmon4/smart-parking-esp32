<?php

it('abre o dashboard', function () {
    $this->get('/')
        ->assertOk()
        ->assertSee('Smart Parking')
        ->assertSee('js/dashboard.js');
});
