<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\Army;
use App\Model\Game;


class ArmiesController extends GamesController
{
    /**
     * function to create an army,and update order of existing armies for provided gameId
     * @return object json response with game and all armies
     */
    public function create() 
    { 
        $game = Game::find(request()->game_id);
        $existingArmies = Army::forGame($game->id)->get();
        foreach($existingArmies as $existingArmy){
            $existingArmy->update([
                'order' => $existingArmy->order + 1
            ]);
        }       
        $newArmy = Army::makeRandom($game->id);
        $armies = $existingArmies;
        $armies->push($newArmy);
        if($armies->count() >= 5){
            $game->update([
                'in_progress' => 1
            ]);
        }
        return $this->makeResponse($game,$armies->sortBy('order'));
    }
    
    /**
     * function to iniciate attack
     * @return object json response with game and all armies
     */
    public function attack()
    {
        $game = Game::find(request()->game_id);
        $armies = Army::forGame($game->id)->units()->get();
        $this->resolveAttack($armies);
        $this->changeArmyOnMove($armies, $game);

        return $this->makeResponse($game, $armies->sortBy('order'));
    }
    
    /**
     * function to iniciate all attacks and they will be finished when game has only one game
     * @return object json response with game and all armies
     */
    public function autorun()
    {
        $game = Game::find(request()->game_id);
        $armies = Army::forGame($game->id)->units()->get();

        $this->autorunAttacks($game, $armies);

        return $this->makeResponse($game, $armies->sortBy('order'));
    }   
   
    /**
     * function for launch attack,call function for search attacked army,demage
     * @param object $armies po referenci
     */
    protected function resolveAttack(&$armies)
    {
        $attackerArmy = $armies->where('on_move', 1)->first();
        $attackerArmy = $attackerArmy ?: $armies->where('order', 1)->first();
        $attackerArmy->on_move = 1;
        $attackerArmy->save();
        
        $targetArmy = $this->findTargetArmy($armies, $attackerArmy);

        $attackSuccess = $attackerArmy->current_units >= rand(1, 100);

        $attackDamage = $attackerArmy->current_units > 1 ? (int) ($attackerArmy->current_units / 2) : 1;

        if($attackSuccess){
            if($targetArmy->current_units <= $attackDamage){
                $targetArmy->current_units = 0;
                $targetArmy->save();
                $this->updateArmiesOrder($armies, $attackerArmy->game_id);
            } else {
                $targetArmy->current_units -= $attackDamage;
                $targetArmy->save();
            }
        }
        $armies = Army::forGame($attackerArmy->game_id)->units()->get();
    }
    
    /**
     *function for find attacked army
     * @param object $armies
     * @param object $attackerArmy
     * @return object $targetArmy
     */
    protected function findTargetArmy($armies, $attackerArmy)
    {
        $armiesWithoutAttacker = $armies->where('id', '!=', $attackerArmy->id);
        
        //attack strategies 
        switch ($attackerArmy->attack_strategy) {
            case Army::STRATEGY_RANDOM:
                $targetUnits = $armiesWithoutAttacker->random()->current_units;
                break;
            case Army::STRATEGY_WEAKEST:
                $targetUnits = $armiesWithoutAttacker->pluck('current_units')->min();
                break;
            case Army::STRATEGY_STRONGEST:
                $targetUnits = $armiesWithoutAttacker->pluck('current_units')->max();
                break;
        }
        
        return $armiesWithoutAttacker->where('current_units', $targetUnits)->first();
    }
    
    /**
     * Update order army and give a chanse to add new army in the game 
     * @param object $armies - by reference
     * @param integer $gameId
     */
    protected function updateArmiesOrder(&$armies, $gameId)
    {
        foreach(Army::forGame($gameId)->units()->orderBy('order', 'asc')->get() as $key => $army){
            $army->update([
                'order' => $key + 1 
            ]);
        }
        $armies = Army::forGame($gameId)->get();
    }
    
    /**
     * function for change atribute on_move where attack is launch,also update in_progress in game
     * @param object $armies - by reference
     * @param object $game - by reference
     */
    protected function changeArmyOnMove(&$armies, &$game)
    {

        if($armies->count() > 1){
            $onMoveArmy = $armies->where('on_move', 1)->first();
            $onMoveArmy->on_move = 0;
            $onMoveArmy->save();
  
            $nextArmy = $armies->where('order', $onMoveArmy->order + 1)->first();
            if(empty($nextArmy)){
               $nextArmy = $armies->where('order', 1)->first();
            }
            $nextArmy->on_move = 1;
            $nextArmy->save();
            $armies = Army::forGame($onMoveArmy->game_id)->units()->get();
        } else {
            $game->in_progress = 0;
            $game->finished = 1;
            $game->save();
        }
        
    }
    
    /**
     * Rekurzivna funkcija koja u do nedogled odradjudje jedan napad, naravno ispituje game in_progress tako da zna kad da stane
     * function how run attack by itself,they inspect game in_progres and stop where only one army left
     * @param object $armies - by reference
     * @param object $game - by reference
     */
    protected function autorunAttacks(&$game, &$armies)
    {
        $this->resolveAttack($armies);
        $this->changeArmyOnMove($armies, $game);
        if($game->in_progress){
            $this->autorunAttacks($game, $armies);
        }
    }
}
