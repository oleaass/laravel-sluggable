# Add slugs to Eloquent models in Laravel

This package allows you to automatically create and/or update slugs when saving Eloquent models.

```php
$post = \App\Models\Post::create([
    'title' => 'My first post'
]);

echo $post->slug; // Output: my-first-post
```

Installation

```
$ composer require oleaass/laravel-sluggable
```

## Make your Eloquent model sluggable

```php
namespace App\Models;

use OleAass\Sluggable\Sluggable;

class Post extends Model
{
    use Sluggable;
    
    public function getSlugOptions() : array
    {
        return [
            'source' => 'title'
        ];
    }
}
```

## Options

These are the available options, with default values, which you can override via `getSlugOptions()` on your Eloquent model.

```php
[
    'source'            => 'title', // The field used as source for the slug
    'dest'              => 'slug',  // Name of the slug column
    'onCreate'          => true,    // If true, automatically create slug when creating a new resource
    'onUpdate'          => false,   // If true, automatically change slug when updating an existing resource
    'allowOverwrite'    => true,    // If true, overwrite existing slug when updating a resource
    'allowDuplicate'    => false,   // If true, non unique slugs can be created (recommended to be false always)
    'delimiter'         => '-',     // Replace spaces with this
];
```
