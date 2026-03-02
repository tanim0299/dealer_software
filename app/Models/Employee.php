<?php

namespace App\Models;

use App\Traits\FileUploader;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $guarded = [];

    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;

    const STATUS = [
        self::STATUS_ACTIVE => 'Active',
        self::STATUS_INACTIVE => 'Inactive',
    ];

    public function deposits()
    {
        return $this->hasMany(EmployeeSalaryDeposit::class, 'employee_id');
    }

    public function driver()
    {
        return $this->belongsTo(Drivers::class, 'driver_id');
    }

    public function withdraws()
    {
        return $this->hasMany(EmployeeSalaryWithdraw::class, 'employee_id');
    }

    public function getEmployeeList($search = [], $is_paginate = true)
    {
        $query = self::query();

        if (!empty($search['free_text'])) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search['free_text'] . '%')
                    ->orWhere('phone', 'like', '%' . $search['free_text'] . '%')
                    ->orWhere('email', 'like', '%' . $search['free_text'] . '%')
                    ->orWhere('designation', 'like', '%' . $search['free_text'] . '%')
                    ->orWhere('nid', 'like', '%' . $search['free_text'] . '%');
            });
        }

        return $is_paginate ? $query->paginate(10) : $query->get();
    }

    public function storeEmployee($request)
    {
        $preparedData = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'designation' => $request->designation,
            'nid' => $request->nid,
            'salary' => $request->salary,
            'status' => $request->status ?? self::STATUS_ACTIVE,
        ];

        if ($request->file('image')) {
            $preparedData['image'] = FileUploader::upload($request->file('image'), 'employee');
        }

        self::create($preparedData);

        return;
    }

    public function updateEmployeeById($request, $id)
    {
        $previousData = self::find($id);

        $preparedData = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'designation' => $request->designation,
            'nid' => $request->nid,
            'salary' => $request->salary,
            'status' => $request->status ?? self::STATUS_ACTIVE,
        ];

        if ($request->file('image')) {
            $preparedData['image'] = FileUploader::upload($request->file('image'), 'employee', $previousData?->image);
        }

        self::where('id', $id)->update($preparedData);

        return;
    }

    public function deleteEmployeeById($id)
    {
        $previousData = self::find($id);

        if (!empty($previousData->image)) {
            FileUploader::unlinkfile($previousData->image);
        }

        self::where('id', $id)->delete();

        return;
    }

    public function getAvailableSalaryBalance()
    {
        $totalDeposit = $this->deposits()->sum('amount');
        $totalWithdraw = $this->withdraws()->sum('amount');

        return $totalDeposit - $totalWithdraw;
    }
}
