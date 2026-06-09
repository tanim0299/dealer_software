<?php
namespace App\Services;

use App\Models\ExpenseEntry;
use Illuminate\Support\Facades\Auth;

class ExpenseEntryService
{
    public function ExpenseEntryList($search = [], $is_paginate = true) : array
    {
        $status_code = $status_message = $response = '';
        try {
            $response = (new ExpenseEntry())->ExpenseEntryList($search, $is_paginate);
            $status_code = ApiService::API_SUCCESS;
            $status_message = 'Data Found';
        } catch(\Throwable $th){
            $status_code = ApiService::API_SERVER_ERROR;
            $status_message = ApiService::friendlyExceptionMessage($th);
        }

        return [$status_code, $status_message, $response];
    }

    public function storeExpenseEntry($request)
    {
        $status_code = $status_message = $error_message = null;
        [$rules, $messages] = RequestRules::ExpenseEntryRules($request->all());

        $validation = RequestRules::validate($request->all(), $rules, $messages);
        if ($validation[0] !== ApiService::API_SUCCESS) {
            $status_code = $validation[0];
            $status_message = $validation[1];
            $error_message = $validation[2];
        }
        else
        {
            try {
                if (Auth::user()?->hasRole('Driver') && Auth::user()->driver_id) {
                    DriverPeriodService::assertDriverPanelDateOpenForTransaction(
                        (int) Auth::user()->driver_id,
                        (string) $request->date
                    );
                }

                $this->assertDriverExpenseWithinCarryingCash((float) $request->amount);

                (new ExpenseEntry())->createExpenseEntry($request);
                $status_code = ApiService::API_SUCCESS;
                $status_message = "Expense Entry created successfully.";
                $error_message = null;
            } catch (\Throwable $th) {
                $status_code = ApiService::API_SERVER_ERROR;
                $status_message = ApiService::friendlyExceptionMessage($th);
                $error_message = [ApiService::friendlyExceptionMessage($th)];
            }
        }

        return [$status_code, $status_message, $error_message];
    }

    public function getExpenseEntryById($id)
    {
        $status_code = $status_message = $ExpenseEntry = '';
        try {
            $ExpenseEntry = (new ExpenseEntry())->getExpenseEntryById($id);
            $status_code = ApiService::API_SUCCESS;
            $status_message = 'Data Found';
        } catch (\Throwable $th) {
            $status_code = ApiService::API_SERVER_ERROR;
            $status_message = ApiService::friendlyExceptionMessage($th);
        }

        return [$status_code, $status_message, $ExpenseEntry];
    }

    public function updateExpenseEntry($request, $id)
    {
        $status_code = $status_message = $error_message = null;
        [$rules, $messages] = RequestRules::ExpenseEntryRules($request->all(), $id);

        $validation = RequestRules::validate($request->all(), $rules, $messages);
        if ($validation[0] !== ApiService::API_SUCCESS) {
            $status_code = $validation[0];
            $status_message = $validation[1];
            $error_message = $validation[2];
        }
        else
        {
            try {
                $existing = ExpenseEntry::find($id);
                if ($existing !== null && Auth::user()?->hasRole('Driver') && Auth::user()->driver_id) {
                    DriverPeriodService::assertDriverPanelDateOpenForTransaction(
                        (int) Auth::user()->driver_id,
                        (string) $existing->date
                    );
                    DriverPeriodService::assertDriverPanelDateOpenForTransaction(
                        (int) Auth::user()->driver_id,
                        (string) $request->date
                    );
                }

                if ($existing !== null && Auth::user()?->hasRole('Driver')) {
                    $this->assertDriverExpenseWithinCarryingCash((float) $request->amount, $existing);
                }

                (new ExpenseEntry())->updateExpenseEntry($request, $id);
                $status_code = ApiService::API_SUCCESS;
                $status_message = "Expense Entry updated successfully.";
                $error_message = null;
            } catch (\Throwable $th) {
                $status_code = ApiService::API_SERVER_ERROR;
                $status_message = ApiService::friendlyExceptionMessage($th);
                $error_message = [ApiService::friendlyExceptionMessage($th)];
            }
        }

        return [$status_code, $status_message, $error_message];
    }

    /**
     * Drivers cannot expense more than open-period carrying cash (same basis as DriverCashService).
     * When updating, pass existing row so its amount is added back before comparing.
     */
    private function assertDriverExpenseWithinCarryingCash(float $amount, ?ExpenseEntry $replaceEntry = null): void
    {
        $user = Auth::user();
        if (! $user || ! $user->hasRole('Driver') || ! $user->driver_id) {
            return;
        }

        $driverId = (int) $user->driver_id;
        $userId = (int) $user->id;

        $available = DriverCashService::openPeriodAvailableCash($userId, $driverId);
        if ($replaceEntry !== null
            && (int) ($replaceEntry->driver_id ?? 0) === $driverId
        ) {
            $available += (float) $replaceEntry->amount;
        }

        if ($amount > $available + 0.02) {
            throw new \RuntimeException(
                'Not enough carrying cash for this expense. Available after prior spending: Tk '
                .number_format($available, 2)
                .'; entered: Tk '.number_format($amount, 2)
            );
        }
    }

    public function deleteExpenseEntry($id)
    {
        $status_code = $status_message = null;

        try {
            [$status_code, $status_message, $entry] = self::getExpenseEntryById($id);
            if (Auth::user()?->hasRole('Driver') && Auth::user()->driver_id && $entry) {
                if ((int) ($entry->driver_id ?? 0) !== (int) Auth::user()->driver_id) {
                    throw new \RuntimeException('Unauthorized expense delete attempt.');
                }
                DriverPeriodService::assertDriverPanelDateOpenForTransaction(
                    (int) Auth::user()->driver_id,
                    (string) $entry->date
                );
            }

            ExpenseEntry::where('id', $id)->delete();

            $status_code = ApiService::API_SUCCESS;
            $status_message = __('Expense Entry deleted successfully.');
        }
        catch (\Throwable $th) {
            $status_code = ApiService::API_SERVER_ERROR;
            $status_message = ApiService::friendlyExceptionMessage($th);
        }

        return [$status_code, $status_message];
    }

    public function updateExpenseEntryStatus($id)
    {
        $status_code = $status_message = null;
        try {
            (new ExpenseEntry())->updateStatus($id);
            $status_code = ApiService::API_SUCCESS;
            $status_message = "Expense Entry status updated successfully.";
        } catch (\Throwable $th) {
            $status_code = ApiService::API_SERVER_ERROR;
            $status_message = ApiService::friendlyExceptionMessage($th);
        }

        return [$status_code, $status_message];
    }
}
