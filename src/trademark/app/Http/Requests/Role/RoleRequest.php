<?php

namespace App\Http\Requests\Role;

use App\Http\Requests\FormRequest;

class RoleRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return  array
     */
    public function rules()
    {

        return [
            'name' => 'required|max:255',
        ];
    }
}
