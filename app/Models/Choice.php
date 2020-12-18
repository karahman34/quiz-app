<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Choice extends Model
{
    use HasFactory;

    protected $fillable = [
        'quiz_id',
        'text',
        'is_right',
    ];

    public static $disk = 'public';
    public static $folder_image = 'choices';

    /**
     * Get the quiz object.
     *
     * @return  BelongsTo
     */
    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }

    /**
     * Get the choice's image.
     */
    public function image()
    {
        return $this->morphOne(Image::class, 'imageable');
    }
}
