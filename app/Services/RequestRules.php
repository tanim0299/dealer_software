<?php
namespace App\Services;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RequestRules{
    
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
