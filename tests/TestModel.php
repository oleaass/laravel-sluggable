<?php

namespace OleAass\Sluggable\Tests;

use Illuminate\Database\Eloquent\Model;
use OleAass\Sluggable\Tests\SluggableTestTrait;

class TestModel extends Model
{
    use SluggableTestTrait;

    protected $fillable = ['title', 'slug'];
    protected $defaultSlugOptions = [
        'source' => 'title',
        'dest' => 'slug'
    ];

    protected $testSlugOptions = [];

    public function overrideSlugOptions(array $options)
    {
        $this->testSlugOptions = array_merge($this->defaultSlugOptions, $options);
        $this->testSetSlugOptions();
    }

    protected function getSlugOptions(): array
    {
        return $this->testSlugOptions;
    }
}