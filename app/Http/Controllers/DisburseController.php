<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DisburseController extends Controller
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

    public function disburseSubmit() {
        return response()->json([
            "message" => "success",
        ], 201);
    }
   
    public function disburseStatus($transaction_id) {
        $data["log_id"] = "32434";
        $data["status"] = "aktif";
        return response()->json([
            "message" => "success",
            "transaction_id" => $seller_id,
            "data" => $data,
        ], 200);    
    }

    public function disburseLog($transaction_id) {
        $data["balance"] = 50000;
        $data["transaction_id"] = $transaction_id;
        return response()->json([
            "message" => "success",
            "data" => $data,
        ], 200);
    }
}

?>