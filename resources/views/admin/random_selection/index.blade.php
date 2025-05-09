@extends('layouts.admin.master')

@section('content')
    <div class="row">
        <div class="col-xl-12 box-margin height-card">
            <div class="card card-body">
                <table id="basic-datatable" class="table table-striped dt-responsive w-100">
                    <thead>
                        <tr>
                            <th>Protocol Name</th>
                            <th>Client</th>
                            <th>Test Type</th>
                            <th>Group</th>
                            <th>Frequency</th>
                            <th>Status</th>
                            <th class="custom-width-action">{{ __('content.action') }}</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($protocols as $protocol)
                            <tr>
                                <td>{{ $protocol->name }}</td>
                                <td>{{ $protocol->client->company_name }}</td>
                                <td>{{ $protocol->test->test_name }}</td>
                                <td>
                                    @switch($protocol->group)
                                        @case('DOT')
                                            <span class="badge badge-pill badge-primary">DOT</span>
                                        @break

                                        @case('NON_DOT')
                                            <span class="badge badge-pill badge-secondary">Non-DOT</span>
                                        @break

                                        @case('DOT_AGENCY')
                                            <span class="badge badge-pill badge-info">DOT Agency:
                                                {{ $protocol->dotAgency->name ?? 'N/A' }}</span>
                                        @break

                                        @default
                                            <span class="badge badge-pill badge-dark">All Employees</span>
                                    @endswitch
                                </td>
                                <td>
                                    @switch($protocol->selection_period)
                                        @case('YEARLY')
                                            Yearly ({{ $protocol->selection_requirement_value }}
                                            {{ $protocol->selection_requirement_type === 'PERCENTAGE' ? '%' : 'employees' }})
                                        @break

                                        @case('QUARTERLY')
                                            Quarterly ({{ $protocol->selection_requirement_value }}
                                            {{ $protocol->selection_requirement_type === 'PERCENTAGE' ? '%' : 'employees' }})
                                        @break

                                        @case('MONTHLY')
                                            Monthly (Day {{ $protocol->monthly_selection_day }})
                                        @break

                                        @default
                                            Manual Dates
                                    @endswitch
                                </td>
                                <td>
                                    @if ($protocol->is_active)
                                        <span class="badge badge-pill badge-success">Active</span>
                                    @else
                                        <span class="badge badge-pill badge-danger">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <div>
                                        <a href="{{ route('random-selection.edit', $protocol->id) }}" class="mr-2">
                                            <i class="fa fa-edit text-info font-18"></i>
                                        </a>

                                        <a href="#" data-toggle="modal"
                                            data-target="#executeModal{{ $protocol->id }}" class="mr-2">
                                            <i class="fa fa-play text-success font-18" title="Execute Protocol"></i>
                                        </a>

                                        <a href="{{ route('random-selection.executions', $protocol->id) }}" class="mr-2">
                                            <i class="fa fa-history text-primary font-18"
                                                title="View Execution History"></i>
                                        </a>

                                        <a href="#" data-toggle="modal"
                                            data-target="#deleteModal{{ $protocol->id }}">
                                            <i class="fa fa-trash text-danger font-18"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>

                            <!-- Execute Modal -->
                            <div class="modal fade" id="executeModal{{ $protocol->id }}" tabindex="-1" role="dialog">
                                <div class="modal-dialog modal-dialog-centered" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Execute Protocol</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <p>Are you sure you want to execute this protocol now?</p>
                                            <p><strong>{{ $protocol->name }}</strong></p>
                                        </div>
                                        <div class="modal-footer">
                                            <form method="POST"
                                                action="{{ route('random-selection.execute', $protocol->id) }}">
                                                @csrf
                                                <button type="button" class="btn btn-secondary"
                                                    data-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-primary">Execute Now</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Delete Modal -->
                            <div class="modal fade" id="deleteModal{{ $protocol->id }}" tabindex="-1" role="dialog">
                                <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">{{ __('content.delete') }}</h5>
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
                                                @include('admin.demo_mode.demo-mode')
                                            @else
                                                <form class="d-inline-block"
                                                    action="{{ route('random-selection.destroy', $protocol->id) }}"
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
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
