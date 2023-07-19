<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use App\Traits\UsesUuid;
use App\Helpers\MiscHelpers;

class InAppAdd extends BaseModel 
{
    use HasFactory, UsesUuid;
    
    protected $appends = ['image_url'];

    protected $guarded = [];

    public function getImageUrlAttribute(){
        return str_replace('localhost', MiscHelpers::getBaseUrl(), $this->getThumbnail());
    
    }
}
