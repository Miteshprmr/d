<?php

namespace App\Models;

use App\Models\Traits\HasActive;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasActive, HasApiTokens, Notifiable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'active' => 'boolean',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'email_verified_at', 'mobile_verified_at'
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'name'
    ];

    /**
     * Get the user's profile picture url.
     *
     * @return string|null
     */
    public function getNameAttribute()
    {
        return $this->first_name ." ". $this->last_name;
    }

    /**
     * Resolve the user from their username (email or mobile number).
     *
     * @param string $username
     * @return $this
     */
    public static function resolveUserFromUsername($username)
    {
        $usernameField = (! preg_match('/[^0-9]/', $username) && strlen((string) $username) === 10)
            ? 'mobile'
            : 'email';

        return self::where($usernameField, $username)->first();
    }

    /**
     * Find the user instance for the given username.
     *
     * @param string $username
     * @return $this
     */
    public function findForPassport($username)
    {
        return self::resolveUserFromUsername($username);
    }

    /**
     * Get the bank accounts for the user.
     */
    public function bankAccounts()
    {
        return $this->hasMany(BankAccount::class, 'user_id', 'id');
    }

}