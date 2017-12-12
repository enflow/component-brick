<?php

namespace Enflow\Component\Brick\Messages;

use Enflow\Component\Brick\BrickMessage;
use Enflow\Component\Brick\Models\BrickDevice;

class RequestPushDeviceId extends BrickMessage
{
    public function response(array $payload)
    {
        $user = app('brick.user');

        if (empty($user) || empty($payload['deviceId'])) {
            return;
        }

        $brickDevice = BrickDevice::firstOrNew([
            'device_id' => $payload['deviceId'],
        ]);
        $brickDevice->user_id = $user->id;
        $brickDevice->save();

        session()->put('brickRequestPushDeviceReceived', true);
    }
}
