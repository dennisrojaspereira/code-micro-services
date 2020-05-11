<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\Genre;
use App\Models\Traits\Uuid;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;

#classe especifica               vendor/bin/phpunit tests/Unit/GenreTest.php
#Metodo especifico em um arquivo vendor/bin/phpunit --filter testIfUseTraits tests/Unit/GenreTest.php
#Metodo especifico em uma classe vendor/bin/phpunit --filter GenreTest::testIfUseTraits
class GenreTest extends TestCase
{
    private $Genre;

    protected function setUp():void{
        parent::setUp();
        $this->Genre = new Genre();
    }

    public function testFillable()
    {
        $fillable = ['name','description','is_active'];
        $this->assertEquals($fillable,$this->Genre->getFillable());
    }

    public function testIfUseTraits(){
        $traits  = [ SoftDeletes::class,Uuid::class];
        $GenreTraits = array_keys(class_uses(Genre::class));
        $this->assertEquals($traits,$GenreTraits);

    }

    public function testCasts()
    {
        $casts = ['id'=>'string','is_active'=>'boolean'];
        $this->assertEquals($casts,$this->Genre->getCasts());
    }

    public function testDates()
    {
        $dates = ['created_at','deleted_at','updated_at'];
        foreach ($dates as $date) {
            $this->assertContains($date,$this->Genre->getDates());
        }
        $this->assertCount(count($dates),$this->Genre->getDates());
    }

    public function testIncrementing()
    {
        $this->assertFalse($this->Genre->incrementing);
    }
}
