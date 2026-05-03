<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label for="building_id">Building</label>
            <select id="building_id" name="building_id" class="form-control @error('building_id') is-invalid @enderror" required>
                <option value="">Select Building</option>
                @foreach($buildings as $buildingOption)
                    <option value="{{ $buildingOption->id }}" {{ (string) old('building_id', $room->building_id ?? '') === (string) $buildingOption->id ? 'selected' : '' }}>
                        {{ $buildingOption->name }}
                    </option>
                @endforeach
            </select>
            @error('building_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="department_id">Department</label>
            <select id="department_id" name="department_id" class="form-control @error('department_id') is-invalid @enderror">
                <option value="">No Department</option>
                @foreach($departments as $departmentOption)
                    <option value="{{ $departmentOption->id }}" {{ (string) old('department_id', $room->department_id ?? '') === (string) $departmentOption->id ? 'selected' : '' }}>
                        {{ $departmentOption->name }}
                    </option>
                @endforeach
            </select>
            @error('department_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label for="name">Name</label>
            <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $room->name ?? '') }}" required>
            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="code">Code</label>
            <input type="text" id="code" name="code" class="form-control @error('code') is-invalid @enderror" value="{{ old('code', $room->code ?? '') }}" required>
            @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="form-group">
            <label for="floor_number">Floor Number</label>
            <input type="number" id="floor_number" name="floor_number" class="form-control @error('floor_number') is-invalid @enderror" value="{{ old('floor_number', $room->floor_number ?? 0) }}" min="0" required>
            @error('floor_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label for="room_type">Room Type</label>
            <select id="room_type" name="room_type" class="form-control @error('room_type') is-invalid @enderror" required>
                @foreach($roomTypes as $roomType)
                    <option value="{{ $roomType->value }}" {{ old('room_type', isset($room) && $room->room_type ? $room->room_type->value : '') === $roomType->value ? 'selected' : '' }}>
                        {{ ucfirst($roomType->value) }}
                    </option>
                @endforeach
            </select>
            @error('room_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label for="seating_capacity">Seating Capacity</label>
            <input type="number" id="seating_capacity" name="seating_capacity" class="form-control @error('seating_capacity') is-invalid @enderror" value="{{ old('seating_capacity', $room->seating_capacity ?? '') }}" min="0">
            @error('seating_capacity')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>
</div>

<div class="form-check mb-3">
    <input type="checkbox" id="is_active" name="is_active" value="1" class="form-check-input" {{ old('is_active', $room->is_active ?? true) ? 'checked' : '' }}>
    <label class="form-check-label" for="is_active">Active</label>
</div>
