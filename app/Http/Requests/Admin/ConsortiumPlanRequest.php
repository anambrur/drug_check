<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ConsortiumPlanRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Permissions can be handled via controller middleware/gates
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $planId = $this->route('id'); // route param is {id}, not {plan}

        return [
            'name' => 'required|string|max:255|unique:consortium_plans,name,' . $planId,
            'slug' => 'required|string|max:255|unique:consortium_plans,slug,' . $planId,
            'description' => 'nullable|string',
            'min_drivers' => 'nullable|integer|min:1',
            'max_drivers' => 'nullable|integer|min:1|gte:min_drivers',
            'display_order' => 'required|integer|min:0',
            'is_active' => 'required|boolean',
            'fees' => 'required|array|min:1',
            'fees.*.fee_label' => 'required|string|max:255',
            'fees.*.fee_key' => 'required|string|regex:/^[a-z_]+$/',
            'fees.*.fee_amount' => 'required|numeric|min:0',
            'fees.*.fee_type' => 'required|in:flat,per_driver',
            'fees.*.display_order' => 'required|integer|min:0',
        ];
    }
}
