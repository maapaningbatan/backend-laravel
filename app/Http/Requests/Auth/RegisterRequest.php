<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    // Optional: authorize only guests or always true
    public function authorize()
    {
        return true; // set to false if you want to restrict access
    }

    public function rules()
    {
       return [
        'email_address' => 'required|email|unique:tbl_user,Email_Address',
        'username' => 'required|string|max:255|unique:tbl_user,Username',
        'password' => 'required|string|confirmed|min:6',
        'honorifics' => 'nullable|string|max:50',
        'first_name' => 'required|string|max:255',
        'middle_name' => 'nullable|string|max:255',
        'last_name' => 'required|string|max:255',
        'suffix' => 'nullable|string|max:50',
        'title' => 'nullable|string|max:50',
        'sex' => 'required|integer|in:0,1',
        'contact_number' => 'nullable|string|max:20',
        'address' => 'nullable|string|max:500',
        'employee_id' => 'required|string|max:50|unique:lib_employee,Employee_Id',
        'position' => 'required|integer',
        'region' => 'required|integer',
        'office' => 'required|integer',
        'division' => 'nullable|integer',
        'cluster' => 'required|integer',
        'soa' => 'nullable|integer',
        'soe' => 'nullable|integer',
        'user_level' => 'required|integer',
        'upload_contract' => 'file|nullable',
    ];
    }
}
