<?php

namespace Enflow\Component\Brick;

use Illuminate\Support\Str;
use JsonSerializable;

abstract class BrickMessage implements JsonSerializable
{
    public $payload;

    private function __construct(array $payload = [])
    {
        $this->payload = $payload;
    }

    public function response(array $payload)
    {
    }

    public function jsonSerialize()
    {
        return array_merge([
            'id' => $this->id(),
        ], $this->payload);
    }

    public static function write(array $payload = [])
    {
        return new static($payload);
    }

    public static function make()
    {
        return new static();
    }

    public function id()
    {
        return Str::camel(substr(strrchr(static::class, "\\"), 1));
    }
}
