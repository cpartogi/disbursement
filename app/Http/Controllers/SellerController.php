<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SellerController extends Controller
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
     * @OA\Post(
     *     path="/seller/register",
     *     operationId="/seller/register",
     *     tags={"seller"},
     *     @OA\RequestBody(
     *          @OA\JsonContent(
     *              type="object",
     *                  @OA\Property(property="seller_name", type="string"),
     *                  @OA\Property(property="seller_email", type="string"),
     *                  @OA\Property(property="seller_bank_code", type="string"),
     *                  @OA\Property(property="seller_account_name", type="string"),
     *                  @OA\Property(property="seller_account_number", type="string"),
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
     *              ref="#/components/schemas/registerseller"
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
     * )
     */

     /**
     * @OA\Schema(
     *   schema="registerseller",
     *   @OA\Property(
     *     property="seller_name",
     *     type="string",
     *   ),
     *   @OA\Property(
     *     property="seller_bank_code",
     *     type="string",
     *   ),
     *   @OA\Property(
     *     property="seller_account_name",
     *     type="string",
     *   ),
     *   @OA\Property(
     *     property="seller_account_number",
     *     type="string",
     *   )
     * )
     */

    public function sellerRegister(Request $request) {

        $data = array();
        //check params
        if (!empty($request->json('seller_name')) && !empty($request->json('seller_email')) && !empty($request->json('seller_bank_code')) && !empty($request->json('seller_account_name')) && !empty($request->json('seller_account_number')) ) {
            $seller_name = $request->json('seller_name');
            $seller_email = $request->json('seller_email');
            $seller_bank_code = $request->json('seller_bank_code');
            $seller_account_name = $request->json('seller_account_name');
            $seller_account_number = $request->json('seller_account_number');

            $data["seller_name"] = $seller_name;
            $data["seller_email"] = $seller_email;
            $data["seller_bank_code"] = $seller_bank_code;
            $data["seller_account_name"] = $seller_account_name;
            $data["seller_account_number"] = $seller_account_number;

            //insert to database
            $sqli = "INSERT INTO tb_seller (seller_id, seller_name, seller_email, seller_bank_code, seller_account_name, seller_account_number, created_by, updated_by) values (uuid(),  '".$seller_name."', '".$seller_email."', '".$seller_bank_code."' , '".$seller_account_name."' , '".$seller_account_number."' , 'admin', 'admin')";
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
