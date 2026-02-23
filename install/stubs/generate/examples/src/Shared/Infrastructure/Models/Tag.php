<?php

declare(strict_types=1);

namespace Src\Shared\Infrastructure\Models;

use Database\Factories\TagFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tag extends Model
{
    /** @use HasFactory<TagFactory> */
    use HasFactory;

    static string $factory = TagFactory::class;

    protected $guarded = [];

    public $timestamps = false;

    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class);
    }

    public function tagType(): BelongsTo
    {
        return $this->belongsTo(TagType::class);
    }
}
