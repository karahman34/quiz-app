<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Session extends Model
{
    use HasFactory;

    protected $fillable = [
        'packet_id',
        'code',
        'available_for',
        'status',
    ];

    /**
     * Get the packet.
     *
     * @return  BelongsTo
     */
    public function packet()
    {
        return $this->belongsTo(Packet::class);
    }

    /**
     * Get the participants collection.
     *
     * @return  BelongsToMany
     */
    public function participants()
    {
        return $this->belongsToMany(User::class, 'participants', 'session_id', 'user_id');
    }
}
