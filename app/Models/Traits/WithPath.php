<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Model;

trait WithPath
{
    protected static function getSeparateChar(): string
    {
        return '||';
    }

    protected static function setPath(Model $model): Model
    {
        // Automatically generate a path if had parent_id.
        $path = $model->id;
        if (!empty($model->parent_id)) {
            $parentModel = static::query()->findOrFail($model->parent_id);
            $path = $parentModel->path . static::getSeparateChar() . $model->id;
        }
        $model->setAttribute('path', $path);
        $model->setAttribute('level', count(explode(static::getSeparateChar(), $path)));
        return $model;
    }

    protected static function setPathName(Model $model): Model
    {
        // Automatically generate a path if had parent_id.
        $pathName = $model->name;
        if (!empty($model->parent_id)) {
            $parentModel = static::query()->findOrFail($model->parent_id);
            $pathName = $parentModel->path_name . static::getSeparateChar() . $model->name;
        }
        return $model->setAttribute('path_name', $pathName);
    }

    protected static function updatePathWithChild(Model $model): void
    {
        $model = static::setPath($model);
        $model->saveQuietly();

        // Update children with parent path and parent pathname
        $children = static::query()->where('id', '<>', $model->id)
            ->where('path', 'like', '%' . $model->id . '%')
            ->orderBy('path')
            ->get();

        $children->each(function (Model $childModel) use ($model, $children) {
            $parent = $children->where('id', $childModel->parent_id)->first() ?? $model;
            $childModel->path = $parent->path . static::getSeparateChar() . $childModel->id;
            $childModel->path_name = $parent->path_name . static::getSeparateChar() . $childModel->name;
            $childModel->level = count(explode(static::getSeparateChar(), $childModel->path));
            $childModel->saveQuietly();
        });
    }
}
