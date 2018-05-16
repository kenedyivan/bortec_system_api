<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Sale;
use App\Item;
use App\InventoryStock;
use GuzzleHttp;
use Goutte\Client;
use GuzzleHttp\Client as GuzzleClient;

class SalesController extends Controller
{

    function save(Request $request){
        $resp = array();
        $client = new GuzzleHttp\Client();
        $res = $client->request('GET','http://api.openweathermap.org/data/2.5/weather?q=Kampala,ug&appid=943841f833832e34b9f2132e39a27e47');

        $fuel = $this->get_fuel_price();
        if($fuel == ""){
            $fuel = 0;
        }

        if($res->getStatusCode() == 200){
            $data = json_decode($res->getBody(),true);
            $weather = $data['weather'][0]['main'];
            $temp = $data['main']['temp'];
            $pressure = $data['main']['pressure'];
            $humidity = $data['main']['humidity'];
            $temp_min = $data['main']['temp_min'];
            $temp_max = $data['main']['temp_max'];
            $wind_speed = $data['wind']['speed'];
        }else{
            // Returned with error code
        }

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
                    $saleItem->weather = $weather;
                    $saleItem->temp = $temp;
                    $saleItem->temp_min = $temp_min;
                    $saleItem->temp_max = $temp_max;
                    $saleItem->pressure = $pressure;
                    $saleItem->humidity = $humidity;
                    $saleItem->wind_speed = $wind_speed;
                    $saleItem->fuel_price = $fuel;
                    
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

    function get_fuel_price(){
        $client = new Client();

        // Go to the symfony.com website
        $crawler = $client->request('GET', 'https://www.numbeo.com/gas-prices/in/Kampala');

        $goutteClient = new Client();
        $guzzleClient = new GuzzleClient(array(
            'timeout' => 60,
        ));
        $goutteClient->setClient($guzzleClient);

        $dd = $crawler->filter('td.priceValue ');
        $res = preg_replace("/[^0-9.]/", "", $dd->first()->text());
        return $res;
    }
}
