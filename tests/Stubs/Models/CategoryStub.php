<?php

namespace Tests\Stubs\Models;

use App\Models\Traits\Uuid;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Eloquent\SoftDeletes;


class CategoryStub extends Model
{
    protected $table = 'category_stubs';
    protected $fillable = ['name','description'];

    public static function createTable(){
        Schema::dropIfExists('category_stubs');
        Schema::create('category_stubs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public static function dropTable(){
        Schema::dropIfExists('category_stubs');

    }

}
