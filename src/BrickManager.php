<?php

namespace Enflow\Component\Brick;

use Illuminate\Support\Str;
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
        if (empty($message) && auth()->check() && !session()->get('brickRequestPushDeviceReceived') && config('brick.push_notifications')) {
            $message = RequestPushDeviceId::write();
        }

        $receiver = route('brick.receiver');

        return view('brick::tag-' . $this->version(), compact('message', 'receiver'))->render();
    }

    public function onDevice(): bool
    {
        return (bool)Str::contains(request()->header('User-Agent'), ['Enflow', 'Brick']);
    }

    public function version()
    {
        return Str::contains(request()->header('User-Agent'), '2.') ? static::VERSION_20 : static::VERSION_10;
    }

    public function isAndroid(): bool
    {
        return (bool)Str::contains(request()->header('User-Agent'), ['Android']);
    }

    public function isIos(): bool
    {
        return (bool)Str::contains(request()->header('User-Agent'), ['iPad', 'iPhone', 'iPod']);
    }
}
