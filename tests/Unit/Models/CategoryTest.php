<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\Category;
use App\Models\Traits\Uuid;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;

#classe especifica               vendor/bin/phpunit tests/Unit/CategoryTest.php
#Metodo especifico em um arquivo vendor/bin/phpunit --filter testIfUseTraits tests/Unit/CategoryTest.php
#Metodo especifico em uma classe vendor/bin/phpunit --filter CategoryTest::testIfUseTraits
class CategoryTest extends TestCase
{
    private $category;

    protected function setUp():void{
        parent::setUp();
        $this->category = new Category();
    }

    public function testFillable()
    {
        $fillable = ['name','description','is_active'];
        $this->assertEquals($fillable,$this->category->getFillable());
    }

    public function testIfUseTraits(){
        $traits  = [ SoftDeletes::class,Uuid::class];
        $categoryTraits = array_keys(class_uses(Category::class));
        $this->assertEquals($traits,$categoryTraits);

    }

    public function testCasts()
    {
        $casts = ['id'=>'string','is_active'=>'boolean'];
        $this->assertEquals($casts,$this->category->getCasts());
    }

    public function testDates()
    {
        $dates = ['created_at','deleted_at','updated_at'];
        foreach ($dates as $date) {
            $this->assertContains($date,$this->category->getDates());
        }
        $this->assertCount(count($dates),$this->category->getDates());
    }

    public function testIncrementing()
    {
        $this->assertFalse($this->category->incrementing);
    }
}
