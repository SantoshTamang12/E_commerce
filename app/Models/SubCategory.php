<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\UsesUuid;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class SubCategory extends BaseModel
{
    use HasFactory, UsesUuid;

    protected $guarded = ['id'];
    protected $appends = ['thumbnail'];
    protected $with = ['category', 'fields'];

    protected $casts = ['fields' => 'array'];

    static $apiAttributes = [
        'id',
        'category_id',
        'title',
        'thumbnail'
    ];

    public function category(){
        return $this->belongsTo(Category::class);
    }

    public function fields(){
        return $this->hasMany(Field::class);
    }

    public function getThumbnailAttribute()
    {
        return $this->getThumbnail();
    }
}
