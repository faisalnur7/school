<form method="GET" action="{{ route('post-office.index') }}">
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

        <!-- Police Station Filter -->
        <div class="flex flex-col w-48">
            <label for="police_station_id" class="form-label">Police Station</label>
            <select name="police_station_id" id="police_station_id" class="form-control select2">
                <option value="">All Police Stations</option>
                @foreach ($stations as $station)
                    <option value="{{ $station->id }}"
                        {{ request('police_station_id') == $station->id ? 'selected' : '' }}>
                        {{ $station->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Name Search -->
        <div class="flex flex-col w-48">
            <label for="name" class="form-label">Search by Name</label>
            <input type="text" name="name" id="name" class="form-control" value="{{ request('name') }}"
                placeholder="Post Office Name">
        </div>

        <!-- Per Page -->
        <div>
            <label for="per_page" class="form-label">Results Per Page</label>
            <select name="per_page" id="per_page" class="form-control" onchange="this.form.submit()">
                @foreach ([10, 25, 50, 100, 200] as $size)
                    <option value="{{ $size }}" {{ request('per_page', 10) == $size ? 'selected' : '' }}>
                        {{ $size }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Buttons -->
        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-filter"></i>
            </button>
            <a href="{{ route('post-office.index') }}" class="btn btn-secondary">
                <i class="fas fa-undo"></i>
            </a>
        </div>
    </div>
</form>
