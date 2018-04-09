<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $primaryKey = 'codes';

    /**
     * Get the inventory stock record associated with the item.
     */
    public function inventoryStock(){
        return $this->hasOne('App\InventoryStock','codes', 'codes');
    }
}
