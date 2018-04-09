<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Sale;
use App\Item;
use App\InventoryStock;

class SalesController extends Controller
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
           
            $inventoryList = InventoryStock::where('item_id', $item->id)->take(1)
            ->get();
            if($inventoryList->count() > 0){
                $inventory = $inventoryList[0];
                if($quantity < $inventory->stocks){
                    $saleItem = new Sale();
                    $saleItem->item_id = $item->id;
                    $saleItem->operator_id = $operator_id;
                    $saleItem->quantity = $quantity;
                    $saleItem->total_price = ($item->unit_price * $quantity);
                    
                    if($saleItem->save()){
                        $sales_count = $inventory->sales;
                        $sales_count += $quantity;
                        $inventory->sales = $sales_count;
                        $initial_stock = $inventory->stocks;
                        $initial_stock -= $quantity;
                        $inventory->stocks = $initial_stock;
                        $total_sales_cost = $inventory->total_sales_cost;
                        $total_sales_cost += ($item->unit_price * $quantity);
                        $inventory->total_sales_cost = $total_sales_cost;

                        if($inventory->save()){
                            $resp['msg'] = 'Item sold successfully';
                            $resp['sales_id'] = $saleItem->id;
                            $resp['error'] = 0;
                            $resp['success'] = 1;
                        }else{
                            //failed stock update
                            $resp['msg'] = 'Stock update failed';
                            $resp['error'] = 5;
                            $resp['success'] = 0;
                        }
                    }else{
                        // sale saved
                        $resp['msg'] = 'Sale failed';
                        $resp['error'] = 4;
                        $resp['success'] = 0;
                    }

                }else{
                    // out of stock
                    $resp['msg'] = 'Out of stock';
                    $resp['stock_amount'] = $inventory->stocks;
                    $resp['error'] = 3;
                    $resp['success'] = 0;
                }
                
                
            }else{
                $resp['msg'] = 'No such inventory found';
                $resp['error'] = 2;
                $resp['success'] = 0;
            }

        }else{
            $resp['msg'] = 'Item codes can\'t be null';
            $resp['error'] = 1;
            $resp['success'] = 0;
        }

        return json_encode($resp);


    }
}
