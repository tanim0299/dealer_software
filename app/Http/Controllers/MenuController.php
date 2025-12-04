<?php

namespace App\Http\Controllers;

use App\Models\Actions;
use App\Models\Menu;
use App\Models\MenuSection;
use App\Services\ApiService;
use App\Services\MenuSectionService;
use App\Services\MenuService;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    protected $PATH = 'backend.menu.';
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $data['search']['free_text'] = $request->input('free_text', '');       
        $data['search']['status'] = $request->input('status', '');
        $data['menus'] = (new MenuService())->getMenuList($data['search'], true, true)[2];
        return view($this->PATH.'index',$data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data['search']['type'] = Menu::TYPE_PARENT;
        $data['search']['status'] = Menu::STATUS_ACTIVE;
        $data['parents'] = last((new MenuService())->getMenuList($data['search'], false, false));
        $data['permissions'] = (new Actions())->get();
        $data['sections'] = (new MenuSectionService())->MenuSectionList(['status' => MenuSection::STATUS_INACTIVE], false)[2];
        return view($this->PATH.'create',$data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        [$status_code, $status_message, $error_message] = (new MenuService())->storeMenu($request);
        if ($status_code == ApiService::API_SUCCESS) {
            return redirect()
                ->route('menu.index')
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
        $data['menu'] = (new MenuService())->getMenuById($id)[2];
        $data['search']['type'] = Menu::TYPE_PARENT;
        $data['search']['status'] = Menu::STATUS_ACTIVE;
        $data['parents'] = last((new MenuService())->getMenuList($data['search'], false, false));
        $data['permissions'] = (new Actions())->get();
        $data['sections'] = (new MenuSectionService())->MenuSectionList(['status' => MenuSection::STATUS_INACTIVE], false)[2];
        return view($this->PATH.'.edit',$data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        [$status_code, $status_message, $error_message] = (new MenuService())->updateMenuById($request, $id);
        if ($status_code == ApiService::API_SUCCESS) {
            return redirect()
                ->route('menu.index')
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
        [$status_code, $status_message] = (new MenuService())->deleteMenuById($id);
        if ($status_code == ApiService::API_SUCCESS) {
            return redirect()
                ->route('menu.index')
                ->with('success', $status_message);
        }
        return redirect()
            ->back()
            ->withInput()
            ->with('error', $status_message);
    }
}
