@extends('layouts.admin.master')

@section('content')
    <div class="row">
        <div class="col-12 box-margin">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-20">
                        <h6 class="card-title mb-0">Archived Consortium Pricing Plans</h6>
                        <a href="{{ route('admin.consortium-plans.index') }}" class="btn btn-sm btn-secondary">
                            <i class="fa fa-arrow-left"></i> Back to Plans
                        </a>
                    </div>

                    @if (count($plans) > 0)
                        <table id="basic-datatable" class="table table-striped dt-responsive w-100">
                            <thead>
                                <tr>
                                    <th>Plan Name</th>
                                    <th>Driver Range</th>
                                    <th>Archived Date</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($plans as $plan)
                                    <tr>
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
                                        <td>{{ $plan->deleted_at->format('Y-m-d H:i') }}</td>
                                        <td class="text-center">
                                            <form action="{{ route('admin.consortium-plans.restore', $plan->id) }}" method="POST" style="display:inline;">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-sm btn-success">
                                                    <i class="fa fa-undo"></i> Restore
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="text-center py-5">
                            <i class="fa fa-archive fa-3x text-muted mb-3"></i>
                            <h5>No archived plans found</h5>
                            <p class="text-muted">Only soft-deleted pricing plans will list here.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
