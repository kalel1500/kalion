<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Tests\Support\Contexts\Blog\Infrastructure\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Thehouseofel\Kalion\Tests\Support\Database\Factories\TagTypeFactory;

class TagType extends Model
{
    /** @use HasFactory<TagTypeFactory> */
    use HasFactory;

    static string $factory = TagTypeFactory::class;

    protected $guarded = [];

    public $timestamps = false;

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }
}
