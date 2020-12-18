<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Quiz extends Model
{
    use HasFactory;

    protected $fillable = [
        'packet_id',
        'text'
    ];

    public static $disk = 'public';
    public static $folder_image = 'quizzes';

    /**
     * Get the packet object.
     *
     * @return  BelongsTo
     */
    public function packet()
    {
        return $this->belongsTo(Packet::class);
    }

    /**
     * Get choices collection;
     *
     * @return  HasMany
     */
    public function choices()
    {
        return $this->hasMany(Choice::class);
    }

    /**
     * Get the quiz's image.
     */
    public function image()
    {
        return $this->morphOne(Image::class, 'imageable');
    }
}
