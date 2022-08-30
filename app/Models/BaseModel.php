<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Class BaseModel
 * @package App\Models
 */
class BaseModel extends Model
{
    public const DEFAULT_ORDER_SCOPE = 'defaultOrder';

    protected string $orderByColumn = 'created_at';

    protected string $orderByDirection = 'desc';

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        // uuid models are ordered by created_at
        static::addGlobalScope(
            self::DEFAULT_ORDER_SCOPE,
            function (Builder $builder) {
                $builder->scopes('defaultOrderBy');
            }
        );
    }

    /**
     * Scope a query to set a default order.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeDefaultOrderBy(Builder $query): Builder
    {
        return $query->orderBy($this->getTable() . '.' . $this->orderByColumn, $this->orderByDirection);
    }
}
