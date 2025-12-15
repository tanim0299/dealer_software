<?php
namespace App\Services;

use App\Models\Category;

class CategoryService
{
    public function CategoryList($search = [], $is_paginate = true) : array
    {
        $status_code = $status_message = $response = '';
        try {
            $response = (new Category())->CategoryList($search, $is_paginate);
            $status_code = ApiService::API_SUCCESS;
            $status_message = 'Data Found';
        } catch(\Throwable $th){
            $status_code = ApiService::API_SERVER_ERROR;
            $status_message = $th->getMessage();
        }

        return [$status_code, $status_message, $response];
    }

    public function storeCategory($request)
    {
        $status_code = $status_message = $error_message = null;
        [$rules, $messages] = RequestRules::categoryRules($request->all());

        $validation = RequestRules::validate($request->all(), $rules, $messages);
        if ($validation[0] !== ApiService::API_SUCCESS) {
            $status_code = $validation[0];
            $status_message = $validation[1];
            $error_message = $validation[2];
        }
        else
        {
            try {
                (new Category())->createCategory($request);
                $status_code = ApiService::API_SUCCESS;
                $status_message = "Category created successfully.";
                $error_message = null;
            } catch (\Throwable $th) {
                $status_code = ApiService::API_SERVER_ERROR;
                $status_message = $th->getMessage();
                $error_message = [$th->getMessage()];
            }
        }

        return [$status_code, $status_message, $error_message];
    }

    public function getCategoryById($id)
    {
        $status_code = $status_message = $category = '';
        try {
            $category = (new Category())->getCategoryById($id);
            $status_code = ApiService::API_SUCCESS;
            $status_message = 'Data Found';
        } catch (\Throwable $th) {
            $status_code = ApiService::API_SERVER_ERROR;
            $status_message = $th->getMessage();
        }

        return [$status_code, $status_message, $category];
    }

    public function updateCategory($request, $id)
    {
        $status_code = $status_message = $error_message = null;
        [$rules, $messages] = RequestRules::categoryRules($request->all(), $id);

        $validation = RequestRules::validate($request->all(), $rules, $messages);
        if ($validation[0] !== ApiService::API_SUCCESS) {
            $status_code = $validation[0];
            $status_message = $validation[1];
            $error_message = $validation[2];
        }
        else
        {
            try {
                (new Category())->updateCategory($request, $id);
                $status_code = ApiService::API_SUCCESS;
                $status_message = "Category updated successfully.";
                $error_message = null;
            } catch (\Throwable $th) {
                $status_code = ApiService::API_SERVER_ERROR;
                $status_message = $th->getMessage();
                $error_message = [$th->getMessage()];
            }
        }

        return [$status_code, $status_message, $error_message];
    }

    public function deleteCategory($id)
    {
        $status_code = $status_message = null;

        try {
            [$status_code, $status_message] = self::getCategoryById($id);
            Category::where('id', $id)->delete();

            $status_code = ApiService::API_SUCCESS;
            $status_message = __('Category deleted successfully.');
        }
        catch (\Throwable $th) {
            $status_code = ApiService::API_SERVER_ERROR;
            $status_message = $th->getMessage();
        }

        return [$status_code, $status_message];
    }

    public function updateCategoryStatus($id)
    {
        $status_code = $status_message = null;
        try {
            (new Category())->updateStatus($id);
            $status_code = ApiService::API_SUCCESS;
            $status_message = "Category status updated successfully.";
        } catch (\Throwable $th) {
            $status_code = ApiService::API_SERVER_ERROR;
            $status_message = $th->getMessage();
        }

        return [$status_code, $status_message];
    }
}
