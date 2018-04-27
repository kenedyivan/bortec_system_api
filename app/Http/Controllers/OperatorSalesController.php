<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Sale;
use App\Item;

class OperatorSalesController extends Controller
{
    function getOperatorSalesLog(Request $request){
        $resp = array();

        $operator_id = $request->input('operator_id');
        if($operator_id != ''){
            $sales_logs = Sale::where('operator_id', $operator_id)->orderBy('id','desc')->get();

            if($sales_logs->count() > 0){
                $allLogs = array();
                foreach($sales_logs as $log){
                    $logData = array();
                    $logData['id'] = $log->id;
                    $logData['item_name'] = $log->item->product_name;
                    $logData['quantity'] = $log->quantity;
                    $logData['date'] = date_format($log->created_at,"Y/m/d H:i:s");
                    array_push($allLogs,$logData);
                }
                $resp['msg'] = 'Operator sales logs';
                $resp['logs'] = $allLogs;
                $resp['success'] = 1;
                $resp['error'] = 0;
            }else{
                $resp['msg'] = 'No sales found';
                $resp['success'] = 0;
                $resp['error'] = 1;
            }
        }else{
            $resp['msg'] = 'No operator id found';
            $resp['success'] = 0;
            $resp['error'] = 2;
        }

        return json_encode($resp);
    }
}
