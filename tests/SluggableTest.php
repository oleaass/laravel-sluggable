<?php

namespace OleAass\Sluggable\Tests;

use DomainException;
use Illuminate\Support\Facades\Schema;
use OleAass\Sluggable\Sluggable;

class SluggableTest extends TestCase
{
    /** @test */
    public function slug_options_validation_throws_DomainException_if_source_is_not_of_type_string(): void
    {
        $this->expectException(DomainException::class);

        $model = new TestModel;
        $model->overrideSlugOptions([
            'source' => null
        ]);
    }

    /** @test */
    public function slug_options_validation_throws_DomainException_if_dest_is_not_of_type_string(): void
    {
        $this->expectException(DomainException::class);

        $model = new TestModel;
        $model->overrideSlugOptions([
            'dest' => null
        ]);
    }

    /** @test */
    public function slug_options_validation_fails_if_source_column_does_not_exist_on_model(): void
    {
        $model = new TestModel;
        $model->overrideSlugOptions([
            'source' => 'name'
        ]);
        $this->assertFalse($model->testVerifySlugOptions());
    }

    /** @test */
    public function slug_options_validation_fails_if_dest_column_does_not_exist_on_model(): void
    {
        $model = new TestModel;
        $model->overrideSlugOptions([
            'source' => 'sluggish'
        ]);
        $this->assertFalse(
            $model->testVerifySlugOptions()
        );
    }

    /** @test */
    public function ensure_unique_slug(): void
    {
        $model = TestModel::first();
        $uniqueSlug = $model->ensureUniqueSlug($model->slug);

        $this->assertNotEquals(
            $model->slug, $uniqueSlug,
            "Slug did not change as expected"
        );
    }
}