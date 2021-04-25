<?php

namespace OleAass\Sluggable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

trait Sluggable
{
    protected $slugOptions = [
        'source' => 'title',
        'dest' => 'slug',
        'onCreate' => true,
        'onUpdate' => false,
        'allowOverwrite' => true,
        'allowDuplicate' => false,
        'delimiter' => '-'
    ];

    public static function bootSluggable() : void
    {
        static::creating(function (Model $model) {
            $model->setSlugOptions();
            $model->makeSlugOnCreate();
        });

        static::updating(function (Model $model) {
            $model->setSlugOptions();
            $model->makeSlugOnUpdate();
        });
    }

    public function makeSlugOnCreate() : void
    {
        if (!$this->slugOptions['onCreate']) {
            return;
        }

        $this->setSlug();
    }

    public function makeSlugOnUpdate() : void
    {
        if (!$this->slugOptions['onUpdate']) {
            return;
        }

        $currentSlug = $this->{$this->slugOptions['dest']};

        if (!$this->slugOptions['allowOverwrite'] && $currentSlug !== null) {
            return;
        }

        $this->setSlug();
    }

    public function setSlug() : void
    {
        $slug = Str::slug($this->{$this->slugOptions['source']}, $this->slugOptions['delimiter']);

        if (!$this->slugOptions['allowDuplicate']) {
            $slug = $this->ensureUniqueSlug($slug);
        }

        $this->{$this->slugOptions['dest']} = $slug;
    }

    public function ensureUniqueSlug(string $slug) : string
    {
        $list = static::whereRaw("{$this->slugOptions['dest']} REGEXP '^{$slug}(-[0-9]+)?$'")->get();

        if (!$list->count()) {
            return $slug;
        }

        $i = 2;

        if ($list->count() > 1) {
            $lastSlug = $list->last()->{$this->slugOptions['dest']};
            $slugParts = explode($this->slugOptions['delimiter'], $lastSlug);
            $i = (int) array_pop($slugParts);
            ++$i;
        }

        return $slug . $this->slugOptions['delimiter'] . (int) $i;
    }

    protected function slugExists(string $slug) : bool
    {
        return static::where($this->slugOptions['dest'], $slug)->exists();
    }

    protected function setSlugOptions() : void
    {
        $this->slugOptions = array_merge($this->slugOptions, $this->getSlugOptions());
    }
}
