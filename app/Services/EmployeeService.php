<?php

namespace App\Services;


use App\Models\Employee;
use App\Parameters\Criteria;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\LazyCollection;

/**
 * @package App\Services
 */
class EmployeeService extends BaseService
{
    /**
     * EmployeeService constructor.
     * @param Employee $model
     */
    public function __construct(Employee $model)
    {
        parent::__construct($model);
    }

    /**
     * List all employee.
     * @return array
     */
    public function index(): array
    {
        return static::buildTreeFromArray($this->newQuery()->get());
    }

    /**
     * Search employee and show level
     * @param Criteria $criteria
     * @return array
     */
    public function search(Criteria $criteria): array
    {
        $employees = $this->newQuery()
            ->scopes($this->loadScopes($criteria->getFilters()))
            ->get();

        return static::buildTreeFromArray(
            $employees,
            $criteria->getFilter('level') ?? 0
        );
    }

    public function getAvailableEmployee(Criteria $criteria): LazyCollection
    {
        $criteria->setSelect(['id', 'name']);
        return $this->newQuery()
            ->scopes($this->loadScopes($criteria->getFilters()))
            ->select($criteria->getSelect())
            ->cursor();
    }

    public function import(array $data = [])
    {
        try {
            DB::beginTransaction();

            $employees = collect(array_merge(
                array_keys($data),
                array_values($data)
            ))->unique();

            // create employees
            foreach ($employees as $employee) {
                $this->newQuery()->firstOrCreate(['name' => $employee]);
            }

            // update parent
            $importedEmployees = $this->getAvailableEmployee(
                (new Criteria())->addFilter('name', $employees->all())
            );

            foreach ($data as $employeeName => $supervisorName) {
                $employee = $importedEmployees->firstWhere('name', $employeeName);
                $supervisor = $importedEmployees->firstWhere('name', $supervisorName);
                $employee->update(['parent_id' => optional($supervisor)->id ?? null]);
            }

            DB::commit();
        } catch (\Throwable $throwable) {
            DB::rollBack();
            throw $throwable;
        }
    }

    public static function buildTreeFromArray(Collection $items, int $length = 0, string $separate = '||'): array
    {
        $pathNames = $items->sortBy('level')->pluck('path_name')->values();
        $result = array();
        foreach ($pathNames as $item) {
            $itemParts = explode($separate, $item);
            if (!empty($length)) {
                $itemParts = array_slice($itemParts, -$length);
            }
            $last = &$result;

            foreach ($itemParts as $key => $value) {
                if ($key + 1 < count($itemParts)) {
                    $last = &$last[$value];
                } else {
                    $last[$value] = array();
                }
            }
        }

        return $result;
    }
}
