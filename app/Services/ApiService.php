<?php
namespace App\Services;

class ApiService{
    const API_SUCCESS = 200;
    const API_NOT_FOUND = 404;
    const API_SERVER_ERROR = 500;
    const API_UNAUTHORIZED = 401;
    const API_FORBIDDEN = 403;
    const API_BAD_REQUEST = 400;
    const Api_VALIDATION_ERROR = 422;

    public static function friendlyExceptionMessage(\Throwable $th): string
    {
        $message = $th->getMessage();

        if (str_contains($message, 'SQLSTATE[23000]') || str_contains($message, 'Integrity constraint violation')) {
            return self::friendlyDatabaseMessage($message);
        }

        return $message ?: 'Something went wrong. Please check the form and try again.';
    }

    public static function friendlyDatabaseMessage(string $message): string
    {
        $map = [
            'employees_phone_unique' => 'This phone number is already used by another employee or DSR.',
            'employees_email_unique' => 'This email is already used by another employee.',
            'employees_nid_unique' => 'This NID is already used by another employee.',
            'users_email_unique' => 'This email is already used by another user.',
            'purchase_ledgers_invoice_no_unique' => 'This invoice number already exists.',
            'bank_accounts_account_number_unique' => 'This bank account number already exists.',
            'cash_closes_close_date_unique' => 'Cash is already closed for this date.',
            'permissions_name_guard_name_unique' => 'This permission already exists.',
            'roles_name_guard_name_unique' => 'This role already exists.',
        ];

        foreach ($map as $key => $friendly) {
            if (str_contains($message, $key)) {
                return $friendly;
            }
        }

        if (str_contains($message, 'Duplicate entry')) {
            return 'This value already exists. Please use a different value.';
        }

        if (str_contains($message, 'foreign key constraint fails')) {
            return 'This record is linked with other data or contains an invalid selection.';
        }

        if (str_contains($message, 'cannot be null')) {
            return 'Required information is missing. Please fill all required fields.';
        }

        return 'The form could not be saved because of a database constraint. Please check the values and try again.';
    }
}
