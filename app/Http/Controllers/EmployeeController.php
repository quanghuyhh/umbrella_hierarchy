<?php

namespace App\Http\Controllers;

use App\Parameters\Criteria;
use App\Services\EmployeeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Class EmployeeController
 * @package App\Http\Controllers
 * @property EmployeeService $employeeService
 */
class EmployeeController extends Controller
{
    public EmployeeService $employeeService;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(EmployeeService $employeeService)
    {
        $this->employeeService = $employeeService;
    }

    public function index(Request $request)
    {
        return response()->json(
            $this->employeeService->index()
        );
    }

    public function search(Request $request)
    {
        return response()->json(
            $this->employeeService->search(Criteria::createFromRequest($request))
        );
    }

    public function import(Request $request)
    {
        return response()->json(
            $this->employeeService->import($request->all())
        );
    }


}
