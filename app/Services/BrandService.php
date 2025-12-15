<?php
namespace App\Services;

use App\Models\Brand;
use Symfony\Component\HttpFoundation\Request;

class BrandService
{
    public function BrandList($search = [], $is_paginate = true) : array
    {
        $status_code = $status_message = $response = '';
        try {
            $response = (new Brand())->BrandList($search, $is_paginate);
            $status_code = ApiService::API_SUCCESS;
            $status_message = 'Data Found';
        } catch(\Throwable $th){
            $status_code = ApiService::API_SERVER_ERROR;
            $status_message = $th->getMessage();
        }

        return [$status_code, $status_message, $response];
    }

    public function storeBrand($request)
    {
        $status_code = $status_message = $error_message = null;
        [$rules, $messages] = RequestRules::brandRules($request->all());

        $validation = RequestRules::validate($request->all(), $rules, $messages);
        if ($validation[0] !== ApiService::API_SUCCESS) {
            $status_code = $validation[0];
            $status_message = $validation[1];
            $error_message = $validation[2];
        }
        else
        {
            try {
                (new Brand())->createBrand($request);
                $status_code = ApiService::API_SUCCESS;
                $status_message = "Brand created successfully.";
                $error_message = null;
            } catch (\Throwable $th) {
                $status_code = ApiService::API_SERVER_ERROR;
                $status_message = $th->getMessage();
                $error_message = [$th->getMessage()];
            }
        }

        return [$status_code, $status_message, $error_message];
    }

    public function getBrandById($id)
    {
        $status_code = $status_message = $brand = '';
        try {
            $brand = (new Brand())->getBrandById($id);
            $status_code = ApiService::API_SUCCESS;
            $status_message = 'Data Found';
        } catch (\Throwable $th) {
            $status_code = ApiService::API_SERVER_ERROR;
            $status_message = $th->getMessage();
        }

        return [$status_code, $status_message, $brand];
    }

    public function updateBrand($request, $id)
    {
        $status_code = $status_message = $error_message = null;
        [$rules, $messages] = RequestRules::brandRules($request->all(), $id);

        $validation = RequestRules::validate($request->all(), $rules, $messages);
        if ($validation[0] !== ApiService::API_SUCCESS) {
            $status_code = $validation[0];
            $status_message = $validation[1];
            $error_message = $validation[2];
        }
        else
        {
            try {
                $updated = (new Brand())->updateBrand($request, $id);
                if ($updated) {
                    $status_code = ApiService::API_SUCCESS;
                    $status_message = "Brand updated successfully.";
                    $error_message = null;
                } else {
                    $status_code = ApiService::API_NOT_FOUND;
                    $status_message = "Brand not found.";
                    $error_message = ["Brand not found."];
                }
            } catch (\Throwable $th) {
                $status_code = ApiService::API_SERVER_ERROR;
                $status_message = $th->getMessage();
                $error_message = [$th->getMessage()];
            }
        }

        return [$status_code, $status_message, $error_message];
    }

    public function deleteBrand($id)
    {
        $status_code = $status_message = null;

        try {
            [$status_code, $status_message] = self::getBrandById($id);
            Brand::where('id', $id)->delete();

            $status_code = ApiService::API_SUCCESS;
            $status_message = __('Brand deleted successfully.');
        }
        catch (\Throwable $th) {
            $status_code = ApiService::API_SERVER_ERROR;
            $status_message = $th->getMessage();
        }

        return [$status_code, $status_message];
    }

    public function updateBrandStatus($id)
    {
        $status_code = $status_message = null;
        try {
            (new Brand())->updateStatus($id);
            $status_code = ApiService::API_SUCCESS;
            $status_message = "Brand status updated successfully.";
        } catch (\Throwable $th) {
            $status_code = ApiService::API_SERVER_ERROR;
            $status_message = $th->getMessage();
        }

        return [$status_code, $status_message];
    }
}
