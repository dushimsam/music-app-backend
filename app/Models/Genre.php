<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property mixed songs
 */
class Genre extends Model
{
    use HasFactory;

    protected $fillable = ["title"];

    public function songs(){
        return $this->hasMany(Song::class);
    }
}
