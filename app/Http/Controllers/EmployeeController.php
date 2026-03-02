<?php

namespace App\Http\Controllers;

use App\Services\ApiService;
use App\Services\EmployeeService;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    protected $path = 'backend.employee';

    public function __construct()
    {
        $this->middleware(['permission:Employee view'])->only(['index']);
        $this->middleware(['permission:Employee create'])->only(['create']);
        $this->middleware(['permission:Employee edit'])->only(['edit']);
        $this->middleware(['permission:Employee destroy'])->only(['destroy']);
    }

    public function index(Request $request)
    {
        $data['search']['free_text'] = $request->free_text ?? '';
        $data['employees'] = (new EmployeeService())->getEmployeeList($data['search'], true)[2];

        return view($this->path . '.index', $data);
    }

    public function create()
    {
        return view($this->path . '.create');
    }

    public function store(Request $request)
    {
        [$status_code, $status_message, $error_message] = (new EmployeeService())->storeEmployee($request);

        if ($status_code == ApiService::API_SUCCESS) {
            return redirect()
                ->route('employee.index')
                ->with('success', $status_message);
        }

        return redirect()
            ->back()
            ->withInput()
            ->withErrors($error_message)
            ->with('error', $status_message);
    }

    public function show(string $id)
    {
        //
    }

    public function edit(string $id)
    {
        $data['data'] = (new EmployeeService())->getEmployeeById($id)[2];

        return view($this->path . '.edit', $data);
    }

    public function update(Request $request, string $id)
    {
        [$status_code, $status_message, $error_message] = (new EmployeeService())->updateEmployeeById($request, $id);

        if ($status_code == ApiService::API_SUCCESS) {
            return redirect()
                ->route('employee.index')
                ->with('success', $status_message);
        }

        return redirect()
            ->back()
            ->withInput()
            ->withErrors($error_message)
            ->with('error', $status_message);
    }

    public function destroy(string $id)
    {
        [$status_code, $status_message] = (new EmployeeService())->deleteEmployeeById($id);

        if ($status_code == ApiService::API_SUCCESS) {
            return redirect()
                ->route('employee.index')
                ->with('success', $status_message);
        }

        return redirect()
            ->back()
            ->with('error', $status_message);
    }
}
