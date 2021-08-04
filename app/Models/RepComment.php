<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RepComment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'comment_id',
        'content'
    ];

    public function RepComments(){
        return $this->hasMany(RepComment::class);
    }

    public function RepCommentsUser(){
        return $this->hasMany(RepComment::class);
    }
}
