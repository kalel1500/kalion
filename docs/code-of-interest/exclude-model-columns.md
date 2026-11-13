
# Launch optimize in console
```php
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

trait ExcludableColumns
{
    public function scopeExclude(Builder $query, array $columnas): Builder
    {
        $tabla = $this->getTable();
        $cacheKey = "table_columns_{$tabla}";

        $todas = Cache::rememberForever($cacheKey, function () use ($tabla) {
            return Schema::getColumnListing($tabla);
        });

        return $query->select(array_diff($todas, $columnas));
    }
}

```
