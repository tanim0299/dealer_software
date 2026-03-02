<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DriverCashDistribution extends Model
{
    protected $guarded = [];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function driver()
    {
        return $this->belongsTo(Drivers::class, 'driver_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function salaryWithdraw()
    {
        return $this->belongsTo(EmployeeSalaryWithdraw::class, 'employee_salary_withdraw_id');
    }
}
