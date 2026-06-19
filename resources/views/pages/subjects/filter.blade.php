<form method="GET" action="{{ route('subjects.index') }}" class="mb-3">
    <div class="row">
        <div class="col-md-3">
            <div class="form-group">
                <input type="text" name="search" class="form-control" placeholder="Search subjects..." 
                    value="{{ request('search') }}">
            </div>
        </div>
        <div class="col-md-3">
            <select name="school_class_id" id="school_class_id" class="form-control">
                <option value="">All Classes</option>
                @foreach ($classes as $classId => $class)
                    @php
                        $optionValue = is_object($class) ? $class->id : $classId;
                        $optionLabel = is_object($class)
                            ? ($class->name ?? $class->name_en ?? $class->title ?? $class->class_name ?? 'Unnamed Class')
                            : $class;
                    @endphp
                    <option value="{{ $optionValue }}" {{ (string) request('school_class_id') === (string) $optionValue ? 'selected' : '' }}>
                        {{ $optionLabel }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <select name="group_id" id="group_id" class="form-control">
                <option value="">All Groups</option>
                @foreach ($groups as $groupId => $group)
                    @php
                        $groupOptionValue = is_object($group) ? $group->id : $groupId;
                        $groupOptionLabel = is_object($group)
                            ? ($group->name ?? $group->name_en ?? $group->title ?? 'Unnamed Group')
                            : $group;
                    @endphp
                    <option value="{{ $groupOptionValue }}" {{ (string) request('group_id') === (string) $groupOptionValue ? 'selected' : '' }}>
                        {{ $groupOptionLabel }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                <select name="type" class="form-control">
                    <option value="">All Types</option>
                    <option value="mandatory" {{ request('type') == 'mandatory' ? 'selected' : '' }}>Mandatory</option>
                    <option value="optional" {{ request('type') == 'optional' ? 'selected' : '' }}>Optional</option>
                </select>
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                <select name="is_active" class="form-control">
                    <option value="">All Status</option>
                    <option value="1" {{ request('is_active') == '1' ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ request('is_active') == '0' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                <button type="submit" class="btn btn-primary btn-block" title="Filter" aria-label="Filter">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                <a href="{{ route('subjects.index') }}" class="btn btn-secondary btn-block" title="Reset" aria-label="Reset">
                    <i class="fas fa-undo-alt"></i>
                </a>
            </div>
        </div>
    </div>
</form>
