<?php

namespace Tests\Feature\Http\Controllers\Api;

use Tests\TestCase;
use App\Models\CastMember;
use Illuminate\Support\Facades\Lang;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\TestResponse;
use Tests\Traits\TestSaves;
use Tests\Traits\TestValidations;

class CastMemberControllerTest extends TestCase
{

    use DatabaseMigrations,TestValidations,TestSaves;

    private $castMember;

    protected function setUp():void
    {
        parent::setUp();
        $this->castMember = factory(CastMember::class)->create([
            'type' => CastMember::TYPE_DIRECTOR
        ]);
    }

    public function testIndex()
    {
        $response = $this->get(route('cast_members.index'));

        $response->assertStatus(200)->assertJson([$this->castMember->toArray()]);
    }

    public function testShow()
    {
        $response = $this->get(route('cast_members.show',['castMember'=>$this->castMember->id]));
        $response->assertStatus(200);
        $response->assertJson([$this->castMember->toArray()]);
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

    }

    public function testStore(){
        $data = ['name'=>'test','type'=>1];
        $response = $this->assertStore($data,$data+['type'=>1,'deleted_at'=>null]);
        $response->assertJsonStructure([
            'created_at','updated_at'
        ]);
        $data = ['name'=>'test','type'=>1];
        $this->assertStore($data,$data+['type'=>1,'deleted_at'=>null]);
    }

    public function testUpdate(){

        $this->castMember = factory(CastMember::class)->create([
            'type'=> 1
        ]);


        $data = ([
            'name'=>'test',
            'type'=>1
        ]);
        $response = $this->assertUpdate($data,array_merge($data,['type'=>1]));
        $response->assertJsonStructure([
            'created_at','updated_at'
        ]);

        $data['type'] = 1;
        $response = $this->assertUpdate($data,array_merge($data,['type'=>1]));
        $response->assertJsonStructure([
            'created_at','updated_at'
        ]);

        $data['type'] = 2;
        $response = $this->assertUpdate($data,array_merge($data,['type'=>2]));
        $response->assertJsonStructure([
            'created_at','updated_at'
        ]);

    }

    public function testDelete(){

        $response = $this->json('DELETE',route('cast_members.destroy',['cast_member'=>$this->castMember->id]));
        $response->assertStatus(204);
        $this->assertNull(CastMember::find($this->castMember->id));
        $this->assertNotNull(CastMember::withTrashed()->find($this->castMember->id));
    }

    protected function routeStore(){
        return route('cast_members.store');
    }

    protected function routeUpdate(){
        return route('cast_members.update',['cast_member'=>$this->castMember->id]);
    }

    protected function model(){
        return CastMember::class;
    }

}
