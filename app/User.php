<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'role_id', 'passport_token', 'photo'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attribute that shoud be show for array.
     *
     * @var array
     */
    protected $with = ['role'];
    
    public function isAdmin()
    {
        return $this->role_id === 1;
    }

    public function isManager()
    {
        return $this->role_id === 2;
    }

    public function isChef()
    {
        return $this->role_id === 3;
    }

    public function isWaiter()
    {
        return $this->role_id === 4;
    }

    public function smallPhotoLink()
    {
        if (is_null($this->photo)) {
            if ($this->role_id == 1) {
                return asset('storage/images/users/admin48.png');
            } elseif ($this->role_id == 2) {
                return asset('storage/images/users/manager48.png');
            } elseif ($this->role_id == 3) {
                return asset('storage/images/users/chef48.png');
            } elseif ($this->role_id == 4) {
                return asset('storage/images/users/waiter48.png');
            }
        } else {
            return asset('storage/images/users/48/'.$this->photo);
        }
    }

    public function mediumPhotoLink()
    {
        if (is_null($this->photo)) {
            if ($this->role_id == 1) {
                return asset('storage/images/users/admin96.png');
            } elseif ($this->role_id == 2) {
                return asset('storage/images/users/manager96.png');
            } elseif ($this->role_id == 3) {
                return asset('storage/images/users/chef96.png');
            } elseif ($this->role_id == 4) {
                return asset('storage/images/users/waiter96.png');
            }
        } else {
            return asset('storage/images/users/96/'.$this->photo);
        }
    }

    //relationship
    public function role()
    {
        return $this->belongsTo('App\Role');
    }

    public function AauthAccessToken()
    {
        return $this->hasMany('\App\OauthAccessToken');
    }

    public function tables()
    {
        return $this->hasMany('App\Table');
    }

    public function dishCategories()
    {
        return $this->hasMany('App\DishCategory');
    }

    public function dishes()
    {
        return $this->hasMany('App\Dish');
    }

    public function customers()
    {
        return $this->hasMany('App\Customer');
    }

    public function carts()
    {
        return $this->hasMany('App\Cart');
    }

    public function waiterOrders()
    {
        return $this->hasMany('App\Order', 'waiter_id');
    }

    public function chefOrders()
    {
        return $this->hasMany('App\Order', 'chef_id');
    }

    public function payments()
    {
        return $this->hasMany('App\Payments');
    }
}
