<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Participant extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'session_id',
        'score',
        'status',
        'joined_at',
        'finished_at',
    ];

    /**
     * Get the user model.
     *
     * @return  BelongsTo
     */
    public function user()
    {
        return $this->BelongsTo(User::class);
    }

    /**
     * Get the session model.
     *
     * @return  BelongsTo
     */
    public function session()
    {
        return $this->BelongsTo(Session::class);
    }
}
