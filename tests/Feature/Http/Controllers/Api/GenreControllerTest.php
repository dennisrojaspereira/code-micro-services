<?php

namespace Tests\Feature\Http\Controllers\Api;

use Tests\TestCase;
use App\Models\Genre;
use Illuminate\Support\Facades\Lang;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\TestResponse;

class GenreControllerTest extends TestCase
{

    use DatabaseMigrations;

    public function testIndex()
    {
        $Genre = factory(Genre::class)->create();

        $response = $this->get(route('genres.index'));

        $response->assertStatus(200)->assertJson([$Genre->toArray()]);
    }

    public function testShow()
    {
        $Genre = factory(Genre::class)->create();

        $response = $this->get(route('genres.show',['genre'=>$Genre->id]));

        $response->assertStatus(200);
        $response->assertJson($Genre->toArray());
    }

    public function testInvalidationData(){
        $response = $this->json('POST',route('genres.store',[]));
        $this->asserInvalidationRequired($response);

        $response = $this->json('POST',route('genres.store'),['name'=>str_repeat('a',260),'is_active'=>'a']);
        $this->assertInvalidationMax($response);
        $this->assertInvalidationBoolean($response);


        $Genre = factory(Genre::class)->create();
        $response = $this->json('PUT',route('genres.update',['genre'=>$Genre->id]),[]);
        $this->asserInvalidationRequired($response);
         $response = $this->json('PUT',route('genres.update',['genre'=>$Genre->id]),['name'=>str_repeat('a',260),'is_active'=>'a']);

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
        $response = $this->json('POST',route('genres.store'),['name'=>'test']);

        $id = $response->json('id');
        $Genre = Genre::find($id);

        $response->assertStatus(201)->assertJson($Genre->toArray());
        $this->assertTrue($response->json('is_active'));

        $response = $this->json('POST',route('genres.store'),['name'=>'test','is_active'=>false]);

        $response->assertJsonFragment(['name'=>'test','is_active'=>false]);

    }

    public function testUpdate(){

        $Genre = factory(Genre::class)->create(['is_active' => false]);
        $response = $this->json('PUT',route('genres.update',['genre'=>$Genre->id]),['name'=>'test','is_active'=>true]);

        $id = $response->json('id');
        $Genre = Genre::find($id);

        $response->assertStatus(200)->assertJson($Genre->toArray())->assertJsonFragment(['name'=>'test','is_active' => true]);

        $response = $this->json('PUT',route('genres.update',['genre'=>$Genre->id]),['name'=>'test','is_active'=>true]);
        $response->assertJsonFragment(['name'=>'test']);
    }

    public function testDelete(){

        $genre = factory(Genre::class)->create();
        $response = $this->json('DELETE',route('genres.destroy',['genre'=>$genre->id]));
        $response->assertStatus(204);
        $this->assertNull(Genre::find($genre->id));
        $this->assertNotNull(Genre::withTrashed()->find($genre->id));
    }

}
