<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable,HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

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

    protected $append = [
        'profile_img'
    ];

    public function getProfileimgAttribute()
    {
        $fullname = explode(' ', $this->name);
        $photo_name = '';
        $count = count($fullname);

        if ($count > 1) {
            $photo_name = mb_substr($fullname[0], 0, 1) . mb_substr($fullname[1], 0, 1);
        } else {
            $photo_name = mb_substr($fullname[0], 0, 1);
        }

        return 'https://ui-avatars.com/api/?name=' . $photo_name . '&background=F2F2F2&color=008A49&format=svg';
    }

    public function department()
    {
        return $this->hasOne('App\Models\Department','id','department_id');
    }
}
