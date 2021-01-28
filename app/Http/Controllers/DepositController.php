<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DepositController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function depositBalance($seller_id) {
        $data["balance"] = 50000;
        $data["seller_id"] = $seller_id;
        return response()->json([
            "message" => "success",
            "data" => $data,
        ], 200);

    }
   
    public function depositLog($seller_id) {

        $data["log_id"] = "32434";
        $data["status"] = "aktif";
        return response()->json([
            "message" => "success",
            "seller_id" => $seller_id,
            "data" => $data,
        ], 200);
    }

    public function depositRegister(Request $request) {
       
        $data = array();
        //check params
        if (!empty($request->json('seller_id')) || !empty($request->json('amount'))) {
            $seller_id = $request->json('seller_id');
            $amount = $request->json('amount');

            $data["seller_id"] = $seller_id;
            $data["amount"]= $amount;

            //insert to database
            $sqli = "INSERT tb_seller_deposit (deposit_id, seller_id, amount, created_by, updated_by) values (uuid(),  '".$seller_id."', '".$amount."', 'admin', 'admin')";      
            DB::insert($sqli); 

        } else {
           // return bad request
           return response()->json([
            "status_code" => 400,   
            "message" => "Bad Request",
            "data" => $data
            ], 400); 
        }
       
        // response
        return response()->json([
            "status_code" => 201,
            "message" => "Data created",
            "data"  => $data
        ], 201);
    }
}

?>