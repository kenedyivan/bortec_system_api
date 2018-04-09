<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Item;
use App\InventoryStock;

class ItemsController extends Controller
{
    function save(Request $request){
        $resp = array();

    	$codes = $request->input('codes');
    	$product_name = $request->input('product_name'); 
    	$units = $request->input('units'); 
    	$unit_price = $request->input('unit_price'); 
        $remarks = $request->input('remarks'); 
        
        $item = new Item();
        $item->codes = $codes;
        $item->product_name = $product_name;
        $item->units = $units;
        $item->unit_price = $unit_price;
        $item->remarks = $remarks;

        if($item->save()){
            $stock = new InventoryStock();
            $stock->codes = $codes;
            $stock->received = 0;
            $stock->sales = 0;
            $stock->stocks = 0;
            $stock->total_sales_price = 0;

            if($stock->save()){
                $resp['msg'] = 'Item added successful';
                $resp['codes'] = $stock->codes;
                $resp['error'] = 2;
                $resp['success'] = 0;
            }else{
                $resp['msg'] = 'Failed to associate stock';
                $resp['codes'] = $stock->codes;
                $resp['error'] = 3;
                $resp['success'] = 0;
            }
           
        }else{
            $resp['msg'] = 'Process up failed';
            $resp['codes'] = 0;
            $resp['error'] = 1; 
            $resp['success'] = 0;
        }

        return json_encode($resp);
    }
}
