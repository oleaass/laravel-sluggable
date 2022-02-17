<?php

namespace OleAass\Sluggable;

use DomainException;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
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

        $currentValue = $this->{$this->slugOptions['source']};

        if (Str::slug($currentValue, $this->slugOptions['delimiter']) == $currentSlug) {
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
        if (!$this->slugExists($slug)) {
            return $slug;
        }

        $list = static::whereRaw("{$this->slugOptions['dest']} LIKE '{$slug}%'")->get();

        if (!$list->count()) {
            return $slug;
        }

        $i = 2;

        $filtered = $list->filter(function ($item) use ($slug) {
            if (preg_match("/^{$slug}(-[0-9]+)?$/", $item->{$this->slugOptions['dest']})) {
                return $item;
            }
        });

        if ($filtered->count() > 1) {
            $lastSlug = $filtered->last()->{$this->slugOptions['dest']};
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
        if (!$this->verifySlugOptions()) {
            throw new Exception('Failed to verify slug options');
        }
    }

    protected function verifySlugOptions(): bool
    {
        [
            'source' => $source,
            'dest' => $dest,
            'onCreate' => $onCreate,
            'onUpdate' => $onUpdate,
            'allowOverwrite' => $allowOverwrite,
            'allowDuplicate' => $allowDuplicate,
            'delimiter' => $delimiter
        ] = $this->slugOptions;

        if (!is_string($source)) {
            throw new DomainException('Source value must be of type string');
        }

        if (!is_string($dest)) {
            throw new DomainException('Dest value must be of type string');
        }

        $columns = Schema::getColumnListing((new static)->getTable());

        if (!in_array($source, $columns)) {
            return false;
        }

        if (!in_array($dest, $columns)) {
            return false;
        }

        return true;
    }
}
