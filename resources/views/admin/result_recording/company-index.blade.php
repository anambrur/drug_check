@extends('layouts.admin.master')

@section('content')
    
    <!-- Form row -->
    <div class="row">
        <div class="col-xl-12 box-margin height-card">
            <div class="card card-body">
                <table id="basic-datatable" class="table table-striped dt-responsive w-100">
                    <thead>
                        <tr>
                            <th>Collected</th>
                            <th>Client / Company</th>
                            <th>Employee</th>
                            <th>Reason</th>
                            <th>Test</th>
                            <th>{{ __('content.status') }}</th>
                            <th class="custom-width-action">{{ __('content.action') }}</th>
                        </tr>
                    </thead>

                    <tbody>

                        @foreach ($recoding_results as $result)
                            <tr>
                                <td>
                                    {{ \Carbon\Carbon::parse($result->collection_datetime)->format('Y-m-d h:i A') }}
                                </td>

                                <td>{{ $result->clientProfile->company_name }}</td>
                                <td>{{ $result->employee->first_name }} {{ $result->employee->first_name }}</td>
                                <td>{{ $result->reason_for_test }}</td>
                                <td>{{ $result->testAdmin->test_name }}</td>
                                <td>
                                    @if ($result->status == 'positive')
                                        <span class="badge badge-pill badge-success">Positive</span>
                                    @elseif ($result->status == 'negative')
                                        <span class="badge badge-pill badge-danger">Negative</span>
                                    @elseif ($result->status == 'refused')
                                        <span class="badge badge-pill badge-danger">Refused</span>
                                    @elseif ($result->status == 'excused')
                                        <span class="badge badge-pill badge-danger">Excused</span>
                                    @elseif ($result->status == 'cancelled')
                                        <span class="badge badge-pill badge-danger">Cancelled</span>
                                    @elseif ($result->status == 'pending')
                                        <span class="badge badge-pill badge-danger">Pending</span>
                                    @elseif ($result->status == 'saved')
                                        <span class="badge badge-pill badge-danger">Saved</span>
                                    @elseif ($result->status == 'collection_only')
                                        <span class="badge badge-pill badge-danger">Collection Only</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex">
                                        @can('result recording edit')
                                            <a href="{{ route('result-recording.edit', $result->id) }}" class="mr-2">
                                                <i class="fa fa-edit text-info font-18"></i>
                                            </a>
                                        @endcan

                                        @can('result recording view')
                                            <a href="{{ route('result-recording.show', $result->id) }}" class="mr-2">
                                                <i class="fa fa-eye text-success font-18"></i>
                                            </a>
                                        @endcan

                                        @can('result recording delete')
                                            <a href="#" data-toggle="modal"
                                                data-target="#deleteModal{{ $result->id }}">
                                                <i class="fa fa-trash text-danger font-18 mr-2"></i>
                                            </a>
                                        @endcan

                                        @can('result recording edit')
                                            <a href="#" data-toggle="modal"
                                                data-target="#notifyModal{{ $result->id }}">
                                                <i class="fa fa-send-o font-18"></i>
                                            </a>
                                        @endcan
                                    </div>
                                </td>
                            </tr>

                            <!-- Modal -->
                            <div class="modal fade" id="deleteModal{{ $result->id }}" tabindex="-1" role="dialog"
                                aria-labelledby="resultModalCenterTitle" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="resultModalCenterTitle">
                                                {{ __('content.delete') }}</h5>
                                            <button type="button" class="close" data-dismiss="modal"
                                                aria-label="{{ __('content.close') }}">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body text-center">
                                            {{ __('content.you_wont_be_able_to_revert_this') }}
                                        </div>
                                        <div class="modal-footer">
                                            @if ($demo_mode == 'on')
                                                <!-- Include Alert Blade -->
                                                @include('admin.demo_mode.demo-mode')
                                            @else
                                                <form class="d-inline-block"
                                                    action="{{ route('result-recording.destroy', $result->id) }}"
                                                    method="POST">
                                                    @method('DELETE')
                                                    @csrf
                                            @endif

                                            <button type="button" class="btn btn-danger"
                                                data-dismiss="modal">{{ __('content.cancel') }}</button>
                                            <button type="submit"
                                                class="btn btn-success">{{ __('content.yes_delete_it') }}</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <!-- Modal2 -->
                            <div class="modal fade" id="notifyModal{{ $result->id }}" tabindex="-1" role="dialog"
                                aria-labelledby="notifyModalTitle" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="notifyModalTitle">Notify Client:
                                                {{ $result->clientProfile->company_name }} of Test Results and Random
                                                Selections</h5>
                                            <button type="button" class="close" data-dismiss="modal"
                                                aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <form method="POST"
                                            action="{{ route('result-recording.send-notification', $result->id) }}">
                                            @csrf
                                            <div class="modal-body">
                                                <p>The following email may be sent to the client:</p>

                                                <div class="email-preview p-3 mb-3"
                                                    style="background-color: #f8f9fa; border-radius: 5px;">
                                                    <p><strong>Subject:</strong> You have new test results and new random
                                                        selections from Skyros Drug Checks Inc</p>

                                                    <p>Hello {{ $result->employee->first_name }}
                                                        {{ $result->employee->last_name }},</p>

                                                    <p>Skyros Drug Checks Inc has added new test results to your company
                                                        portal.
                                                    </p>

                                                    <p> <a href="{{ route('result-recording.index') }}">Click here</a> to
                                                        view
                                                        all results for
                                                        <strong>{{ $result->clientProfile->company_name }}</strong>.
                                                    </p>

                                                    <p>Also:</p>

                                                    <p>Skyros Drug Checks Inc has added new random selections to your
                                                        company
                                                        portal.</p>

                                                    <p><a href="{{ route('result-recording.index') }}">Click here</a> to
                                                        view
                                                        all selections for
                                                        <strong>{{ $result->clientProfile->company_name }}</strong>.
                                                    </p>
                                                </div>

                                                <div class="form-group">
                                                    <label for="additionalText{{ $result->id }}">Add text you would like
                                                        to
                                                        include in the email</label>
                                                    <textarea name="additional_text" class="form-control" id="additionalText{{ $result->id }}" rows="3"></textarea>
                                                </div>

                                                <div class="text-muted small">
                                                    (No footer text has been configured to append all client notification
                                                    emails)
                                                </div>

                                                <hr>

                                                <div class="client-info">
                                                    <p><strong>Client:</strong> {{ $result->clientProfile->company_name }}
                                                        <strong>Phone:</strong>
                                                        {{ $result->clientProfile->phone ?? 'N/A' }}
                                                    </p>
                                                    <p><strong>Date:</strong>
                                                        {{ \Carbon\Carbon::parse($result->collection_datetime)->format('m/d/Y') }}
                                                    </p>
                                                    <p><strong>DER Contact:</strong>
                                                        {{ $result->clientProfile->der_contact_name ?? 'N/A' }}
                                                        <strong>Email:</strong>
                                                        {{ $result->clientProfile->der_contact_email ?? 'N/A' }}
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-primary">Send Notification</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </tbody>
                </table>

            </div>
        </div>
    </div>
    <!-- end row -->
@endsection


