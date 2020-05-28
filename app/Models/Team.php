<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    protected $fillable = [
        'name',
        'owner_id',
        'slug'
    ];

//    protected static function boot()
//    {
//        parent::boot();
////        static::creating( function ($team) {
////             auth()->user()->teams()->attach($team->id);
//////            $team->members()->attach( ['owner_id'=>auth()->id(),'team_id'=>$team->id] );
////        } );
//        static::deleting( function ($team) {
//            $team->members()->sync( [] );
//        } );
//
//    }

    public function owner()
    {
        return $this->belongsTo( User::class, 'owner_id' );
    }

    public function members()
    {
//        public function belongsToMany($related, $table = null, $foreignPivotKey = null, $relatedPivotKey = null,
//                                      $parentKey = null, $relatedKey = null, $relation = null)
        return $this->belongsToMany( User::class ,'team_user','team_id','owner_id')
            ->withTimestamps();
    }

    public function designs()
    {
        return $this->hasMany( Design::class );
    }

    public function hasUser($user)
    {
        return $this->members()
            ->where( 'owner_id', $user->id )
            ->first() ? true : false;
    }
    public function invitation()
    {
        return $this->hasMany(Invitation::class);
    }
    public function hsaPendingInvite($email)
    {

        return (bool)$this->invitation()
            ->where('recipient_email',$email)
            ->count();
    }

}
