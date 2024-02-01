<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    use HasFactory;
    protected $fillable=['course_id'];

    public function content(){
        return $this->hasManyThrough(Content::class, Course::class);
    }
}
