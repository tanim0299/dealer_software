<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeSalaryDeposit extends Model
{
    protected $guarded = [];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
