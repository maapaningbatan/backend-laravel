<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize()
    {
        return true; // allow registration
    }

    public function rules()
    {
        return [
            'email' => 'required|email|unique:tbl_users,email',
            'username' => 'required|string|max:255|unique:tbl_users,username',
            'password' => 'required|string|confirmed|min:6',
            'honorific' => 'nullable|string|max:50',
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'suffix' => 'nullable|string|max:50',
            'title' => 'nullable|string|max:50',
            'sex' => 'required|integer|in:0,1',
            'contact_number' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'employee_no' => 'required|string|max:50|unique:lib_employees,employee_no',
            'position_id' => 'required|integer|exists:lib_positions,id',
            'region_id' => 'required|integer|exists:lib_regions,id',
            'office_id' => 'required|integer|exists:lib_offices,id',
            'division_id' => 'nullable|integer|exists:lib_divisions,id',
            'cluster_id' => 'required|integer|exists:lib_clusters,id',
            'center_id' => 'nullable|integer|exists:lib_center,center_id',
            'user_level_id' => 'nullable|integer',
            'upload_contract' => 'nullable|file',
        ];
    }
}
