<?php
namespace App\Services;

class RequestRules{
    public static function menuSectionRules(){
        $rules =  [
            'section_name' => 'required|string|max:255',
            'status' => 'required|in:0,1',
        ];
    }   
}