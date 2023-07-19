<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use App\Traits\UsesUuid;
use Spatie\Image\Manipulations;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use App\Helpers\MiscHelpers;
use Musonza\Chat\Traits\Messageable;

class Ad extends BaseModel
{
    use HasFactory, UsesUuid, Messageable;

    protected $appends = ['images', 'thumbnail'];
    protected $guarded = [];
    protected $casts = ['fields' => 'array', 'position' => 'array'];

    // protected $with = ['seller', 'buyer'];

    public function seller()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function buyer()
    {
        return $this->belongsTo(User::class, 'sold_to', 'id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    public function subcategory()
    {
        return $this->belongsTo(SubCategory::class, 'sub_category_id', 'id');
    }

    public function getImagesAttribute()
    {
        $images = [];
        
        foreach($this->getMedia() as $media){
            array_push($images, str_replace('localhost', MiscHelpers::getBaseUrl(), $media->getUrl('medium')));
        }

        return $images;
    }

    public function getThumbnailAttribute()
    {

        if (count($this->getImagesAttribute()) <= 0) {
            return null;
        }

        return str_replace('localhost', MiscHelpers::getBaseUrl(), collect($this->getImagesAttribute())->random());


    }

}
