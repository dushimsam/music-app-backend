<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property mixed songs
 */
class Album extends Model
{
    use HasFactory;
    protected $fillable = ["title", "description","release_date"];
    public function songs(){
        return $this->hasMany(Song::class);
    }
}
