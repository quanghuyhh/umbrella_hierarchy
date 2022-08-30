<?php

namespace App\Parameters;

use Illuminate\Http\Request;

class Relation
{
    private $eagerLoad;

    public function __construct(array $relations)
    {
        $this->eagerLoad = $relations;
    }

    public static function createFromRequest(Request $request): Relation
    {
        return (new static($request->get('load', [])));
    }

    /**
     * @return array
     */
    public function list(): array
    {
        return $this->eagerLoad;
    }

    /**
     * @param array|string $relations
     */
    public function add($relations): void
    {
        if (is_string($relations)) {
            $relations = [$relations];
        }
        $this->eagerLoad = array_merge($this->eagerLoad, $relations);
    }
}
