<?php

namespace App\Models;

use App\Models\SocialiteProvider;
use App\Notifications\ResetPasswordNotification;
use App\Notifications\VerifyEmailNotification;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use jeremykenedy\LaravelRoles\Traits\HasRoleAndPermission;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, HasApiTokens, HasRoleAndPermission;

    /**
     * The accessors to append to the model's array.
     *
     * @var array
     */
    protected $appends = [
        'roles',
        'avatar',
        'providers'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'theme_dark',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'theme_dark'        => 'boolean',
        'email_verified_at' => 'datetime',
    ];

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    public function sendEmailVerificationNotification()
    {
        $this->notify(new VerifyEmailNotification());
    }

    public function getRolesAttribute()
    {
        return $this->getRoles();
    }

    public function getAvatarAttribute()
    {
        return 'https://www.gravatar.com/avatar/'.md5(Str::lower($this->email)).'.jpg?s=200&d=mp';
    }

    public function getProvidersAttribute()
    {
        return $this->socialiteProviders;
    }

    /**
     * Get the socialite providers.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function socialiteProviders()
    {
        return $this->hasMany(SocialiteProvider::class);
    }
}
