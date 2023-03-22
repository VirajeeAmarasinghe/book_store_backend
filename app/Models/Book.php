<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'isbn', 'user_id', 'cover_image'
    ];

    /**
     * Get the user who owns the book
     *
     * @return void
     */
    public function user(){

        return $this->belongsTo(User::class);

    }

}
