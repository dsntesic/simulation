<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    protected $fillable = ['in_progress','finished'];
    protected $visible = ['id','in_progress','finished'];
    public $timestamps = false;
   /**
     * Get the armies for the game.
     */
     public function armies()
    {
        return $this->hasMany(Army::class);
    }
}
