<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DriverCashDistribution extends Model
{
    protected $guarded = [];

    protected static function booted(): void
    {
        static::deleting(function (DriverCashDistribution $distribution): void {
            $withdrawId = $distribution->employee_salary_withdraw_id;
            if ($withdrawId) {
                EmployeeSalaryWithdraw::whereKey($withdrawId)->delete();
            }
        });
    }

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
