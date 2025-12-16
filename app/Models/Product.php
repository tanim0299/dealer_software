<?php

namespace App\Models;

use App\Traits\FileUploader;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $guarded = [];

    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;

    const STATUS = [
        self::STATUS_ACTIVE => 'Active',
        self::STATUS_INACTIVE => 'Inactive',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class,'item_id');
    }
    public function category()
    {
        return $this->belongsTo(Category::class,'category_id');
    }
    public function brand()
    {
        return $this->belongsTo(Brand::class,'brand_id');
    }
    public function unit()
    {
        return $this->belongsTo(Unit::class,'unit_id');
    }

    public function getList($search = [], $is_paginate = true, $is_relation = true)
    {
        $query = self::query();
        if(!empty($search['item_id']))
        {
            $query = $query->where('item_id',$search['item_id']);
        }

        if(!empty($search['category_id']))
        {
            $query = $query->where('category_id',$search['category_id']);
        }

        if(!empty($search['brand_id']))
        {
            $query = $query->where('brand_id',$search['brand_id']);
        }

        if(!empty($search['free_text']))
        {
            $query = $query->where('name','like','%'.$search['free_text'].'%');
        }

        if(!empty($search['status']))
        {
            $query = $query->where('status',$search['status']);
        }

        if($is_paginate)
        {
            $query = $query->paginate(10);
        }
        else
        {
            $query = $query->get();
        }

        return $query;
    }

    public function createProduct($request)
    {
        $preparedData = [
            'item_id' => $request->item_id,
            'category_id' => $request->category_id,
            'brand_id' => $request->brand_id,
            'name' => $request->name,
            'purchase_price' => $request->purchase_price,
            'sale_price' => $request->sale_price,
            'unit_id' => $request->unit_id,
            'status' => $request->status ?? self::STATUS_ACTIVE,
        ];

        if($request->file('image'))
        {
            $preparedData['image'] = FileUploader::upload($request->file('image'), 'product');
        }
        self::create($preparedData);
        return;
    }
    public function updateProduct($request,$id)
    {
        $previousData = self::find($id);
        $preparedData = [
            'item_id' => $request->item_id,
            'category_id' => $request->category_id,
            'brand_id' => $request->brand_id,
            'name' => $request->name,
            'purchase_price' => $request->purchase_price,
            'sale_price' => $request->sale_price,
            'unit_id' => $request->unit_id,
            'status' => $request->status ?? self::STATUS_ACTIVE,
        ];

        if($request->file('image'))
        {
            $preparedData['image'] = FileUploader::upload($request->file('image'), 'product', $previousData->image);
        }
        self::where('id',$id)->update($preparedData);
        return;
    }
    public function deleteProductById($id)
    {
        $previousData = self::find($id);
        if(!empty($previousData->image))
        {   
            $preparedData['image'] = FileUploader::unlinkfile($previousData->image);
        }
        self::where('id',$id)->delete();
        return;
    }
}
