<?php


class DisburseTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testGetDisbursementStatus()
    {
        $this->get("disburse/status/2", []);
        $this->seeStatusCode(404);
        $this->seeJsonStructure(
            ['status_code',
              'message'  
            ]    
        );
    }    

  public function testGetDisbursementlog() {
    $this->get("disburse/log/24234234", []);
    $this->seeStatusCode(404);
    $this->seeJsonStructure(
        ['status_code',
          'message'  
        ]    
    );
  }

  public function testDisburseRequest() {
    $parameters = [
        'seller_id' => 'f09cb49e-613f-11eb-9afb-4205538fd788',
        'amount' => '10000',    
        'remark' => 'secepatnya'   
    ];

    $this->json('POST', 'disburse/request', $parameters);
    $this->seeStatusCode(201);
    $this->seeJsonStructure(
        [
            'status_code', 
            'message'
        ]    
    );
  }

  public function testDisburseRequestFail() {
    $parameters = [
        'seller_id' => 'f09cb49e-613f-11eb-9afb-4205538fd788',
        'amount' => '10000'
    ];

    $this->json('POST', 'disburse/request', $parameters);
    $this->seeStatusCode(400);
    $this->seeJsonStructure(
        [
            'status_code', 
            'message'
        ]    
    );
  }

}

?>