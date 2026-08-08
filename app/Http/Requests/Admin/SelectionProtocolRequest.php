<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class SelectionProtocolRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function failedValidation(Validator $validator): void
    {
        toastr()->error($validator->errors()->first(), 'content.error');

        throw new HttpResponseException(
            redirect()->back()->withInput()->withErrors($validator)
        );
    }

    protected function prepareForValidation(): void
    {
        $manualDates = collect((array) $this->input('manual_dates', []))
            ->map(fn ($date) => is_string($date) ? trim($date) : $date)
            ->filter(fn ($date) => filled($date))
            ->values()
            ->all();

        // Hidden empty date inputs are still posted when period is not MANUAL.
        if ($this->input('selection_period') !== 'MANUAL') {
            $manualDates = null;
        }

        $this->merge([
            'exclude_previously_selected' => $this->boolean('exclude_previously_selected'),
            'automatic' => $this->boolean('automatic'),
            'calculate_pool_average' => $this->boolean('calculate_pool_average'),
            'is_active' => $this->boolean('is_active'),
            'is_email_send' => $this->boolean('is_email_send'),
            'manual_dates' => $manualDates,
        ]);
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'client_ids' => 'required|array|min:1',
            'client_ids.*' => 'exists:client_profiles,id',
            'test_id' => 'required|exists:test_admins,id',
            'group' => 'required|in:DOT,NON_DOT,ALL,FMCSA,FRA,FTA,FAA,PHMSA,RSPA,USCG',
            'dot_agency_id' => 'nullable|exists:dot_agencies,id',
            'department_filter' => 'nullable|string|max:255',
            'shift_filter' => 'nullable|string|max:255',
            'exclude_previously_selected' => 'boolean',
            'selection_requirement_type' => 'required|in:NUMBER,PERCENTAGE',
            'selection_requirement_value' => 'required|integer|min:1',
            'selection_period' => 'required|in:YEARLY,QUARTERLY,MONTHLY,MANUAL',
            'monthly_selection_day' => 'required_if:selection_period,MONTHLY|nullable|integer|min:1|max:28',
            'manual_dates' => 'required_if:selection_period,MANUAL|nullable|array|min:1',
            'manual_dates.*' => 'nullable|date',
            'alternates_type' => 'nullable|in:NUMBER,PERCENTAGE',
            'alternates_value' => 'nullable|integer|min:0',
            'automatic' => 'boolean',
            'calculate_pool_average' => 'boolean',
            'is_active' => 'boolean',
            'is_email_send' => 'boolean',
            'extra_tests' => 'sometimes|array',
            'extra_tests.*' => 'sometimes|exists:test_admins,id',
            'sub_selections' => 'sometimes|array|max:3',
            'sub_selections.*.test_id' => 'required_with:sub_selections|exists:test_admins,id',
            'sub_selections.*.requirement_type' => 'required_with:sub_selections|in:NUMBER,PERCENTAGE',
            'sub_selections.*.requirement_value' => 'required_with:sub_selections|integer|min:1',
        ];
    }

    public function protocolAttributes(): array
    {
        $alternatesValue = (int) $this->input('alternates_value', 0);

        return [
            'name' => $this->input('name'),
            'client_id' => $this->input('client_ids.0'),
            'test_id' => $this->input('test_id'),
            'group' => $this->input('group'),
            'dot_agency_id' => null,
            'department_filter' => $this->input('department_filter'),
            'shift_filter' => $this->input('shift_filter'),
            'exclude_previously_selected' => $this->boolean('exclude_previously_selected'),
            'selection_requirement_type' => $this->input('selection_requirement_type'),
            'selection_requirement_value' => $this->input('selection_requirement_value'),
            'selection_period' => $this->input('selection_period'),
            'monthly_selection_day' => $this->input('selection_period') === 'MONTHLY'
                ? $this->input('monthly_selection_day')
                : null,
            'manual_dates' => $this->input('selection_period') === 'MANUAL'
                ? array_values(array_filter((array) $this->input('manual_dates', [])))
                : null,
            'alternates_type' => $alternatesValue > 0 ? $this->input('alternates_type') : null,
            'alternates_value' => $alternatesValue > 0 ? $alternatesValue : 0,
            'automatic' => $this->boolean('automatic'),
            'calculate_pool_average' => $this->boolean('calculate_pool_average'),
            'is_active' => $this->boolean('is_active'),
            'is_email_send' => $this->boolean('is_email_send'),
        ];
    }
}
