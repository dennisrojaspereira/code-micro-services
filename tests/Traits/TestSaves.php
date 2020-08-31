<?php
declare(strict_types=1);

namespace Tests\Traits;

use Illuminate\Foundation\Testing\TestResponse;

trait TestSaves
{

    protected abstract function model();
    protected abstract function routeStore();
    protected abstract function routeUpdate();

    protected function assertStore(array $sendData,array $testDataBase, array $testJsonData = null ) :TestResponse
    {
        /** @var TestReponse $response */
        $response = $this->json('POST',$this->routeStore(),$sendData);
        if ( $response->status() !== 201 ){
            throw new \Exception("Response status must be 201, given{$response->status()} : {$response->content()} \n");
        }
        $this->assertInDatabase($response,$testDataBase);
        $this->assertJsonResponseContent($response,$testDataBase,$testJsonData);
        return $response;
    }

    protected function assertUpdate(array $sendData,array $testDataBase, array $testJsonData = null ) :TestResponse
    {
        /** @var TestReponse $response */
        $response = $this->json('PUT',$this->routeUpdate(),$sendData);
        if ( $response->status() !== 200 ){
            throw new \Exception("Response status must be 200, given{$response->status()} : {$response->content()} \n");
        }
        $this->assertInDatabase($response,$testDataBase);
        $this->assertJsonResponseContent($response,$testDataBase,$testJsonData);
        return $response;

    }

    private function assertInDatabase($response, array $testDataBase){
        $model = $this->Model();
        $table = (new $model)->getTable();
        $this->assertDatabaseHas($table,$testDataBase + ['id'=> $response->json('id')]);
       
    }

    private function assertJsonResponseContent($response, array $testDataBase,array $testJsonData = null){
        $testResponse = $testJsonData ?? $testDataBase;
        $response->assertJsonFragment($testResponse + ['id'=> $response->json('id')]);
    }

    
}


