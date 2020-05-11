<?php

namespace Tests\Feature\Models;

use Tests\TestCase;
use App\Models\Genre;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class GenreTest extends TestCase
{

    use DatabaseMigrations;


    public function testList()
    {
        factory(Genre::class,1)->create();
        $categories = Genre::all();
        $this->assertCount(1,$categories);
        $GenreKey = array_keys($categories->first()->getAttributes());
        $this->assertEqualsCanonicalizing(['id','name','created_at','updated_at','deleted_at','is_active'],$GenreKey);

    }

    public function testCreate(){
        $Genre = Genre::create(['name'=>'teste1']);
        $Genre->refresh();

        $this->assertEquals('teste1',$Genre->name);
        $this->assertNull($Genre->description);
        $this->assertTrue($Genre->is_active);

        //check if is hash

        $this->assertTrue(strlen($Genre->id) == 36 && strpos($Genre->id,'-'));

        $Genre = Genre::create(['name'=>'teste1','is_active'=>false]);
        $this->assertFalse($Genre->is_active);
    }


    public function testUpdate(){
        $Genre = factory(Genre::class)->create([
            'name'=>'test_name',
            'is_active' => false
        ])->first();

        $data = [
            'name'=>'test_name_updated',
            'is_active'=>true
        ];
        $Genre->update($data);

        foreach( $data as $key=>$value){
            $this->assertEquals($value,$Genre->{$key});
        }
    }

    public function testDelete(){
        $genre = factory(Genre::class)->create([
            'name'=>'test_name',
            'is_active' => false
        ])->first();

        $data = [
            'name'=>'test_name_updated',
            'is_active'=>true
        ];
        $genre->delete();

        $genreDeleted = Genre::find($genre->id);

        $this->assertNull($genreDeleted);


    }

}
