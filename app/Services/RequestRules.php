<?php
namespace App\Services;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RequestRules{

    public static function driverIssueStoreRules()
    {
        $rules = [
            'driver_id'              => 'required|exists:drivers,id',
            'items'                  => 'required|array',
            'items.*.product_id'     => 'required|exists:products,id',
            'items.*.issue_qty'      => 'required|numeric|min:1',
        ];

        $messages = [
            'driver_id.required'         => 'Driver is required.',
            'driver_id.exists'           => 'Selected driver does not exist.',
            'items.required'             => 'At least one item is required.',
            'items.array'                => 'Items must be an array.',
            'items.*.product_id.required'=> 'Product is required for each item.',
            'items.*.product_id.exists'  => 'Selected product does not exist.',
            'items.*.issue_qty.required' => 'Issue quantity is required for each item.',
            'items.*.issue_qty.numeric'  => 'Issue quantity must be a number.',
            'items.*.issue_qty.min'      => 'Issue quantity must be at least 1.',
        ];

        return [$rules, $messages];
    }

    public static function supplierRules($request, $id = null)
    {
        $isUpdate = !empty($id);
        $rules = [
            'name' => 'required',
            'phone' => $isUpdate
                    ? 'required|unique:suppliers,phone,'.$id
                    : 'required|unique:suppliers,phone',
        ];

        $messages = [
            'name.required'  => 'Supplier name is required.',

            'phone.required' => 'Phone number is required.',
            'phone.unique'   => 'This phone number is already associated with another supplier.',
        ];

        return [$rules, $messages];

    }
    public static function driverStoreRules()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'vehicle_no' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ];

        $messages = [
            'name.required'      => 'Driver name is required.',

            'phone.required'     => 'Phone number is required.',
            'phone.unique'       => 'This phone number is already associated with another driver.',

            'vehicle_no.required'=> 'Vehicle number is required.',
            'vehicle_no.unique'  => 'This vehicle number is already assigned to another driver.',

            'status.required'    => 'Driver status is required.',
            'status.in'          => 'Invalid driver status selected.',
        ];

        return [$rules, $messages];

    }


    public static function productRules($request, $id = null)
    {
        $isUpdate = !empty($id);
        $rules = [
            'item_id'        => 'required',
            'category_id'    => 'required',
            'brand_id'       => 'required',
            'name'           => 'required',
            'unit_id'        => 'required',
            'purchase_price' => 'required',
            'sale_price'     => 'required',
            'status'         => 'required',
        ];

        $messages = [
            'item_id.required'        => 'Please select an item.',
            'category_id.required'    => 'Please select a category.',
            'brand_id.required'       => 'Please select a brand.',
            'name.required'           => 'Product name is required.',
            'unit_id.required'        => 'Please select a unit.',
            'purchase_price.required' => 'Purchase price is required.',
            'sale_price.required'     => 'Sale price is required.',
            'status.required'         => 'Please select product status.',
        ];

        return [$rules, $messages];

    }

    public static function userRules($request, $id = null)
    {
        $isUpdate = !empty($id);

        $rules = [
            'role_id' => 'required',
            'name' => 'required',
            'email' => $isUpdate
                ? 'required|string|unique:users,email,' . $id
                : 'required|string|unique:users,email',
            'phone' => $isUpdate
                    ? 'required|unique:users,phone,'.$id
                    : 'required|unique:users,phone',
        ];

        if(empty($id))
        {
            $rules['password'] = 'required';
        }

        $messages = [
            'role_id.required' => 'Please Select A Role',
            'name.required' => 'Please Give User Name',
            'email.required' => 'Please Give Email',
            'email.unique' => 'This Email Is Already Used',
            'phone.unique' => 'This Phone Is Already Used',
            'phone.required' => 'Please Give A Phone Number',
            'password.required' => 'Please Give Password',
        ];

        return [$rules, $messages];
    }

    public static function menuSectionRules($request, $id = null)
    {
        $isUpdate = !empty($id);

        $rules = [
            'sl' => $isUpdate
                ? 'required|integer|unique:menu_sections,sl,' . $id
                : 'required|integer|unique:menu_sections,sl',

            'name' => $isUpdate
                ? 'required|string|max:255|unique:menu_sections,name,' . $id
                : 'required|string|max:255|unique:menu_sections,name',

            'status' => 'required|in:0,1',
        ];

        $messages = [
            'sl.required' => 'SL is required.',
            'sl.integer' => 'SL must be an integer.',
            'sl.unique' => 'SL must be unique.',
            'name.required' => 'Section name is required.',
            'name.string' => 'Section name must be a string.',
            'name.max' => 'Section name may not be greater than 255 characters.',
            'name.unique' => 'Section name must be unique.',
            'status.required' => 'Status is required.',
            'status.in' => 'Status must be either 0 (inactive) or 1 (active).',
        ];

        return [$rules, $messages];
    }

    public static function menuRules($request, $id = null)
    {
        $isUpdate = !empty($id);
        $rules = [
            'sl' => $isUpdate
                ? 'required|integer|unique:menus,sl,' . $id
                : 'required|integer|unique:menus,sl',

            'name' => 'required|string|max:255',
            'section_id' => 'required',
            'status' => 'required|in:0,1',
            'type' => 'required|in:1,2,3',
        ];

        if($request->type == \App\Models\Menu::TYPE_PARENT)
        {
            $rules['icon'] = 'required|string|max:255';
        }
        elseif($request->type == \App\Models\Menu::TYPE_CHILD)
        {
            $rules['parent_id'] = 'required';
            $rules['system_name'] = 'required|string|max:255';
            $rules['route'] = 'required|string|max:255';
            $rules['slug'] = 'required';
        }
        else // TYPE_SINGLE
        {
            $rules['system_name'] = 'required|string|max:255';
            $rules['route'] = 'required|string|max:255';
            $rules['slug'] = 'required';
            $rules['icon'] = 'nullable|string|max:255';
        }

        $messages = [
            'sl.required' => 'SL is required.',
            'sl.integer' => 'SL must be an integer.',
            'sl.unique' => 'SL must be unique.',
            'name.required' => 'Menu name is required.',
            'name.string' => 'Menu name must be a string.',
            'name.max' => 'Menu name may not be greater than 255 characters.',
            'section_id.required' => 'Section is required.',
            'status.required' => 'Status is required.',
            'status.in' => 'Status must be either 0 (inactive) or 1 (active).',
            'type.required' => 'Type is required.',
            'type.in' => 'Type must be one of the following values: 1 (Parent Menu), 2 (Child Menu), 3 (Single Menu).',
            'parent_id.required' => 'Parent menu is required for Parent and Child menu types.',
            'system_name.required' => 'System name is required for Child and Single menu types.',
            'system_name.string' => 'System name must be a string.',
            'system_name.max' => 'System name may not be greater than 255 characters.',
            'route.required' => 'Route is required for Child and Single menu types.',
            'route.string' => 'Route must be a string.',
            'route.max' => 'Route may not be greater than 255 characters.',
            'slug.required' => 'Slug is required for Child and Single menu types.',
            'icon.string' => 'Icon must be a string.',
            'icon.max' => 'Icon may not be greater than 255 characters.',
            'icon.required' => 'Icon is required for Parent menu type.',
        ];

        return [$rules, $messages];


    }

    public static function roleRules($request, $id = null)
    {
        $isUpdate = !empty($id);

        $rules = [
            'name' => $isUpdate
                ? 'required|string|max:255|unique:roles,name,' . $id
                : 'required|string|max:255|unique:roles,name',

            'index' => $isUpdate
                ? 'required|integer|unique:roles,index,' . $id
                : 'required|integer|unique:roles,index',
        ];

        $messages = [
            'name.required' => 'Role name is required.',
            'name.string'   => 'Role name must be a string.',
            'name.max'      => 'Role name may not be greater than 255 characters.',
            'name.unique'   => 'Role name must be unique.',

            'index.required' => 'Role index is required.',
            'index.integer'  => 'Role index must be a number.',
            'index.unique'   => 'Role index must be unique.',
        ];

        return [$rules, $messages];
    }

    public static function globalsettingsRules($request, $id = null)
    {
        $rules = [
            'title' => 'required|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'favicon' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,ico|max:1024',
        ];

        $messages = [
            'title.required' => 'Website name is required.',
            'title.string' => 'Website name must be a string.',
            'title.max' => 'Website name may not be greater than 255 characters.',
            'logo.image' => 'Website logo must be an image.',
            'logo.mimes' => 'Website logo must be a file of type: jpeg, png, jpg, gif, svg.',
            'logo.max' => 'Website logo may not be greater than 2048 kilobytes.',
            'favicon.image' => 'Website favicon must be an image.',
            'favicon.mimes' => 'Website favicon must be a file of type: jpeg, png, jpg, gif, svg, ico.',
            'favicon.max' => 'Website favicon may not be greater than 1024 kilobytes.',
        ];

        return [$rules, $messages];
    }

    public static function ItemRules($request, $id = null)
    {
        $isUpdate = !empty($id);

        $rules = [
            'name' => $isUpdate
                ? 'required|string|max:255|unique:items,name,' . $id
                : 'required|string|max:255|unique:items,name',

            'status' => 'required|in:0,1',
        ];

        $messages = [
            'name.required' => 'Item name is required.',
            'name.string'   => 'Item name must be a string.',
            'name.max'      => 'Item name may not be greater than 255 characters.',
            'name.unique'   => 'Item name must be unique.',

            'status.required' => 'Status is required.',
            'status.in'       => 'Status must be either 0 (inactive) or 1 (active).',
        ];

        return [$rules, $messages];
    }

    public static function brandRules($request, $id = null)
    {
        $isUpdate = !empty($id);

        $rules = [
            'name' => $isUpdate
                ? 'required|string|max:255|unique:brands,name,' . $id
                : 'required|string|max:255|unique:brands,name',

            'status' => 'required|in:0,1',
        ];

        $messages = [
            'name.required' => 'Brand name is required.',
            'name.string'   => 'Brand name must be a string.',
            'name.max'      => 'Brand name may not be greater than 255 characters.',
            'name.unique'   => 'Brand name must be unique.',

            'status.required' => 'Status is required.',
            'status.in'       => 'Status must be either 0 (inactive) or 1 (active).',
        ];

        return [$rules, $messages];
    }

    public static function categoryRules($request, $id = null)
    {
        $isUpdate = !empty($id);

        $rules = [
            'name' => $isUpdate
                ? 'required|string|max:255|unique:categories,name,' . $id
                : 'required|string|max:255|unique:categories,name',

            'status' => 'required|in:0,1',
        ];

        $messages = [
            'name.required' => 'Category name is required.',
            'name.string'   => 'Category name must be a string.',
            'name.max'      => 'Category name may not be greater than 255 characters.',
            'name.unique'   => 'Category name must be unique.',

            'status.required' => 'Status is required.',
            'status.in'       => 'Status must be either 0 (inactive) or 1 (active).',
        ];

        return [$rules, $messages];
    }

    public static function unitRules($request, $id = null)
    {
        $isUpdate = !empty($id);

        $rules = [
            'name' => $isUpdate
                ? 'required|string|max:255|unique:units,name,' . $id
                : 'required|string|max:255|unique:units,name',

            'status' => 'required|in:0,1',
        ];

        $messages = [
            'name.required' => 'Unit name is required.',
            'name.string'   => 'Unit name must be a string.',
            'name.max'      => 'Unit name may not be greater than 255 characters.',
            'name.unique'   => 'Unit name must be unique.',

            'status.required' => 'Status is required.',
            'status.in'       => 'Status must be either 0 (inactive) or 1 (active).',
        ];

        return [$rules, $messages];
    }

    public static function subUnitRules($request, $id = null)
    {
        $isUpdate = !empty($id);

        $rules = [
            'unit_id' => 'required',
            'name' => $isUpdate
                ? 'required|string|max:255|unique:sub_units,name,' . $id
                : 'required|string|max:255|unique:sub_units,name',

            'status' => 'required|in:0,1',
        ];

        $messages = [
            'unit_id.required' => 'Please select a Unit.',
            'name.required' => 'Sub Unit name is required.',
            'name.string'   => 'Sub Unit name must be a string.',
            'name.max'      => 'Sub Unit name may not be greater than 255 characters.',
            'name.unique'   => 'Sub Unit name must be unique.',

            'status.required' => 'Status is required.',
            'status.in'       => 'Status must be either 0 (inactive) or 1 (active).',
        ];

        return [$rules, $messages];
    }

    public static function customerRules($request, $id = null)
    {
        $isUpdate = !empty($id);

        $rules = [
            //
        ];

        $messages = [
            //
        ];

        return [$rules, $messages];
    }

    public static function CustomerAreaRules($request, $id = null)
    {
        $isUpdate = !empty($id);

        $rules = [
            //
        ];

        $messages = [
            //
        ];

        return [$rules, $messages];
    }

    public static function IncomeExpenseTitleRules($request, $id = null)
    {
        $isUpdate = !empty($id);

        $rules = [
            //
        ];

        $messages = [
            //
        ];

        return [$rules, $messages];
    }

    public static function ExpenseEntryRules($request, $id = null)
    {
        $isUpdate = !empty($id);

        $rules = [
            //
        ];

        $messages = [
            //
        ];

        return [$rules, $messages];
    }

    public static function IncomeEntryRules($request, $id = null)
    {
        $isUpdate = !empty($id);

        $rules = [
            //
        ];

        $messages = [
            //
        ];

        return [$rules, $messages];
    }




    public static function validate($input, $rules, $messages){
        $status_code = $status_message = $error_message = null;
        $validator = Validator::make($input, $rules, $messages);
        if ($validator->fails()) {
            $status_code = ApiService::Api_VALIDATION_ERROR;
            $status_message = $validator->errors()->first();
            $error_message = $validator->errors()->toArray();
        }
        else{
            $status_code = ApiService::API_SUCCESS;
            $status_message = "Validation passed.";
            $error_message = null;
        }
        return [$status_code, $status_message, $error_message];
    }
}
