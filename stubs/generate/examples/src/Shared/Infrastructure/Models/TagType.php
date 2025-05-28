<?php

declare(strict_types=1);

namespace Src\Shared\Infrastructure\Models;

use Database\Factories\TagTypeFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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
