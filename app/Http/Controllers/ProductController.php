<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Services\ApiService;
use App\Services\BrandService;
use App\Services\CategoryService;
use App\Services\ItemService;
use App\Services\ProductService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    protected $PATH = 'backend.product';
    public function __construct()
    {
        $this->middleware(['permission:Product view'])->only(['index']);
        $this->middleware(['permission:Product create'])->only(['create']);
        $this->middleware(['permission:Product edit'])->only(['edit']);
        $this->middleware(['permission:Product destroy'])->only(['destroy']);
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $data['items'] = (new ItemService())->ItemList([],false)[2];
        $data['categories'] = (new CategoryService())->CategoryList([],false)[2];
        $data['brands'] = (new BrandService())->BrandList([],false)[2];
        $data['units'] = (new Unit())->get();
        $data['search']['item_id'] = $request->item_id ?? '';
        $data['search']['category_id'] = $request->category_id ?? '';
        $data['search']['brand_id'] = $request->brand_id ?? '';
        $data['search']['free_text'] = $request->free_text ?? '';
        $data['products'] = (new ProductService())->getProductList($data['search'], true, true)[2];
        return view($this->PATH.'.index',$data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data['items'] = (new ItemService())->ItemList([],false)[2];
        $data['brands'] = (new BrandService())->BrandList([],false)[2];
        $data['units'] = (new Unit())->get();
        return view($this->PATH.'.create',$data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        [$status_code, $status_message , $error_message] = (new ProductService())->storeProduct($request);
        if ($status_code == ApiService::API_SUCCESS) {
            return redirect()
                ->route('product.index')
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
        $data['items'] = (new ItemService())->ItemList([],false)[2];
        $data['brands'] = (new BrandService())->BrandList([],false)[2];
        $data['units'] = (new Unit())->get();
        $data['categories'] = (new CategoryService())->CategoryList([],false)[2];
        $data['product'] = (new ProductService())->getProductById($id)[2];
        return view($this->PATH.'.edit',$data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        [$status_code, $status_message , $error_message] = (new ProductService())->updateProductById($request,$id);
        if ($status_code == ApiService::API_SUCCESS) {
            return redirect()
                ->route('product.index')
                ->with('success', $status_message);
        }
        return redirect()->back()->withInput()->withErrors($error_message)->with('error', $status_message);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        [$status_code, $status_message] = (new ProductService())->deleteProductById($id);
        if ($status_code == ApiService::API_SUCCESS) {
            return redirect()
                ->back()
                ->with('success', $status_message);
        }

        return redirect()
            ->back()
            ->with('error', $status_message);
    }
}
