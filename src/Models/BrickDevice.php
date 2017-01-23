<?php

namespace Enflow\Component\Brick\Models;

use Illuminate\Database\Eloquent\Model;

class BrickDevice extends Model
{
    public $fillable = [
        'user_id',
        'device_id',
    ];
}
