<?php

namespace Enflow\Component\Brick;

use Enflow\Component\Brick\Messages\RequestPushDeviceId;

class BrickManager
{
    const VERSION_10 = 1;
    const VERSION_20 = 2;

    public function tags(): string
    {
        if (!$this->onDevice()) {
            return '';
        }

        $message = session()->get('brick_message') ?? session()->get('brickMessage');
        if (empty($message) && auth()->check()) {
            $message = RequestPushDeviceId::write();
        }

        $receiver = route('brick.receiver');

        return view('brick::tag-' . $this->version(), compact('message', 'receiver'))->render();
    }

    public function onDevice(): bool
    {
        return (bool)str_contains(request()->header('User-Agent'), ['Enflow', 'Brick']);
    }

    public function version()
    {
        return ends_with(request()->header('User-Agent'), '2.0') ? static::VERSION_20 : static::VERSION_10;
    }

    public function isAndroid(): bool
    {
        return (bool)str_contains(request()->header('User-Agent'), ['Android']);
    }
}
