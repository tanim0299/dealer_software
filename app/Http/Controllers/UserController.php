<?php

namespace App\Http\Controllers;

use App\Services\ApiService;
use App\Services\RoleService;
use App\Services\UserService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    protected $PATH = 'backend.user';
    public function __construct()
    {
        $this->middleware(['permission:User view'])->only(['index']);
        $this->middleware(['permission:User create'])->only(['create']);
        $this->middleware(['permission:User edit'])->only(['edit']);
        $this->middleware(['permission:User destroy'])->only(['destroy']);
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $data['search']['free_text'] = $request->free_text ?? '';
        $data['users'] = (new UserService())->getUserList($data['search'],true,true)[2];
        return view($this->PATH.'.index',$data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data['roles'] = (new RoleService())->getRoleList([],false,false)[2];
        return view($this->PATH.'.create',$data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        [$status_code, $status_message, $error_message, $response] = (new UserService())->storeUser($request);
        if ($status_code == ApiService::API_SUCCESS) {
            return redirect()
                ->route('user.index')
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
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $data['user'] = (new UserService())->getUserById($id)[2];
        $data['roles'] = (new RoleService())->getRoleList([],false,false)[2];
        return view($this->PATH.'.edit',$data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        [$status_code, $status_message, $error_message, $response] = (new UserService())->updateUserById($request,$id);
        if ($status_code == ApiService::API_SUCCESS) {
            return redirect()
                ->route('user.index')
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
        [$status_code, $status_message] = (new UserService())->deleteUserById($id);
        return redirect()
            ->route('user.index')
            ->with('success', $status_message);
      
    }
}
