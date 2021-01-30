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

    /**
     * @OA\Get(
     *     path="/deposit/balance/{seller_id}",
     *     operationId="/deposit/balance/seller_id",
     *     tags={"deposit"},
     *     @OA\Parameter(
     *         name="seller_id",
     *         in="path",
     *         description="Seller id",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Returns seller deposit balance",
     *         @OA\JsonContent(
     *           @OA\Property(
     *              property="message",
     *              type="string"
     *            ),
     *           @OA\Property(
     *              property="status_code",
     *              type="integer"
     *            ),
     *            @OA\Property(
     *              property="data",
     *              ref="#/components/schemas/balanceinfo"
     *            )
     *          )
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="Error: Not Found",
     *         @OA\JsonContent(
     *           @OA\Property(
     *              property="message",
     *              type="string"
     *            ),
     *           @OA\Property(
     *              property="status_code",
     *              type="integer"
     *            ),
     *           @OA\Property(
     *              property="seller_id",
     *              type="string"
     *            ),
     *            @OA\Property(
     *              property="data",
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/emptyarray")
     *            )
     *          )
     *     ),
     * )
     */

    /**
     * @OA\Schema(
     *   schema="balanceinfo",
     *   @OA\Property(
     *     property="balance",
     *     type="integer",
     *   ),
     *   @OA\Property(
     *     property="seller_id",
     *     type="string",
     *   )
     * )
     */

     /**
     * @OA\Schema(
     *   schema="emptyarray"
     * )
     */


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
            "status_code" => 200,
            "data" => $data
        ], 200);

    }

    /**
     * @OA\Get(
     *     path="/deposit/log",
     *     operationId="/deposit/log",
     *     tags={"deposit"},
     *     @OA\Parameter(
     *         name="seller_id",
     *         in="query",
     *         description="Seller id",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *   @OA\Parameter(ref="#/components/parameters/get_start_request_parameter"),
     *   @OA\Parameter(ref="#/components/parameters/get_limit_request_parameter"),

     *     @OA\Response(
     *         response="200",
     *         description="Returns seller deposit log",
     *         @OA\JsonContent(
     *           @OA\Property(
     *              property="message",
     *              type="string"
     *            ),
     *           @OA\Property(
     *              property="status_code",
     *              type="integer"
     *            ),
     *            @OA\Property(
     *              property="data",
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/balancelog")
     *            )
     *          )
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="Error: Not Found",
     *         @OA\JsonContent(
     *           @OA\Property(
     *              property="message",
     *              type="string"
     *            ),
     *           @OA\Property(
     *              property="status_code",
     *              type="integer"
     *            ),
     *           @OA\Property(
     *              property="seller_id",
     *              type="string"
     *            ),
     *            @OA\Property(
     *              property="data",
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/emptyarray")
     *            )
     *          )
     *     ),
     * )
     */

    /**
     * @OA\Schema(
     *   schema="balancelog",
     *   @OA\Property(
     *     property="log_description",
     *     type="string",
     *   ),
     *   @OA\Property(
     *     property="deposit_before",
     *     type="integer",
     *   ),
     *   @OA\Property(
     *     property="amount",
     *     type="integer",
     *   ),
     *   @OA\Property(
     *     property="updated_date",
     *     type="string",
     *   ),
     *   @OA\Property(
     *     property="updated_by",
     *     type="string",
     *   ),
     * )
     */

    /**
    * @OA\Parameter(
    *   parameter="get_limit_request_parameter",
    *   name="limit",
    *   description="Limit the number of results",
    *   in="query",
    *   @OA\Schema(
    *     type="number", default=10
    *   )
    * ),
    * @OA\Parameter(
    *   parameter="get_start_request_parameter",
    *   name="start",
    *   description="Start from",
    *   in="query",
    *   @OA\Schema(
    *     type="number", default=0
    *   )
    * ),
    */

    public function depositLog(Request $request) {
        $seller_id = $request->input("seller_id");
        $start = $request->input("start");
        $limit = $request->input("limit");
        $data = array();

        //query to db
        $sqls = "SELECT a.log_description, a.deposit_before, a.amount, a.updated_date, a.updated_by FROM tb_seller_deposit_log a, tb_seller_deposit b where a.deposit_id=b.deposit_id and b.seller_id='".$seller_id."' order by a.updated_date desc limit $start, $limit";

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
            "status_code" => 404,
            "seller_id" => $seller_id,
            "data" => $data,
        ], 200);
    }

    /**
     * @OA\Post(
     *     path="/deposit/register",
     *     operationId="/deposit/register",
     *     tags={"deposit"},
     *     @OA\RequestBody(
     *          @OA\JsonContent(
     *              type="object",
     *                  @OA\Property(property="seller_id", type="string"),
     *                  @OA\Property(property="amount", type="integer")
     *         )
    *      ),
     *     @OA\Response(
     *         response="201",
     *         description="Returns succes register seller deposit balance",
     *         @OA\JsonContent(
     *           @OA\Property(
     *              property="message",
     *              type="string"
     *            ),
     *           @OA\Property(
     *              property="status_code",
     *              type="integer"
     *            ),
     *            @OA\Property(
     *              property="data",
     *              ref="#/components/schemas/balanceinforeg"
     *            )
     *          )
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Error: Bad Request",
     *         @OA\JsonContent(
     *           @OA\Property(
     *              property="message",
     *              type="string"
     *            ),
     *           @OA\Property(
     *              property="status_code",
     *              type="integer"
     *            ),
     *            @OA\Property(
     *              property="data",
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/emptyarray")
     *            )
     *          )
     *     ),
     *     @OA\Response(
     *         response="409",
     *         description="Error: Conflict",
     *         @OA\JsonContent(
     *           @OA\Property(
     *              property="message",
     *              type="string"
     *            ),
     *           @OA\Property(
     *              property="status_code",
     *              type="integer"
     *            ),
     *            @OA\Property(
     *              property="data",
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/balanceinforeg")
     *            )
     *          )
     *     ),
     * )
     */

     /**
     * @OA\Schema(
     *   schema="balanceinforeg",
     *   @OA\Property(
     *     property="amount",
     *     type="integer",
     *   ),
     *   @OA\Property(
     *     property="seller_id",
     *     type="string",
     *   )
     * )
     */



    public function depositRegister(Request $request) {

        $data = array();
        //check params
        if (!empty($request->json('seller_id')) && !empty($request->json('amount'))) {
            $seller_id = $request->json('seller_id');
            $amount = $request->json('amount');

            $data["seller_id"] = $seller_id;
            $data["amount"]= $amount;

            //check if data exist
            $sqlc = "SELECT seller_id FROM tb_seller_deposit where seller_id='$seller_id'";
            $rc = DB::select($sqlc);

            if (count($rc) != 0) {

                return response()->json([
                    "message" => "Seller id already exist",
                    "status_code" => 409,
                    "data" => $data,
                ], 409);
            }

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
