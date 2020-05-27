<?php

namespace Tests\Feature\Http\Controllers\Api;

use Tests\TestCase;
use App\Models\Category;
use Illuminate\Support\Facades\Lang;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\TestResponse;

class CategoryControllerTest extends TestCase
{

    use DatabaseMigrations;

    public function testIndex()
    {
        $category = factory(Category::class)->create();

        $response = $this->get(route('categories.index'));

        $response->assertStatus(200)->assertJson([$category->toArray()]);
    }

    public function testShow()
    {
        $category = factory(Category::class)->create();

        $response = $this->get(route('categories.show',['category'=>$category->id]));

        $response->assertStatus(200);
        $response->assertJson($category->toArray());
    }

    public function testInvalidationData(){
        $response = $this->json('POST',route('categories.store',[]));
        $this->asserInvalidationRequired($response);

        $response = $this->json('POST',route('categories.store'),['name'=>str_repeat('a',260),'is_active'=>'a']);
        $this->assertInvalidationMax($response);
        $this->assertInvalidationBoolean($response);


        $category = factory(Category::class)->create();
        $response = $this->json('PUT',route('categories.update',['category'=>$category->id]),[]);
        $this->asserInvalidationRequired($response);
         $response = $this->json('PUT',route('categories.update',['category'=>$category->id]),['name'=>str_repeat('a',260),'is_active'=>'a']);

        // dd($response->content());
       $this->assertInvalidationMax($response);
       $this->assertInvalidationBoolean($response);

    }

    protected function assertInvalidationMax(TestResponse $response){
        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name','is_active'])
            ->assertJsonFragment(
                [
                    Lang::get('validation.max.string',['attribute'=>'name','max'=>255])
                ]);
    }

    protected function assertInvalidationBoolean(TestResponse $response){
        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name','is_active'])
            ->assertJsonFragment(
                [
                        Lang::get('validation.boolean',['attribute'=>'is active'])
                ]);

    }

    protected function asserInvalidationRequired(TestResponse $response){
        $response
        ->assertStatus(422)
        ->assertJsonValidationErrors(['name'])
        ->assertJsonMissingValidationErrors(['is_active'])
        ->assertJsonFragment(
            [
                Lang::get('validation.required',['attribute'=>'name'])
            ]);
    }

    public function testStore(){
        $response = $this->json('POST',route('categories.store'),['name'=>'test']);

        $id = $response->json('id');
        $category = Category::find($id);

        $response->assertStatus(201)->assertJson($category->toArray());
        $this->assertTrue($response->json('is_active'));
        $this->assertNull($response->json('description'));

        $response = $this->json('POST',route('categories.store'),['name'=>'test','is_active'=>false,'description'=>'teste']);

        $response->assertJsonFragment(['description'=>'teste','is_active'=>false]);

    }

    public function testUpdate(){

        $category = factory(Category::class)->create(['is_active' => false]);
        $response = $this->json('PUT',route('categories.update',['category'=>$category->id]),['name'=>'test','is_active'=>true,'description'=>'teste']);

        $id = $response->json('id');
        $category = Category::find($id);

        $response->assertStatus(200)->assertJson($category->toArray())->assertJsonFragment(['description'=>'teste','is_active' => true]);

        $response = $this->json('PUT',route('categories.update',['category'=>$category->id]),['name'=>'test','is_active'=>true,'description'=>'']);
        $response->assertJsonFragment(['description'=>null]);
    }

}
