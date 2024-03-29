<?php

namespace App\Models;
use App\Models\Game;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'percentage_won'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    //Campos sensibles que se marcan como ocultos que no se incluyen en las respuestas JSON
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
        'password' => 'hashed',
        'date' => 'date',
        'percentage_won' => 'float',
    ];

    protected $attributes = [
        'percentage_won' => 0,
        'name' => 'Anonymous'
    ];

    public function games()
    {
        return $this->hasMany(Game::class);
    }

    //Método que va actualizando el campo percentage_won mediante un Observer (GameObserver)
    public function calculatePercentageWon()
    {
        $totalGames = $this->games()->count(); 
        $winGames = $this->games()->where('game_won', true)->count();
        $this->percentage_won = ($winGames / $totalGames) * 100;
        $this->save();
    }
}
