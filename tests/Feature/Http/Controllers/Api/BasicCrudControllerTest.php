<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Http\Controllers\Api\BasicCrudController;
use Mockery;
use Tests\TestCase;
use App\Models\Category;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Tests\Traits\TestSaves;
use Illuminate\Http\Request;
use Tests\Traits\TestValidations;
use Illuminate\Support\Facades\Lang;
use Tests\Stubs\Models\CategoryStub;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Stubs\Controllers\CategoryControllerStub;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Validation\ValidationException;
use ReflectionClass;

class BasicCrudControllerTest extends TestCase
{

    private $controller;

    protected function setUp() : void
    {
        parent::setUp();
        CategoryStub::createTable();
        $this->controller = new CategoryControllerStub();
    }

    protected function tearDown() : void 
    {
       CategoryStub::dropTable();
       parent::tearDown();
    }

    public function testIndex(){
        /** @var CategoryStub $category */
        $category = CategoryStub::create(['name'=> 'test_name', 'description'=> 'test_description']);
        $controller = new CategoryControllerStub;
        $result = $this->controller->index()->toArray();
        $this->assertEquals([$category->toArray()],$result);
    }
    
    public function testInvalidationDataInStore(){
        $this->expectException(ValidationException::class);
        $request = \Mockery::mock(Request::class);
        $request->shouldReceive('all')->once()->andReturn(['name'=>'']);
        $this->controller->store($request);
    }

    public function testStore(){
        $request = \Mockery::mock(Request::class);
        $request->shouldReceive('all')->once()->andReturn(['name'=>'test_name','description'=>'test_description']);
        $obj = $this->controller->store($request);
        $this->assertEquals(CategoryStub::find(1)->toArray(),$obj->toArray());
    }

    public function testIfFindOrFailFetchModel(){
        $reflectionClass = new ReflectionClass(BasicCrudController::class);
        $reflectionMethod = $reflectionClass->getMethod('findOrFail');
        $reflectionMethod->setAccessible(true);
        /** @var CategoryStub $category */
        $category = CategoryStub::create(['name'=> 'test_name', 'description'=> 'test_description']);
        $result = $reflectionMethod->invokeArgs($this->controller,[$category->id]);
        $this->assertInstanceOf(CategoryStub::class,$result);
    }

    public function testIfFindOrFailFetchModelWhenIdInvalid(){
        $this->expectException(ModelNotFoundException::class);
        $reflectionClass = new ReflectionClass(BasicCrudController::class);
        $reflectionMethod = $reflectionClass->getMethod('findOrFail');
        $reflectionMethod->setAccessible(true);
        /** @var CategoryStub $category */
        $result = $reflectionMethod->invokeArgs($this->controller,[0]);
        $this->assertInstanceOf(CategoryStub::class,$result);
    }

    public function testShow()
    {
        $category = CategoryStub::create(['name'=>'test_name','description'=>'test_description']);
        $result = $this->controller->show($category->id);
        $this->assertEquals($result->toArray(),CategoryStub::find(1)->toArray());
    }

    public function testUpdate()
    {
        $category = CategoryStub::create(['name'=>'test_name','description'=>'test_description']);
        $request = \Mockery::mock(Request::class);
        $request->shouldReceive('all')->once()->andReturn(['name'=>'test_changed','description'=>'test_description_changed']);
        $result = $this->controller->update($request,$category->id);
        $this->assertEquals($result->toArray(),CategoryStub::find(1)->toArray());
    }

    public function testDestroy()
    {
        $category = CategoryStub::create(['name'=>'test_name','description'=>'test_description']);
        $response = $this->controller->destroy($category->id);
        $this->createTestResponse($response)->assertStatus(204);
        $this->assertCount(0,CategoryStub::all());
    }






}
