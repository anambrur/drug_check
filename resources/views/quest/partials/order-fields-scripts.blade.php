<script>
document.addEventListener('DOMContentLoaded', function () {
    if (typeof flatpickr !== 'undefined') {
        flatpickr('.quest-dob-picker', {
            dateFormat: 'm/d/Y',
            maxDate: 'today',
            allowInput: false,
        });
    }

    document.querySelectorAll('.quest-phone-digits').forEach(function (el) {
        el.addEventListener('input', function () {
            this.value = this.value.replace(/\D/g, '');
        });
    });

    const dotTestSelect = document.getElementById('dot_test');
    const testingAuthorityField = document.getElementById('testingAuthorityField');
    const testingAuthoritySelect = document.getElementById('testing_authority');

    function toggleTestingAuthority() {
        if (!testingAuthorityField || !dotTestSelect) return;
        const show = dotTestSelect.value === 'T';
        testingAuthorityField.style.display = show ? 'block' : 'none';
        if (testingAuthoritySelect) {
            testingAuthoritySelect.required = show;
        }
    }

    if (dotTestSelect) {
        dotTestSelect.addEventListener('change', toggleTestingAuthority);
        toggleTestingAuthority();
    }

    const endDateTime = document.getElementById('end_datetime');
    const isPhysical = {{ ($questIsPhysical ?? false) ? 'true' : 'false' }};
    if (endDateTime && isPhysical) {
        endDateTime.addEventListener('change', function () {
            const sel = new Date(this.value);
            const max = new Date(Date.now() + 168 * 3600 * 1000);
            if (sel > max) {
                alert('For physical tests, the expiration date must be within 7 days.');
                this.value = '';
            }
        });
    }

    @if (!$isNonDot && ($employees ?? collect())->isNotEmpty())
    @php
        $employeePrefillData = ($employees ?? collect())->mapWithKeys(function ($employee) {
            return [
                $employee->id => [
                    'first_name' => $employee->first_name,
                    'last_name' => $employee->last_name,
                    'middle_name' => $employee->middle_name,
                    'primary_id' => $employee->employee_id,
                    'email' => $employee->email,
                    'primary_phone' => $employee->phone,
                    'dob' => $employee->date_of_birth
                        ? \Carbon\Carbon::parse($employee->date_of_birth)->format('m/d/Y')
                        : '',
                ],
            ];
        });
    @endphp
    const employeeData = @json($employeePrefillData);

    const employeeSelect = document.getElementById('employee_id');
    if (employeeSelect) {
        employeeSelect.addEventListener('change', function () {
            const data = employeeData[this.value];
            if (!data) return;
            const set = (id, val) => {
                const el = document.getElementById(id);
                if (el) el.value = val || '';
            };
            set('first_name', data.first_name);
            set('last_name', data.last_name);
            set('middle_name', data.middle_name);
            set('primary_id', data.primary_id);
            set('email', data.email);
            set('primary_phone', data.primary_phone);
            set('dob', data.dob);
        });
    }
    @endif

    if (typeof $ !== 'undefined' && $.fn.select2) {
        const siteSelect = $('.select2-collection-sites');
        @if (!empty($initialCollectionSite))
        const initialSite = @json($initialCollectionSite);
        if (initialSite && initialSite.id) {
            const option = new Option(initialSite.text, initialSite.id, true, true);
            siteSelect.append(option);
        }
        @endif

        siteSelect.select2({
            placeholder: 'Search collection sites…',
            allowClear: true,
            minimumInputLength: 2,
            ajax: {
                url: '{{ route('collection-sites.search') }}',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return { q: params.term };
                },
                processResults: function (data) {
                    return {
                        results: data.map(function (s) {
                            return { id: s.collection_site_code, text: s.text };
                        }),
                    };
                },
            },
        });
    }
});
</script>
