<form method="GET" action="{{ route('police-station.index') }}">
    <div class="d-flex flex-wrap align-items-end gap-3">
        <!-- Division Filter -->
        <div class="flex flex-col w-48">
            <label for="division_id" class="form-label">Division</label>
            <select name="division_id" id="division_id" class="form-control select2">
                <option value="">All Divisions</option>
                @foreach ($divisions as $division)
                    <option value="{{ $division->id }}" {{ request('division_id') == $division->id ? 'selected' : '' }}>
                        {{ $division->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- District Filter -->
        <div class="flex flex-col w-48">
            <label for="district_id" class="form-label">District</label>
            <select name="district_id" id="district_id" class="form-control select2">
                <option value="">All Districts</option>
                @foreach ($districts as $district)
                    <option value="{{ $district->id }}" {{ request('district_id') == $district->id ? 'selected' : '' }}>
                        {{ $district->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Name Search -->
        <div class="flex flex-col w-48">
            <label for="name" class="form-label">Search Name</label>
            <input type="text" name="name" id="name" class="form-control"
                   value="{{ request('name') }}" placeholder="Station Name">
        </div>

        <!-- Per Page -->
        <div class="flex flex-col w-40">
            <label for="per_page" class="form-label">Per Page</label>
            <select name="per_page" id="per_page" class="form-control" onchange="this.form.submit()">
                @foreach ([10, 25, 50, 100] as $count)
                    <option value="{{ $count }}" {{ request('per_page', 10) == $count ? 'selected' : '' }}>
                        {{ $count }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Buttons -->
        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary shadow-sm">
                <i class="fas fa-filter"></i>
            </button>
            <a href="{{ route('police-station.index') }}" class="btn btn-secondary shadow-sm">
                <i class="fas fa-undo"></i>
            </a>
        </div>
    </div>
</form>
