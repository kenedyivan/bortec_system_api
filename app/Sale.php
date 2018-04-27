<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    public function item(){
        return $this->belongsTo('App\Item');
    }
}
