<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\UsesUuid;

class Field extends Model
{
    use HasFactory, UsesUuid;

    protected $guarded = [];
    protected $casts   = ['options' => 'array'];

    static $apiAttributes = [
        'id',
        'label',
        'isPrice',
        'required',
        'type',
        'options',
        'category_id',
        'sub_category_id',
    ];
}
