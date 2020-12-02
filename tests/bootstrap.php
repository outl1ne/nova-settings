<?php

use Dotenv\Dotenv;
use Illuminate\Support\Env;

require __DIR__.'/../vendor/autoload.php';

Dotenv::create(
    Env::getRepository(),
    __DIR__.'/../',
    '.env.dusk'
)->safeLoad();

if (isset($_SERVER['CI']) || isset($_ENV['CI'])) {
    Orchestra\Testbench\Dusk\Options::withoutUI();
} else {
    Orchestra\Testbench\Dusk\Options::withUI();
}
