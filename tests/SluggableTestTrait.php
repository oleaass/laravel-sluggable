<?php

namespace OleAass\Sluggable\Tests;

use OleAass\Sluggable\Sluggable;

trait SluggableTestTrait
{
    use Sluggable;

    public function testSetSlugOptions()
    {
        $this->slugOptions = array_merge($this->slugOptions, $this->getSlugOptions());
        return $this->verifySlugOptions();
    }

    public function testVerifySlugOptions(): bool
    {
        return $this->verifySlugOptions();
    }
}