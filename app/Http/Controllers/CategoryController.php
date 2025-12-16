<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Services\CategoryService;
use App\Services\ItemService;
use App\Services\ApiService;

class CategoryController extends Controller
{
    protected $PATH = 'backend.category.';
    public function __construct()
    {
        $this->middleware(['permission:Category view'])->only(['index']);
        $this->middleware(['permission:Category create'])->only(['create']);
        $this->middleware(['permission:Category edit'])->only(['edit']);
        $this->middleware(['permission:Category destroy'])->only(['destroy']);
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data['search']['free_text'] = $request->free_text ?? '';
        [$status_code, $status_message, $response] = (new CategoryService())->CategoryList($data['search'], true);
        $data['data'] = $response;
        return view($this->PATH.'.index',$data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        [$status_code, $status_message, $item] = (new ItemService())->ItemList([], false);
        $data['status_code'] = $status_code;
        $data['status_message'] = $status_message;
        $data['items'] = $item;
        return view($this->PATH.'.create',$data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        [$status_code, $status_message , $error_message] = (new CategoryService())->storeCategory($request);
        if ($status_code == ApiService::API_SUCCESS) {
            return redirect()
                ->route('category.index')
                ->with('success', $status_message);
        }
        return redirect()->back()->withInput()->withErrors($error_message)->with('error', $status_message);
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
        [$status_code, $status_message, $items] = (new ItemService())->ItemList([],false);
        $data['items'] = $items;
        [$status_code, $status_message, $category] = (new CategoryService())->getCategoryById($id);
        $data['category'] = $category;
        return view($this->PATH.'.edit',$data);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        [$status_code, $status_message, $error_message] =
            (new CategoryService())->updateCategory($request, $id);

        if ($status_code == ApiService::API_SUCCESS) {
            return redirect()
                ->route('category.index')
                ->with('success', $status_message);
        }

        return redirect()
            ->back()
            ->withInput()
            ->withErrors($error_message)
            ->with('error', $status_message);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        [$status_code, $status_message] = (new CategoryService())->deleteCategory($id);

        if ($status_code == ApiService::API_SUCCESS) {
            return redirect()
                ->back()
                ->with('success', $status_message);
        }

        return redirect()
            ->back()
            ->with('error', $status_message);
    }

    public function status(Request $request)
    {
        [$status_code, $status_message] = (new CategoryService())->updateCategoryStatus($request->id);

        return response()->json([
            'status_code' => $status_code,
            'status_message' => __($status_message),
            'message_type' => $status_code == ApiService::API_SUCCESS ? 'success' : 'error'
        ]);
    }

    public function itemWiseCategory(Request $request)
    {
        $item_id = $request->item_id ?? '';

        $categories = (new CategoryService())->CategoryList(['item_id' => $item_id], false)[2];
        $output = '';
        if(!empty($categories))
        {
            $output .= '<option value="">Chose One</option>';
            foreach($categories as $category)
            {
                $output .= '<option value="'.$category->id.'">'.$category->name.'</option>';
            }
        }
        else
        {
            $output .= '<option value="">No Category Found !</option>';
        }

        return $output;
    }
}
