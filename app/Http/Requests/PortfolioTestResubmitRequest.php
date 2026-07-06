<?php

namespace App\Http\Requests;

use App\Models\PortfolioTestApplication;
use App\Services\PortfolioTestApplicationService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PortfolioTestResubmitRequest extends FormRequest
{
    public function authorize(): bool
    {
        $application = PortfolioTestApplication::where('user_id', auth()->id())
            ->find($this->route('id'));

        return $application && $application->payment_status === 'completed';
    }

    public function rules(): array
    {
        $isPhysical = $this->isPhysical();
        $isEbat = $this->isEbat();

        return [
            'test_type' => ['required', 'in:dot,non_dot'],
            'employee_id' => ['required_if:test_type,dot', 'nullable', 'integer', 'exists:employees,id'],

            'first_name' => ['required', 'string', 'max:20'],
            'last_name' => ['required', 'string', 'max:25'],
            'middle_name' => ['nullable', 'string', 'max:20'],
            'email' => ['required', 'email:rfc,dns', 'max:254'],
            'primary_phone' => ['nullable', 'string', 'max:20'],
            'secondary_phone' => ['nullable', 'string', 'max:20'],
            'primary_id' => ['required', 'string', 'max:25'],
            'primary_id_type' => ['nullable', 'string', 'max:5'],
            'dob' => ['nullable', 'string'],
            'zip_code' => ['nullable', 'string', 'max:10'],

            'dot_test' => ['required', 'in:T,F'],
            'testing_authority' => ['required_if:dot_test,T', 'nullable', 'in:FMCSA,PHMSA,FAA,FTA,FRA,USCG'],
            'reason_for_test_id' => [Rule::requiredIf(!$isPhysical), 'nullable', 'integer'],
            'physical_reason_for_test_id' => [Rule::requiredIf($isPhysical), 'nullable', 'in:NC,RE,FU,OT,SA,PE,RD,SU'],
            'collection_site_id' => ['nullable', 'string', 'max:6'],
            'end_datetime' => ['nullable', 'date_format:Y-m-d\TH:i'],
            'end_datetime_timezone_id' => ['nullable', 'integer', 'between:1,8'],
            'observed_requested' => ['nullable', 'in:Y,N'],
            'split_specimen_requested' => ['nullable', 'in:Y,N'],
            'csl' => ['nullable', 'string', 'max:20'],
            'contact_name' => [Rule::requiredIf($isEbat), 'nullable', 'string', 'max:45'],
            'telephone_number' => [Rule::requiredIf($isEbat), 'nullable', 'string', 'max:20'],
            'order_comments' => ['nullable', 'string', 'max:250'],

            'is_physical' => ['required', 'in:true,false,0,1'],
            'is_ebat' => ['required', 'in:true,false,0,1'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $application = PortfolioTestApplication::with('portfolio')
            ->where('user_id', auth()->id())
            ->find($this->route('id'));

        if ($application) {
            $flags = app(PortfolioTestApplicationService::class)->portfolioFlags($application->portfolio);
            $this->merge([
                'test_type' => $application->test_type,
                'is_physical' => filter_var($flags['is_physical'], FILTER_VALIDATE_BOOLEAN) ? 'true' : 'false',
                'is_ebat' => filter_var($flags['is_ebat'], FILTER_VALIDATE_BOOLEAN) ? 'true' : 'false',
            ]);
        }

        $this->merge([
            'is_physical' => filter_var($this->input('is_physical', false), FILTER_VALIDATE_BOOLEAN) ? 'true' : 'false',
            'is_ebat' => filter_var($this->input('is_ebat', false), FILTER_VALIDATE_BOOLEAN) ? 'true' : 'false',
        ]);
    }

    public function isPhysical(): bool
    {
        return $this->input('is_physical') === 'true';
    }

    public function isEbat(): bool
    {
        return $this->input('is_ebat') === 'true';
    }
}
