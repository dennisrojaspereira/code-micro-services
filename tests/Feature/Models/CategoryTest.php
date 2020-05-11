<?php

namespace Tests\Feature\Models;

use Tests\TestCase;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class CategoryTest extends TestCase
{

    use DatabaseMigrations;


    public function testList()
    {
        factory(Category::class,1)->create();
        $categories = Category::all();
        $this->assertCount(1,$categories);
        $categoryKey = array_keys($categories->first()->getAttributes());
        $this->assertEqualsCanonicalizing(['id','name','description','created_at','updated_at','deleted_at','is_active'],$categoryKey);

    }

    public function testCreate(){
        $category = Category::create(['name'=>'teste1']);
        $category->refresh();

        $this->assertEquals('teste1',$category->name);
        $this->assertNull($category->description);
        $this->assertTrue($category->is_active);

        $category = Category::create(['name'=>'teste1','description'=> null]);
        $this->assertNull($category->description);

        $category = Category::create(['name'=>'teste1','description'=> 'testedescription','is_active'=>false]);
        $this->assertEquals('testedescription',$category->description);
        $this->assertFalse($category->is_active);
    }


    public function testUpdate(){
        $category = factory(Category::class)->create([
            'description'=>'test_description',
            'is_active' => false
        ])->first();

        $data = [
            'name'=>'test_name_updated',
            'description'=>'test_description_updated',
            'is_active'=>true
        ];
        $category->update($data);

        foreach( $data as $key=>$value){
            $this->assertEquals($value,$category->{$key});
        }


    }

}
