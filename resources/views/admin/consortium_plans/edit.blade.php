@extends('layouts.admin.master')

@section('content')
    <div class="row">
        <div class="col-xl-12 box-margin height-card">
            <div class="card card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="card-title mb-0">Edit Consortium Pricing Plan</h4>
                    <a href="{{ route('admin.consortium-plans.index') }}" class="btn btn-sm btn-secondary">
                        <i class="fa fa-arrow-left"></i> Back to Plans
                    </a>
                </div>

                @if ($demo_mode == 'on')
                    @include('admin.demo_mode.demo-mode')
                @else
                    <form id="plan-form" action="{{ route('admin.consortium-plans.update', $plan->id) }}" method="POST">
                        @method('PUT')
                        @csrf
                @endif

                <div class="row">
                    <!-- Section 1: Plan Details -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="name">Plan Name *</label>
                            <input type="text" name="name" id="name" class="form-control"
                                value="{{ old('name', $plan->name) }}" required>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="slug">Slug *</label>
                            <input type="text" name="slug" id="slug" class="form-control"
                                value="{{ old('slug', $plan->slug) }}" required>
                            <small class="form-text text-muted">URL-friendly unique identifier.</small>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea name="description" id="description" class="form-control" rows="2">{{ old('description', $plan->description) }}</textarea>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="min_drivers">Min Drivers</label>
                            <input type="number" name="min_drivers" id="min_drivers" class="form-control" min="1"
                                value="{{ old('min_drivers', $plan->min_drivers) }}">
                            <small class="form-text text-muted">Empty or 1 for no min limit.</small>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="max_drivers">Max Drivers</label>
                            <input type="number" name="max_drivers" id="max_drivers" class="form-control" min="1"
                                value="{{ old('max_drivers', $plan->max_drivers) }}">
                            <small class="form-text text-muted">Leave blank for unlimited.</small>
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="display_order">Display Order *</label>
                            <input type="number" name="display_order" id="display_order" class="form-control"
                                min="0" value="{{ old('display_order', $plan->display_order) }}" required>
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="is_active">Status *</label>
                            <select name="is_active" id="is_active" class="form-control" required>
                                <option value="1" {{ $plan->is_active ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ !$plan->is_active ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>

                <hr class="my-4">

                <!-- Section 2: Plan Fees Manager -->
                <div class="mb-3">
                    <h5 class="fw-bold"><i class="fa fa-money me-2"></i>Fee Management</h5>
                    <p class="text-muted small">Configure the fees associated with this plan. You can choose whether they
                        are flat fees or charged per driver.</p>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered" id="fees-table">
                        <thead>
                            <tr class="bg-light">
                                <th>Fee Label *</th>
                                <th>Fee Key *</th>
                                <th>Amount ($) *</th>
                                <th>Type *</th>
                                <th>Sort Order</th>
                                <th class="text-center" style="width: 80px;">Action</th>
                            </tr>
                        </thead>
                        <tbody id="fees-tbody">
                            <!-- Rows injected by JS -->
                        </tbody>
                    </table>
                </div>

                <div class="mb-4">
                    <button type="button" class="btn btn-sm btn-success" id="add-fee-btn">
                        <i class="fa fa-plus"></i> Add Custom Fee Row
                    </button>
                </div>

                <!-- Section 3: Live Preview Panel -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="alert alert-secondary p-4">
                            <h5 class="fw-bold mb-3"><i class="fa fa-calculator me-2"></i>Live Calculation Preview</h5>
                            <div class="row">
                                <div class="col-md-4">
                                    <strong>Flat Fees Subtotal:</strong>
                                    <div class="h4 font-weight-bold text-primary mt-1" id="preview-flat">$0.00</div>
                                </div>
                                <div class="col-md-4">
                                    <strong>Per-Driver Fee rate:</strong>
                                    <div class="h4 font-weight-bold text-info mt-1" id="preview-per-driver">$0.00 / driver</div>
                                </div>
                                <div class="col-md-4">
                                    <strong>Total Previews:</strong>
                                    <div class="mt-1 small">
                                        <div>1 Driver: <span class="font-weight-bold text-success" id="preview-1">$0.00</span></div>
                                        <div>5 Drivers: <span class="font-weight-bold text-success" id="preview-5">$0.00</span></div>
                                        <div>10 Drivers: <span class="font-weight-bold text-success" id="preview-10">$0.00</span></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary mr-2">Update Consortium Plan</button>
                        <a href="{{ route('admin.consortium-plans.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </div>
                </form>
            </div>
        </div>
    </div>

    @php
        $feesData = $plan->fees->map(function ($f) {
            return [
                'fee_label'     => $f->fee_label,
                'fee_key'       => $f->fee_key,
                'fee_amount'    => $f->fee_amount_in_dollars,
                'fee_type'      => $f->fee_type,
                'display_order' => $f->display_order,
            ];
        })->values()->toArray();
    @endphp

    <script>
        // ─── Existing fee data from server ──────────────────────────────────────
        var fees = @json($feesData);

        // ─── Helpers ────────────────────────────────────────────────────────────
        function formatCurrency(val) {
            return '$' + parseFloat(val || 0).toFixed(2);
        }

        function generateSlug(str) {
            return str.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)+/g, '');
        }

        function sanitizeKey(str) {
            return str.toLowerCase().replace(/[^a-z_]+/g, '');
        }

        function escHtml(str) {
            return String(str).replace(/&/g, '&amp;').replace(/"/g, '&quot;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
        }

        // ─── Render fee rows ────────────────────────────────────────────────────
        function renderFees() {
            var tbody = document.getElementById('fees-tbody');
            tbody.innerHTML = '';

            fees.forEach(function (fee, index) {
                var tr = document.createElement('tr');
                tr.innerHTML =
                    '<td><input type="text" name="fees[' + index + '][fee_label]" class="form-control fee-label" value="' + escHtml(fee.fee_label) + '" required placeholder="e.g. Annual Enrollment Fee"></td>' +
                    '<td><input type="text" name="fees[' + index + '][fee_key]" class="form-control fee-key" value="' + escHtml(fee.fee_key) + '" required placeholder="e.g. annual_enrollment_fee"></td>' +
                    '<td><input type="number" name="fees[' + index + '][fee_amount]" class="form-control fee-amount" value="' + fee.fee_amount + '" step="0.01" min="0" required placeholder="0.00"></td>' +
                    '<td>' +
                        '<select name="fees[' + index + '][fee_type]" class="form-control fee-type" required>' +
                            '<option value="flat"' + (fee.fee_type === 'flat' ? ' selected' : '') + '>Flat Fee</option>' +
                            '<option value="per_driver"' + (fee.fee_type === 'per_driver' ? ' selected' : '') + '>Per Driver Fee</option>' +
                        '</select>' +
                    '</td>' +
                    '<td><input type="number" name="fees[' + index + '][display_order]" class="form-control fee-order" value="' + fee.display_order + '" required></td>' +
                    '<td class="text-center"><button type="button" class="btn btn-sm btn-danger remove-fee-btn" data-index="' + index + '"><i class="fa fa-times"></i></button></td>';
                tbody.appendChild(tr);
            });

            // Attach per-row event listeners
            tbody.querySelectorAll('.fee-label').forEach(function (el, i) {
                el.addEventListener('input', function () { fees[i].fee_label = this.value; updatePreview(); });
            });
            tbody.querySelectorAll('.fee-key').forEach(function (el, i) {
                el.addEventListener('input', function () { fees[i].fee_key = sanitizeKey(this.value); this.value = fees[i].fee_key; });
            });
            tbody.querySelectorAll('.fee-amount').forEach(function (el, i) {
                el.addEventListener('input', function () { fees[i].fee_amount = parseFloat(this.value) || 0; updatePreview(); });
            });
            tbody.querySelectorAll('.fee-type').forEach(function (el, i) {
                el.addEventListener('change', function () { fees[i].fee_type = this.value; updatePreview(); });
            });
            tbody.querySelectorAll('.fee-order').forEach(function (el, i) {
                el.addEventListener('input', function () { fees[i].display_order = parseInt(this.value) || 0; });
            });
            tbody.querySelectorAll('.remove-fee-btn').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    fees.splice(parseInt(this.dataset.index), 1);
                    renderFees();
                    updatePreview();
                });
            });

            updatePreview();
        }

        // ─── Live preview ───────────────────────────────────────────────────────
        function calculateSubtotal(type) {
            return fees.filter(function (f) { return f.fee_type === type; })
                       .reduce(function (sum, f) { return sum + (parseFloat(f.fee_amount) || 0); }, 0);
        }

        function calculateTotal(driverCount) {
            return calculateSubtotal('flat') + calculateSubtotal('per_driver') * driverCount;
        }

        function updatePreview() {
            document.getElementById('preview-flat').textContent = formatCurrency(calculateSubtotal('flat'));
            document.getElementById('preview-per-driver').textContent = formatCurrency(calculateSubtotal('per_driver')) + ' / driver';
            document.getElementById('preview-1').textContent = formatCurrency(calculateTotal(1));
            document.getElementById('preview-5').textContent = formatCurrency(calculateTotal(5));
            document.getElementById('preview-10').textContent = formatCurrency(calculateTotal(10));
        }

        // ─── Slug auto-generation from name field ───────────────────────────────
        document.getElementById('name').addEventListener('input', function () {
            document.getElementById('slug').value = generateSlug(this.value);
        });

        // ─── Add fee button ─────────────────────────────────────────────────────
        document.getElementById('add-fee-btn').addEventListener('click', function () {
            fees.push({ fee_label: '', fee_key: '', fee_amount: 0.00, fee_type: 'flat', display_order: fees.length + 1 });
            renderFees();
        });

        // ─── Boot on DOM ready ──────────────────────────────────────────────────
        document.addEventListener('DOMContentLoaded', function () {
            renderFees();
        });
    </script>
@endsection
