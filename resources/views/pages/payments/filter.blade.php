<!-- Filter Section -->
<div class="card mb-3">
    <div class="card-header bg-light">
        <h5 class="mb-0 text-white">
            <i class="fas fa-filter"></i> Filter Payments
            <button class="btn btn-sm btn-link float-right filter_button" type="button">
                <i class="fas fa-chevron-down"></i>
            </button>
        </h5>
    </div>
    <div class="" id="filterCollapse">
        <div class="card-body">
            <form action="{{ route('payments.index') }}" method="GET">
                <div class="row">

                   <!-- From Date -->
                    <div class="col-md-3 mb-3">
                        <label class="form-label">From Date</label>
                        <input type="date" name="from" class="form-control form-control-sm"
                            value="{{ isset($from) ? \Carbon\Carbon::parse($from)->format('Y-m-d') : '' }}"
                            style="text-transform:uppercase"
                            onchange="formatDate(this)">
                    </div>

                    <!-- To Date -->
                    <div class="col-md-3 mb-3">
                        <label class="form-label">To Date</label>
                        <input type="date" name="to" class="form-control form-control-sm"
                            value="{{ isset($to) ? \Carbon\Carbon::parse($to)->format('Y-m-d') : '' }}"
                            style="text-transform:uppercase"
                            onchange="formatDate(this)">
                    </div>

                    <!-- Status -->
                    <div class="col-md-2 mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-control form-control-sm">
                            <option value="">All Status</option>
                            <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>

                    <!-- Action Buttons -->
                    <div class="col-md-3 mb-3 d-flex flex-column flex-sm-row align-items-stretch align-items-sm-center gap-2">
                        <button type="submit" class="btn btn-primary btn-sm w-100 w-sm-auto">
                            <i class="fas fa-search"></i> Filter
                        </button>
                        <a href="{{ route('students.index') }}" class="btn btn-secondary btn-sm w-100 w-sm-auto">
                            <i class="fas fa-redo"></i> Reset
                        </a>
                    </div>

                </div>
            </form>
        </div>
    </div>
</div>
