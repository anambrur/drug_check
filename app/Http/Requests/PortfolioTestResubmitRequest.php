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
        $application = PortfolioTestApplication::find($this->route('id'));

        if (!$application || $application->payment_status !== 'completed') {
            return false;
        }

        if (auth()->check() && (int) $application->user_id === (int) auth()->id()) {
            return true;
        }

        return $application->is_guest
            && $application->isNonDot()
            && $application->guestSessionMatches();
    }

    public function rules(): array
    {
        $isPhysical = $this->isPhysical();
        $isEbat = $this->isEbat();
        $isDot = $this->input('test_type') === 'dot';

        $endDatetimeRules = ['nullable', 'date_format:Y-m-d\TH:i', 'after:now'];
        if ($isPhysical) {
            $endDatetimeRules[] = 'before_or_equal:' . now()->addHours(168)->format('Y-m-d\TH:i');
        }

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
            'primary_id_type' => [Rule::prohibitedIf(!$isPhysical), 'nullable', 'in:DL'],
            'dob' => ['nullable', 'string'],
            'zip_code' => ['nullable', 'string', 'max:10'],

            'dot_test' => ['required', 'in:T,F', Rule::in([$isDot ? 'T' : 'F'])],
            'testing_authority' => ['required_if:dot_test,T', 'nullable', 'in:FMCSA,PHMSA,FAA,FTA,FRA,USCG'],
            'reason_for_test_id' => [Rule::requiredIf(!$isPhysical), 'nullable', 'integer', 'in:1,2,3,5,6,23,99'],
            'physical_reason_for_test_id' => [Rule::requiredIf($isPhysical), 'nullable', 'in:NC,RE,FU,OT,SA,PE,RD,SU'],
            'collection_site_id' => [Rule::prohibitedIf($isPhysical), 'nullable', 'string', 'max:6'],
            'end_datetime' => $endDatetimeRules,
            'end_datetime_timezone_id' => ['nullable', 'required_with:end_datetime', 'integer', 'between:1,8'],
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
            ->find($this->route('id'));

        if ($application) {
            $authorized = (auth()->check() && (int) $application->user_id === (int) auth()->id())
                || ($application->is_guest && $application->isNonDot() && $application->guestSessionMatches());

            if (!$authorized) {
                $application = null;
            }
        }

        $flags = ['is_physical' => false, 'is_ebat' => false];
        $testType = $this->input('test_type');

        if ($application) {
            $flags = app(PortfolioTestApplicationService::class)->portfolioFlags($application->portfolio);
            $testType = $application->test_type;
        }

        $dotTest = $testType === 'dot' ? 'T' : 'F';
        $observed = $this->input('observed_requested');
        $reasonId = (int) $this->input('reason_for_test_id');
        if ($dotTest === 'T' && in_array($reasonId, [6, 23], true)) {
            $observed = 'Y';
        }

        $this->merge([
            'test_type' => $testType,
            'dot_test' => $dotTest,
            'observed_requested' => $observed ?: 'N',
            'is_physical' => $flags['is_physical'] ? 'true' : 'false',
            'is_ebat' => $flags['is_ebat'] ? 'true' : 'false',
            'collection_site_id' => $flags['is_physical'] ? null : $this->input('collection_site_id'),
            'primary_id_type' => $flags['is_physical'] ? $this->input('primary_id_type') : null,
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
