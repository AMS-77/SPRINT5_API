<?php

namespace App\Observers;
use App\Models\Game;

class GameObserver
{
    public function created (Game $game)
    {
        $user = $game->user;
        $user->calculatePercentageWon();
    }
}
