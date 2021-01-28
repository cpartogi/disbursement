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

    public function disburseSubmit(Request $request) {
        $data = array();

        // check param
        if (!empty($request->json('seller_id')) && !empty($request->json('amount')) && !empty($request->json('remark')) ) {
            $seller_id = $request->json('seller_id');
            $amount = $request->json('amount');
            $remark = $request->json('remark'); 


            $data["seller_id"] = $seller_id;
            $data["amount"] = $amount;
            $data["remark"] = $remark;

            //insert to db
            $sqli = "INSERT INTO tb_disbursement (disbursement_id, seller_id, amount, remark, created_by, updated_by) values (uuid(), '".$seller_id."', '".$amount."', '".$remark."', 'admin', 'admin')";
            DB::insert($sqli);
        } else {
            // return bad request
            return response()->json([
            "status_code" => 400,   
            "message" => "Bad Request",
            "data" => $data
            ], 400); 
        }
        
        return response()->json([
            "status_code" => 201,
            "message" => "Data created",
            "data"  => $data
        ], 201);
    }
   
    public function disburseStatus($transaction_id) {
        $data = array();
        //query to db
        $sqls = "SELECT transaction_id, amount, fee, remark,`status`, receipt,time_served, `timestamp` FROM tb_disbursement where transaction_id=".$transaction_id;

        $rs = DB::select($sqls);

        if (count($rs) == 0) {
            return response()->json([
                "message" => "Transaction data not found",
                "status_code" => 404,
                "data" => $data,
            ], 404);
        }

        foreach ($rs as $rs_) {
            array_push($data, $rs_);
        }

        return response()->json([
            "message" => "success",
            "data" => $data,
        ], 200);    
    }

    public function disburseLog($transaction_id) {
        $data = array();

        //query to db
        $sqls = "SELECT log_id, transaction_id, amount, fee, remark, status, receipt, bank_code, account_number, beneficiary_name, time_served, `timestamp`, created_date from tb_transaction_log where transaction_id=".$transaction_id;

        $rs = DB::select($sqls);

        if (count($rs) == 0) {
            return response()->json([
                "message" => "Transaction data not found",
                "status_code" => 404,
                "data" => $data,
            ], 404);
        }

        foreach ($rs as $rs_) {
            array_push($data, $rs_);
        }

        return response()->json([
            "message" => "success",
            "data" => $data,
        ], 200);    

      
        return response()->json([
            "message" => "success",
            "data" => $data,
        ], 200);
    }
}

?>