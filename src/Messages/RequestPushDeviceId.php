<?php

namespace Enflow\Component\Brick\Messages;

use Enflow\Component\Brick\BrickMessage;
use Enflow\Component\Brick\Models\BrickDevice;

class RequestPushDeviceId extends BrickMessage
{
    public function response(array $payload)
    {
        if (!auth()->check() || empty($payload['deviceId'])) {
            return;
        }

        $brickDevice = BrickDevice::firstOrNew([
            'device_id' => $payload['deviceId'],
        ]);
        $brickDevice->user_id = auth()->id();
        $brickDevice->save();
    }
}
