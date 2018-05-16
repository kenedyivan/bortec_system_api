<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ReceivedProduct;
use App\Item;
use App\InventoryStock;

class ReceivedProductsController extends Controller
{
    function save(Request $request){
        $resp = array();

    	$codes = $request->input('codes'); 
    	$operator_id = $request->input('operator_id'); 
        $quantity = $request->input('quantity');
        
        if($codes != null){
            $itemList = Item::where('codes', $codes)->take(1)
            ->get();

            $item =  $itemList[0];
            $receivedItem = new ReceivedProduct();
            $receivedItem->item_id = $item->id;
            $receivedItem->operator_id = $operator_id;
            $receivedItem->quantity = $quantity;
            $receivedItem->total_price = ($item->unit_price * $quantity);

            if($receivedItem->save()){
                $inventoryList = InventoryStock::where('item_id', $item->id)->take(1)
                ->get();
                if($inventoryList->count() > 0){
                    $inventory = $inventoryList[0];
                    $received_count = $inventory->received;
                    $received_count += $quantity;
                    $inventory->received = $received_count;
                    $initial_stock = $inventory->stocks;
                    $initial_stock += $quantity;
                    $inventory->stocks = $initial_stock;
                    $total_expenditure_cost = $inventory->total_expenditure_cost;
                    $total_expenditure_cost += ($item->unit_cost * $quantity);
                    $inventory->total_expenditure_cost = $total_expenditure_cost;
                    
                    if($inventory->save()){
                        $resp['msg'] = 'Item received successfully';
                        $resp['received_id'] = $receivedItem->id;
                        $resp['error'] = 0;
                        $resp['success'] = 1;
                    }
                    
                }else{
                    $resp['msg'] = 'Failed to update item inventory';
                    $resp['error'] = 0;
                    $resp['success'] = 1;
                }
            }else{
                $resp['msg'] = 'Failed receiving item';
                $resp['received_id'] = 0;
                $resp['error'] = 0;
                $resp['success'] = 1;
            }

        }else{
            $resp['msg'] = 'Item codes can\'t be null';
            $resp['error'] = 1;
            $resp['success'] = 0;
        }

        return json_encode($resp);


    }
}
