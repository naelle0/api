<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Stuff extends Model
{
    use SoftDeletes;
    protected $fillable = ['name', 'category'];

    public function lendings(){
        return $this->hasMany(Lending::class);
    }

    public function stuffstock(){
        return $this->hasOne(Stuffstock::class);
    }
    
    public function inboundStuffs(){
        return $this->hasMany(InboundStuff::class);
    }

}
