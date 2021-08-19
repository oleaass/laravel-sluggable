<?php

namespace OleAass\Sluggable\Tests;

use Closure;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function setUpDatabaseRequirements(Closure $callback): void
    {
        Schema::create('test_models', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->string('slug');
            $table->timestamps();
        });

        TestModel::create(['title' => 'Foo bar']);
        TestModel::create(['title' => 'Bar baz']);
        TestModel::create([
            'title' => 'Foo bar baz',
            'slug' => 'foo-bar-baz'
        ]);
    }
}