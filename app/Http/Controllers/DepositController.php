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

        $data = array();
        //query to db
        $sqls = "SELECT amount from tb_seller_deposit where seller_id='".$seller_id."'";      
        $rs=DB::select($sqls); 

        if (count($rs)== 0) {

            return response()->json([
                "message" => "Deposit data not found",
                "status_code" => 404,
                "seller_id" => $seller_id,
                "data" => $data,
            ], 404);
        }

      
        $data["balance"] = $rs[0]->amount;
        $data["seller_id"] = $seller_id;
        return response()->json([
            "message" => "success",
            "data" => $data
        ], 200);

    }
   
    public function depositLog($seller_id) {
        $data = array();

        //query to db 
        $sqls = "SELECT b.seller_id, a.log_description, a.deposit_before, a.amount, a.updated_date, a.updated_by FROM tb_seller_deposit_log a, tb_seller_deposit b where a.deposit_id=b.deposit_id and b.seller_id='".$seller_id."' order by a.updated_date desc";

        $rs = DB::select($sqls);

        if (count($rs) == 0) {
            return response()->json([
                "message" => "Deposit log data not found",
                "status_code" => 404,
                "seller_id" => $seller_id,
                "data" => $data,
            ], 404);
        }

        foreach ($rs as $rs_) {
            array_push($data, $rs_);
        }

        return response()->json([
            "message" => "success",
            "seller_id" => $seller_id,
            "data" => $data,
        ], 200);
    }

    public function depositRegister(Request $request) {
       
        $data = array();
        //check params
        if (!empty($request->json('seller_id')) && !empty($request->json('amount'))) {
            $seller_id = $request->json('seller_id');
            $amount = $request->json('amount');

            $data["seller_id"] = $seller_id;
            $data["amount"]= $amount;

            //insert to database
            $sqli = "INSERT INTO tb_seller_deposit (deposit_id, seller_id, amount, created_by, updated_by) values (uuid(),  '".$seller_id."', '".$amount."', 'admin', 'admin')";      
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