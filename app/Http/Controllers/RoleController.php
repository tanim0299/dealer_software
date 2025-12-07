<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Services\ApiService;
use App\Services\RoleService;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    protected $PATH = 'backend.role.';
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $data['search']['free_text'] = $request->free_text ?? '';
        [$status_code, $status_message, $response] = (new RoleService())->getRoleList($data['search'], true);
        $data['roles'] = $response;
        return view($this->PATH.'.index',$data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view($this->PATH.'create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        [$status_code, $status_message, $error_message] = (new RoleService())->storeRole($request);
        if ($status_code == ApiService::API_SUCCESS) {
            return redirect()
                ->route('role.index')
                ->with('success', $status_message);
        }
        return redirect()
            ->back()
            ->withInput()
            ->withErrors($error_message) // must be an array of field → messages
            ->with('error', $status_message);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $data['role'] = (new RoleService())->getRoleById($id)[2];
        $data['checked_permission'] = (new RoleService())->getRolesPermisison($id);
        $data['permissions'] = (new RoleService())->getPermissionList();
        return view($this->PATH.'.show',$data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {

        [$status_code, $status_message, $data] = (new RoleService())->getRoleById($id);
        $data['role'] = $data;
        return view($this->PATH.'.edit',$data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        [$status_code, $status_message, $error_message] = (new RoleService())->updateRoleById($request, $id);
        if ($status_code == ApiService::API_SUCCESS) {
            return redirect()
                ->route('role.index')
                ->with('success', $status_message);
        }
        return redirect()
            ->back()
            ->withInput()
            ->withErrors($error_message) // must be an array of field → messages
            ->with('error', $status_message);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        [$status_code, $status_message] = (new RoleService())->deleteRoleById($id);
        
        if ($status_code == ApiService::API_SUCCESS) {
            return redirect()
                ->route('role.index')
                ->with('success', $status_message);
        }
        return redirect()
            ->back()
            ->withInput()
            ->with('error', $status_message);
    }

    public function permission(Request $request, $id)
    {
        [$status_code, $status_message] = (new RoleService())->assignPermission($request,$id);
        if ($status_code == ApiService::API_SUCCESS) {
            return redirect()
                ->route('role.index')
                ->with('success', $status_message);
        }
        return redirect()
            ->back()
            ->withInput()
            ->with('error', $status_message);
    }
}
