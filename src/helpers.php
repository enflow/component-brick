<?php

use Enflow\Component\Brick\BrickManager;

if (! function_exists('onBrickDevice')) {
    function onBrickDevice(): bool
    {
        return app(BrickManager::class)->onDevice();
    }
}
