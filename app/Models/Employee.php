<?php

namespace App\Models;

use App\Models\Traits\WithPath;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;

class Employee extends BaseModel
{
    use WithPath;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'name_path',
        'id_path',
        'parent_id',
        'level'
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::created(
            function (self $model): void {
                $model = static::setPathName($model);
                $model = static::setPath($model);
                $model->saveQuietly();
            }
        );
        static::updating(
            function (self $category): void {
                if (Arr::hasAny($category->getDirty(), ['name', 'parent_id'])) {
                    static::setPathName($category);
                }
            }
        );
        static::updated(
            function (self $category): void {
                static::updatePathWithChild($category);
            }
        );
        parent::booted();
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'parent_id');
    }

    public function child(): HasMany
    {
        return $this->hasMany(Employee::class, 'parent_id');
    }

    public function scopeName(Builder $builder, ...$name): Builder
    {
        return $builder->whereIn('name', $name);
    }

    public function scopeSearch(Builder $builder, $name): Builder
    {
        return $builder->where('path_name', 'LIKE', "%$name%");
    }
}
