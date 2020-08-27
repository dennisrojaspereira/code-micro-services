<?php

namespace Tests\Feature\Http\Controllers\Api;

use Tests\TestCase;
use App\Models\Category;
use Illuminate\Support\Facades\Lang;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\TestResponse;
use Tests\Traits\TestSaves;
use Tests\Traits\TestValidations;

class CategoryControllerTest extends TestCase
{

    use DatabaseMigrations,TestValidations,TestSaves;

    private $category;

    protected function setUp():void
    {
        parent::setUp();
        $this->category = factory(Category::class)->create();
    }

    public function testIndex()
    {
        $response = $this->get(route('categories.index'));

        $response->assertStatus(200)->assertJson([$this->category->toArray()]);
    }

    public function testShow()
    {
        $response = $this->get(route('categories.show',['category'=>$this->category->id]));
        $response->assertStatus(200);
        $response->assertJson($this->category->toArray());
    }

    public function testInvalidationData()
    {

        $data = [
            'name' => ''
        ];
        $this->assertInvalidationInStoreAction($data,'required');
        $this->assertInvalidationInUpdateAction($data,'required');

        $data = [
            'name'=>str_repeat('a',260)
        ];
        $this->assertInvalidationInStoreAction($data,'max.string',['max'=>255]);
        $this->assertInvalidationInUpdateAction($data,'max.string',['max'=>255]);

        $data = [
            'is_active'=>'a'
        ];
        $this->assertInvalidationInStoreAction($data,'boolean');
        $this->assertInvalidationInUpdateAction($data,'boolean');
    }

    public function testStore(){
        $data = ['name'=>'test'];
        $response = $this->assertStore($data,$data+['description'=>null,'is_active' => true,'deleted_at'=>null]);
        $response->assertJsonStructure([
            'created_at','updated_at'
        ]);
        $data = ['name'=>'test','description'=>'description','is_active'=>false];
        $this->assertStore($data,$data+['description'=>'description','is_active' => false,'deleted_at'=>null]);
    }

    public function testUpdate(){

        $this->category = factory(Category::class)->create([
            'is_active' => false,
            'description'=>'description',
            'is_active'=> false
        ]);

        $data = ([
            'name'=>'test',
            'is_active'=>true,
            'description'=>'teste'
        ]);
        $response = $this->assertUpdate($data,$data +["deleted_at" => null]);
        $response->assertJsonStructure([
            'created_at','updated_at'
        ]);

        $data = ([
            'name'=>'test',
            'is_active'=>true,
            'description'=>''
        ]);
        $response = $this->assertUpdate($data,array_merge($data,['description'=>null]));
        $response->assertJsonStructure([
            'created_at','updated_at'
        ]);

        $data['description'] = 'test';
        $response = $this->assertUpdate($data,array_merge($data,['description'=>'test']));
        $response->assertJsonStructure([
            'created_at','updated_at'
        ]);

        $data['description'] = null;
        $response = $this->assertUpdate($data,array_merge($data,['description'=>null]));
        $response->assertJsonStructure([
            'created_at','updated_at'
        ]);

    }

    public function testDelete(){

        $response = $this->json('DELETE',route('categories.destroy',['category'=>$this->category->id]));
        $response->assertStatus(204);
        $this->assertNull(Category::find($this->category->id));
        $this->assertNotNull(Category::withTrashed()->find($this->category->id));
    }

    protected function routeStore(){
        return route('categories.store');
    }

    protected function routeUpdate(){
        return route('categories.update',['category'=>$this->category->id]);
    }

    protected function model(){
        return Category::class;
    }

}
