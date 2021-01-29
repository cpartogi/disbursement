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

    public function post_data ($url, $authorization, $fields) {
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS =>$fields,
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            'Authorization: Basic '.$authorization,
            'Cookie: __cfduid=d2a80202d2bfa2e1c70140f106dad361e1611844908'
        ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;

    }
    
    public function get_data ($url, $authorization) {

        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            'Authorization: Basic '.$authorization,
            'Cookie: __cfduid=d2a80202d2bfa2e1c70140f106dad361e1611844908'
        ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;
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

            //cek apakah deposit cukup
            $sqld = "SELECT amount from tb_seller_deposit WHERE seller_id='".$seller_id."'";
            $rd = DB::select($sqld);

            if (count($rd)== 0) {

                return response()->json([
                    "message" => "Deposit data not found",
                    "status_code" => 404,
                    "seller_id" => $seller_id,
                    "data" => $data,
                ], 404);
            }
    
            $balance = $rd[0]->amount;

            if ($balance < $amount) {
                return response()->json([
                    "message" => "Not Enough Balance",
                    "status_code" => 400,
                    "seller_id" => $seller_id,
                    "data" => $data,
                ], 400);
            }

            //query data account number dan bank seller
            $sqls = "SELECT seller_bank_code, seller_account_number FROM tb_seller WHERE seller_id='".$seller_id."'";
            $rs = DB::select($sqls);

            if (count($rs)== 0) {

                return response()->json([
                    "message" => "Bank account data not found",
                    "status_code" => 404,
                    "seller_id" => $seller_id,
                    "data" => $data,
                ], 404);
            }
    

            $datapush = array();
            $datapush["bank_code"] = $rs[0]->seller_bank_code;
            $datapush["account_number"] = $rs[0]->seller_account_number;
            $datapush["amount"] = $amount;
            $datapush["remark"] = $remark;

            // panggil api disbursement
            $auth = env('API_KEY').":".env('API_PASS'); 
            $authorization = base64_encode($auth);
          
            $jsondata = json_encode($datapush);

            $url = env('API_URL');

            $disb = $this->post_data($url.'disburse', $authorization, $jsondata);

            $disb_return = json_decode($disb, true);

            $transaction_id = $disb_return["id"];
  

            // tampilkan return error kalau gagal panggil api
            if ($transaction_id == "") {
                // return bad request
                return response()->json([
                "status_code" => 500,   
                "message" => "Error, please try again",
                "data" => $data,
                "result" => $disb,
                "headers" => $headers    
                ], 500); 
            }

            $amount = $disb_return["amount"];
            $status = $disb_return["status"];
            $timestamp = $disb_return["timestamp"];
            $bank_code = $disb_return["bank_code"];
            $account_number = $disb_return["account_number"];
            $beneficiary_name = $disb_return["beneficiary_name"];
            $remark = $disb_return["remark"];
            $receipt = $disb_return["receipt"];
            $time_served = $disb_return["time_served"];
            $fee = $disb_return["fee"];

           
            
            //insert to table disbursement
            $sqli = "INSERT INTO tb_disbursement (disbursement_id, seller_id, amount, remark, transaction_id, `status`, `timestamp`, receipt, time_served, fee , created_by, updated_by) VALUES (uuid(), '$seller_id', $amount, '$remark', '$transaction_id', '$status', '$timestamp', '$receipt', '$time_served', '$fee' , 'admin', 'admin')";
            DB::insert($sqli);


            ///insert to table log 
            $sqlilog = "INSERT INTO tb_transaction_log (log_id, transaction_id, amount, fee, remark, `status`, receipt, bank_code, account_number, beneficiary_name, time_served, `timestamp`, created_by) VALUES (uuid(), '$transaction_id', '$amount', '$fee', '$remark', '$status', '$receipt', '$bank_code', '$account_number', '$beneficiary_name', '$time_served', '$timestamp', 'admin') ";

            DB::insert($sqlilog);

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
        $sqlc = "SELECT transaction_id, `status` FROM tb_disbursement where transaction_id=".$transaction_id;

        $rc = DB::select($sqlc);

        if (count($rc) == 0) {
            return response()->json([
                "message" => "Transaction data not found",
                "status_code" => 404,
                "data" => $data,
            ], 404);
        }

        $laststatus = $rc[0]->status;
        // panggil api 3rd party, cek status terakhir, jika belum ada ke database - insert ke tabel log dan update table disbursement.  jika sudah ada tampilkan hasil
        // panggil api disbursement

        if ($laststatus == "PENDING") {

            $auth = env('API_KEY').":".env('API_PASS'); 
            $authorization = base64_encode($auth);
            
            $url = env('API_URL')."disburse/".$transaction_id;

            $disb = $this->get_data($url, $authorization);

            $disb_return = json_decode($disb, true);

            $transaction_id = $disb_return["id"];

            // tampilkan return error kalau gagal panggil api
            if ($transaction_id == "") {
                // return bad request
                return response()->json([
                "status_code" => 500,   
                "message" => "Error, please try again",
                "data" => $data,
                "result" => $disb,
                "headers" => $headers    
                ], 500); 
            }

            $amount = $disb_return["amount"];
            $status = $disb_return["status"];
            $timestamp = $disb_return["timestamp"];
            $bank_code = $disb_return["bank_code"];
            $account_number = $disb_return["account_number"];
            $beneficiary_name = $disb_return["beneficiary_name"];
            $remark = $disb_return["remark"];
            $receipt = $disb_return["receipt"];
            $time_served = $disb_return["time_served"];
            $fee = $disb_return["fee"];

            $laststatus = $rc[0]->status;

            if ($laststatus != $status) {

                //insert ke tabel log
                $sqlilog = "INSERT INTO tb_transaction_log (log_id, transaction_id, amount, fee, remark, `status`, receipt, bank_code, account_number, beneficiary_name, time_served, `timestamp`, created_by) VALUES (uuid(), '$transaction_id', '$amount', '$fee', '$remark', '$status', '$receipt', '$bank_code', '$account_number', '$beneficiary_name', '$time_served', '$timestamp', 'admin') ";
                DB::insert($sqlilog);

                // cek apakah status sukses 
                if ($status == "SUCCESS") {

                    //ambil deposit seller
                    $sqld = "SELECT a.amount, a.deposit_id from tb_seller_deposit a, tb_disbursement b WHERE a.seller_id = b.seller_id AND b.transaction_id=$transaction_id";
                    $rd = DB::select($sqld);

                    $balance = $rd[0]->amount;
                    $deposit_id = $rd[0]->deposit_id;
                    // insert ke tabel deposit log amount
                    $sqldep = "INSERT INTO tb_seller_deposit_log (log_id, deposit_id, log_description, deposit_before, amount, created_by, updated_by) VALUES (uuid(), '$deposit_id', 'Disbursement transaction id: $transaction_id', $balance, $amount, 'admin', 'admin')";
                    DB::insert($sqldep);

                    $dep_before = $balance - $amount;
                    // insert ke tabel deposit log fee
                    $sqlfee = "INSERT INTO tb_seller_deposit_log (log_id, deposit_id, log_description, deposit_before, amount, created_by, updated_by) VALUES (uuid(), '$deposit_id', 'Transaction fee of transaction id: $transaction_id', $dep_before, $fee, 'admin', 'admin')";
                    DB::insert($sqlfee);


                    // update balance
                    $last_balance = $dep_before - $fee;
                    $sqlu = "UPDATE tb_seller_deposit SET amount=$last_balance WHERE deposit_id='$deposit_id'";
                    DB::update($sqlu);
                }

                //update data tabel disbursement 
                $sqlu = "UPDATE tb_disbursement SET `status`='$status', receipt='$receipt', time_served='$time_served', `timestamp`='$timestamp', updated_date=now(), updated_by='admin'  WHERE transaction_id=$transaction_id";
                DB::update($sqlu);
            }   
        }    
        //query data 
        $sqls = "SELECT transaction_id, amount, `status`, `timestamp`, remark, receipt, time_served, fee   FROM tb_disbursement where transaction_id=$transaction_id";
        $rs = DB::select($sqls);

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
        $sqls = "SELECT log_id, transaction_id, amount, fee, remark, `status`, receipt, bank_code, account_number, beneficiary_name, time_served, `timestamp`, created_date FROM tb_transaction_log WHERE transaction_id=$transaction_id ORDER BY created_date DESC";

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