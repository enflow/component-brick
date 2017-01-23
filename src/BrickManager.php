<?php

namespace Enflow\Component\Brick;

use Enflow\Component\Brick\Messages\RequestPushDeviceId;

class BrickManager
{
    public function tags(): string
    {
        if (!$this->onDevice()) {
            return '';
        }

        $message = session()->get('brick_message') ?? session()->get('brickMessage');
        if (empty($message) && !session()->has('brickGotDeviceId') && auth()->check()) {
            $message = RequestPushDeviceId::write();
        }

        $receiver = route('brick.receiver');

        return view('brick::tag', compact('message', 'receiver'))->render();
    }

    public function onDevice(): bool
    {
        return (bool) session()->get('brickDevice');
    }
}
