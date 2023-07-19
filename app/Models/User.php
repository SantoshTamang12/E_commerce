<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Traits\UsesUuid;
use Laravel\Passport\HasApiTokens;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Musonza\Chat\Traits\Messageable;

class User extends Authenticatable implements HasMedia
{
    use HasApiTokens, InteractsWithMedia, HasFactory, Notifiable, UsesUuid, Messageable;

    public function getParticipantDetailsAttribute()
    {
        return [
            'name' => $this->name,
            'avatar_url' => $this->avatar_url,
        ];
    }

    // protected $appends = ['avatar_url'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    // protected $fillable = [
    //     'name',
    //     'phone',
    //     'email',
    //     'password',
    // ];

    protected $guarded  = ['id'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    static $apiAttributes = [
        'id',
        'name',
        'email',
        'phone',
        'gender',
        'latitude',
        'longitude',
        'location',
        'social_unique_id',
        'social_provider',
        'status',
        'avatar_url',
        'media',

    ];

    public function routeNotificationForFcm()
    {
        return $this->device_token;
    }

    // public function getImageUrl()
    // {
    //     return $this->getFirstMediaUrl();
    // }

    // public function getAvatarUrlAttribute()
    // {
    //     return $this->getFirstMediaUrl();
    // }

    public function ads()
    {
        return $this->hasMany(User::class, 'user_id', 'id');
    }

    /**
     * The Exam_class that belong to the exam.
     */
    public function favourites()
    {
        return $this->belongsToMany(Ad::class, 'ad_user')
            ->withTimestamps();
    }
}
