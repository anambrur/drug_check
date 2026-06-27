@extends('layouts.admin.master')

@section('content')
    <div class="row">
        <div class="col-12 box-margin">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-20">
                        <h6 class="card-title mb-0">Consortium Pricing Plans</h6>
                        <div>
                            <a href="{{ route('admin.consortium-plans.create') }}" class="btn btn-sm btn-primary">
                                <i class="fa fa-plus"></i> Add New Plan
                            </a>
                            <a href="{{ route('admin.consortium-plans.trashed') }}" class="btn btn-sm btn-warning">
                                <i class="fa fa-archive"></i> Trashed Plans
                            </a>
                        </div>
                    </div>

                    @if (count($plans) > 0)
                        <table id="basic-datatable" class="table table-striped dt-responsive w-100">
                            <thead>
                                <tr>
                                    <th>Order</th>
                                    <th>Plan Name</th>
                                    <th>Driver Range</th>
                                    <th>Flat Fees Total</th>
                                    <th>Per-Driver Fee</th>
                                    <th>Status</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($plans as $plan)
                                    <tr>
                                        <td>{{ $plan->display_order }}</td>
                                        <td>
                                            <div class="font-weight-bold">{{ $plan->name }}</div>
                                            @if ($plan->description)
                                                <small class="text-muted">{{ Str::limit($plan->description, 60) }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($plan->min_drivers === $plan->max_drivers)
                                                {{ $plan->min_drivers }} driver(s) only
                                            @elseif ($plan->max_drivers === null)
                                                {{ $plan->min_drivers }}+ drivers
                                            @else
                                                {{ $plan->min_drivers }} to {{ $plan->max_drivers }} drivers
                                            @endif
                                        </td>
                                        <td class="font-weight-bold">
                                            ${{ number_format($plan->fees->where('fee_type', 'flat')->sum('fee_amount_in_dollars'), 2) }}
                                        </td>
                                        <td class="font-weight-bold text-info">
                                            ${{ number_format($plan->fees->where('fee_type', 'per_driver')->sum('fee_amount_in_dollars'), 2) }}
                                        </td>
                                        <td>
                                            <form action="{{ route('admin.consortium-plans.toggle-status', $plan->id) }}" method="POST" style="display:inline;">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-xs {{ $plan->is_active ? 'btn-success' : 'btn-secondary' }}">
                                                    {{ $plan->is_active ? 'Active' : 'Inactive' }}
                                                </button>
                                            </form>
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('admin.consortium-plans.edit', ['id' => $plan->id]) }}"
                                                class="btn btn-sm btn-info mr-1">
                                                <i class="fa fa-edit"></i> Edit
                                            </a>
                                            <form action="{{ route('admin.consortium-plans.destroy', ['id' => $plan->id]) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete/archive this plan?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i class="fa fa-trash"></i> Delete
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="text-center py-5">
                            <i class="fa fa-info-circle fa-3x text-muted mb-3"></i>
                            <h5>No consortium pricing plans found</h5>
                            <p class="text-muted">Click "Add New Plan" to configure dynamic pricing plans.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
