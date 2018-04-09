<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InventoryStock extends Model
{
    protected $fillable = ['codes','received','sales','stocks','total_sales_price'];
}
