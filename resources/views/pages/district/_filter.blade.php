<form method="GET" action="{{ route('district.index') }}">
    <div class="d-flex flex-wrap align-items-end gap-3">
        <!-- Division Filter -->
        <div class="flex flex-col w-48">
            <label for="division_id" class="form-label">Filter by Division</label>
            <select name="division_id" id="division_id" class="form-control">
                <option value="">All Divisions</option>
                @foreach ($divisions as $division)
                    <option value="{{ $division->id }}" {{ request('division_id') == $division->id ? 'selected' : '' }}>
                        {{ $division->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Per Page -->
        <div class="flex flex-col w-40">
            <label for="per_page" class="form-label">Per Page</label>
            <select name="per_page" id="per_page" class="form-control" onchange="this.form.submit()">
                @foreach ([10, 25, 50, 100] as $value)
                    <option value="{{ $value }}" {{ request('per_page', 10) == $value ? 'selected' : '' }}>
                        {{ $value }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Submit Button -->
        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary shadow-sm">
                <i class="fas fa-filter"></i>
            </button>
            <a href="{{ route('district.index') }}" class="btn btn-secondary">
                <i class="fas fa-undo"></i>
            </a>
        </div>
    </div>
</form>
