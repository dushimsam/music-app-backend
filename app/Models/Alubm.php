<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property mixed user
 * @property mixed songs
 */
class Alubm extends Model
{
    use HasFactory;

    protected $fillable = ["title", "description","release_date","cover_img_url"];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function songs(){
        return $this->hasMany(Song::class);
    }

}
