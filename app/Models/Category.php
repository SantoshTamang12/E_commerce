<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\UsesUuid;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use App\Helpers\MiscHelpers;

class Category extends BaseModel
{
    use HasFactory, UsesUuid;

    protected $guarded = ['id'];
    protected $appends = ['thumbnail'];

    static $apiAttributes = [
        'id',
        'title',
        'thumbnail'
    ];

    public function subCategories(){
        return $this->hasMany(SubCategory::class);
    }

    public function fields(){
        return $this->hasMany(Field::class);
    }

    public function getThumbnailAttribute()
    {   
        return str_replace('localhost', MiscHelpers::getBaseUrl(), $this->getThumbnail());
    }
}
