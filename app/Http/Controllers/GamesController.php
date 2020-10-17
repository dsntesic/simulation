<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\Game;
use App\Model\Army;
use Illuminate\Support\Facades\DB;

class GamesController extends Controller
{
    /**
     * function find all games how in progress and not finished
     * @return object json response with all games 
     */
    public function lists() 
    {         
        $games = Game::with([
            'armies' => function($query){
                return $query->units();
            }
        ])->where('in_progress',1)->get();
        $data = [];
        foreach($games as $game){
            $gameData = $game->toArray();
            foreach ($game->armies as $army){
                $gameData['armies'][] = $army->toArray();
            }
            $data[] = $gameData;
        }
        return response()->json([
            'games' => $data,
        ]);
    }
    
    /**
     * function to play game from the list
     * @return object json response with game and all armies
     */
    public function get()
    {
        $game = Game::find(request()->game_id);
        $armies = Army::forGame($game->id)->units()->get();

        return $this->makeResponse($game, $armies->sortBy('order'));
    }
    
    /**
     * function create a game
     * @return object json response with created game 
     */
    public function create() 
    {           
        $game = Game::create();
        return $this->makeResponse($game);
    }
    
    /**
     * function for standardization json response
     * @param object $game
     * @param object $armies
     * @return object json response
     */
    protected function makeResponse($game, $armies = [])
    {
        $armiesData = [];

        if(!empty($armies)){
            foreach($armies as $army){
                $armiesData[] = $army->toArray();
            }
        }
        return response()->json([
            'game' => $game->toArray(),
            'armies' => $armiesData
        ]);
    }
}
