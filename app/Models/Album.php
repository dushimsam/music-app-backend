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
    protected $fillable = ["title", "description","release_date","user_id"];
    public function songs(){
        return $this->belongsToMany(Song::class);
    }
}
