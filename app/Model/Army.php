<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Army extends Model
{
    protected $fillable = ['name','game_id','start_units','current_units','attack_strategy','on_move','order'];
    public $timestamps = false;
    
    
    const ATTACK_STRATEGIES = [
        self::STRATEGY_RANDOM,
        self::STRATEGY_WEAKEST,
        self::STRATEGY_STRONGEST
    ];
    
    const STRATEGY_RANDOM = 'random';
    const STRATEGY_WEAKEST = 'weakest';
    const STRATEGY_STRONGEST = 'strongest';

    /**
     * Get the game that owns the army.
     */
    public function game()
    {
        return $this -> belongsTo(Game::class);
    }
       
    /**
     * static function for create a army
     * @param type $gameId
     */
    public static function makeRandom($gameId)
    {
        $units = rand(80,100);
        
        return self::create([
            'name'=>'Army'. rand(1,1000),
            'game_id'=>$gameId,
            'start_units'=>$units,
            'current_units'=>$units,
            'attack_strategy'=>self::ATTACK_STRATEGIES[rand(0,count(self::ATTACK_STRATEGIES)-1)],
            'order'=>1,
            'on_move'=>0,
        ]);
    }
    /**
     * function for return all armies from specific game 
     *
     * @return object
     */
    public function scopeForGame($query,$id)
    {
        return $query -> where('game_id',$id);
    }
    /**
     * function for return all armies whose army is not 0 
     *
     * @return object
     */
    public function scopeUnits($query) 
    {
        return $query -> where('current_units','!=',0);
    }
}
