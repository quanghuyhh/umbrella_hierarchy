<?php

namespace App\Parameters;

use Illuminate\Http\Request;

class Criteria
{
    /**
     * @var array
     */
    private array $select = [];

    /**
     * @var array
     */
    private array $filters = [];

    /**
     * @var array
     */
    private array $sorts = [];

    /**
     * @var int
     */
    private int $page = 1;

    /**
     * @var int|null
     */
    private ?int $limit;

    /**
     * @param Request $request
     *
     * @return Criteria
     */
    public static function createFromRequest(Request $request): Criteria
    {
        $filters = $request->get('filter', []);
        return (new static())->setFilters($filters)
            ->setSorts($request->get('sort', []))
            ->setSelect($request->get('select', []))
            ->setPagination(
                $request->get(config('pagination.page_name')) ?
                    (int)$request->get(config('pagination.page_name')) : 1,
                $request->get(config('pagination.per_page_name')) ?
                    (int)$request->get(config('pagination.per_page_name')) : null
            );
    }

    /**
     * @param $page
     * @param $limit
     *
     * @return Criteria
     */
    public function setPagination(int $page, ?int $limit): Criteria
    {
        $this->page = $page;
        $this->limit = $limit;

        return $this;
    }

    /**
     * @return array
     */
    public function getFilters(): array
    {
        return $this->filters;
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function getFilter(string $key): mixed
    {
        return $this->filters[$key] ?? null;
    }

    /**
     * @param array $filters
     *
     * @return Criteria
     */
    public function setFilters(array $filters): Criteria
    {
        $this->filters = $filters;

        return $this;
    }

    /**
     * @param string|array $field
     * @param int|string|null $value
     *
     * @return Criteria
     */
    public function addFilter($field, $value = null): Criteria
    {
        if (is_array($field)) {
            foreach ($field as $filterField => $filterValue) {
                $this->filters[$filterField] = $filterValue;
            }

            return $this;
        }

        $this->filters[$field] = $value;

        return $this;
    }

    /**
     * @return array
     */
    public function getSorts(): array
    {
        return $this->sorts;
    }

    /**
     * @param array $sorts
     *
     * @return Criteria
     */
    public function setSorts(array $sorts): Criteria
    {
        $this->sorts = $sorts;

        return $this;
    }

    /**
     * @return int
     */
    public function getPage(): int
    {
        return $this->page;
    }

    /**
     * @return int
     */
    public function getLimit(): int
    {
        return $this->limit ?? config('pagination.per_page_number');
    }

    /**
     * @return array
     */
    public function getSelect(): array
    {
        return $this->select;
    }

    /**
     * @param array $select
     * @return $this
     */
    public function setSelect(array $select): Criteria
    {
        $this->select = $select;
        return $this;
    }
}
