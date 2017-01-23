<?php

namespace Enflow\Component\Brick;

use Illuminate\Support\Str;

abstract class BrickMessage implements \JsonSerializable
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
            'id' => Str::camel(substr(strrchr(static::class, "\\"), 1)),
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
}
