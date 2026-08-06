@php
    $companyName = $result->clientProfile->company_name ?? 'N/A';
    $employeeFirst = $result->employee->first_name ?? '';
    $employeeLast = $result->employee->last_name ?? '';
    $employeeName = trim($employeeFirst . ' ' . $employeeLast) ?: 'N/A';
    $collectedDate = $result->collection_datetime
        ? \Carbon\Carbon::parse($result->collection_datetime)->format('m/d/Y')
        : 'N/A';
@endphp

<div class="d-flex align-items-center justify-content-center rr-actions">
    @if ($canEdit)
        <a href="{{ route('result-recording.edit', $result->id) }}" class="mr-2" title="Edit">
            <i class="fa fa-edit text-info font-18"></i>
        </a>
    @endif

    @if ($canView)
        <a href="{{ route('result-recording.show', $result->id) }}" class="mr-2" title="View">
            <i class="fa fa-eye text-success font-18"></i>
        </a>
    @endif

    @if ($canDelete)
        <a href="#" class="mr-2 rr-delete-btn" title="Delete"
           data-id="{{ $result->id }}"
           data-url="{{ route('result-recording.destroy', $result->id) }}">
            <i class="fa fa-trash text-danger font-18"></i>
        </a>
    @endif

    @if ($canEdit)
        <a href="#" class="rr-notify-btn" title="Notify"
           data-id="{{ $result->id }}"
           data-url="{{ route('result-recording.send-notification', $result->id) }}"
           data-company="{{ e($companyName) }}"
           data-employee="{{ e($employeeName) }}"
           data-phone="{{ e($result->clientProfile->phone ?? 'N/A') }}"
           data-der-name="{{ e($result->clientProfile->der_contact_name ?? 'N/A') }}"
           data-der-email="{{ e($result->clientProfile->der_contact_email ?? 'N/A') }}"
           data-date="{{ e($collectedDate) }}">
            <i class="fa fa-send-o font-18"></i>
        </a>
    @endif
</div>
