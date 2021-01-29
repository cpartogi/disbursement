<?php


class DepositTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testGetBalanceFail()
    {
        $this->get("/deposit/2", []);
        $this->seeStatusCode(404);
        $this->seeJsonStructure(
            ['status_code',
              'message'
            ]
        );
    }

    public function testGetBalance()
    {
        $this->get("deposit/f09cb49e-613f-11eb-9afb-4205538fd788", []);
        $this->seeStatusCode(200);
        $this->seeJsonStructure(
            ['data' =>
                [
                    'balance',
                    'seller_id'
                ]
            ]
        );
    }

    public function testGetBalancelogFail() {
        $this->get("deposit/log/2", []);
        $this->seeStatusCode(404);
        $this->seeJsonStructure(
            ['status_code',
              'message',
              'seller_id'
            ]
        );
    }

    public function testGetBalancelogl() {
        $this->get("deposit/log/f09cb49e-613f-11eb-9afb-4205538fd788", []);
        $this->seeStatusCode(200);
        $this->seeJsonStructure(
            [ 'message',
              'seller_id',
              'data' => [
                  ["seller_id",
                  "log_description",
                  "deposit_before",
                  "amount",
                  "updated_date",
                  "updated_by"]
              ]
            ]
        );
    }

    public function testRegisterDepositFail() {
        $parameters = [
            'amount' => '10000',
        ];

        $this->json('POST', 'deposit/register', $parameters);
        $this->seeStatusCode(400);
        $this->seeJsonStructure(
            [
                'status_code',
                'message'
            ]
        );
    }

    public function testRegisterDeposit() {
        $parameters = [
            'seller_id' => 'f09cb49e-613f-11eb-9afb-4205538fd788',
            'amount' => 10000,
        ];

        $this->json('POST', 'deposit/register', $parameters);
        $this->seeStatusCode(201);
        $this->seeJsonStructure(
            [
                'status_code',
                'message',
                'data' => [
                    'seller_id',
                    'amount'
                ]
            ]
        );
    }

    public function testRegisterSellerFail() {
        $parameters = [
            'seller_name' => 'abc',
            'seller_email' => 'a@b.com',
            'seller_bank_code' => 'bca',
            'seller_account_name' => 'nama'
        ];

        $this->json('POST', 'seller/register', $parameters);
        $this->seeStatusCode(400);
        $this->seeJsonStructure(
            [
                'status_code',
                'message'
            ]
        );
    }

    public function testRegisterSeller() {
        $parameters = [
            'seller_name' => 'abc',
            'seller_email' => 'a@b.com',
            'seller_bank_code' => 'bca',
            'seller_account_name' => 'nama',
            'seller_account_number' => '3424234234'
        ];

        $this->json('POST', 'seller/register', $parameters);
        $this->seeStatusCode(201);
        $this->seeJsonStructure(
            [
                'status_code',
                'message',
                'data' => [
                    'seller_name',
                    'seller_email',
                    'seller_bank_code',
                    'seller_account_name',
                    'seller_account_number'
                ]
            ]
        );

    }


}
