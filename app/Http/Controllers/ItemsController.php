<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Item;
use App\InventoryStock;

class ItemsController extends Controller
{
    function show(Request $request){
        $resp = array();
        $codes = $request->input('codes');

        $items = Item::where('codes',$codes)->get();

        if($items->count() > 0){
            foreach($items as $item){
                $resp['item_name'] = $item->product_name;
                $resp['units'] = $item->units;
                $resp['success'] = 1;
                $resp['error'] = 0;
                $resp['msg'] = 'Item details';
            }
        }else{
            $resp['success'] = 0;
            $resp['error'] = 1;
            $resp['msg'] = 'No item details';
            
        }

        return json_encode($resp);
    }

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
            $stock->item_id = $item->id;
            $stock->received = 0;
            $stock->sales = 0;
            $stock->stocks = 0;
            $stock->total_sales_cost = 0;

            if($stock->save()){
                $resp['msg'] = 'Item added successful';
                $resp['inventory_id'] = $stock->id;
                $resp['error'] = 0;
                $resp['success'] = 1;
            }else{
                $resp['msg'] = 'Failed to associate stock';
                $resp['inventory_id'] = $stock->id;
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
