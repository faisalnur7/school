<form method="GET" action="{{ route('subjects.index') }}" class="subject-filter-form mb-3">
    <input type="hidden" name="view" value="{{ request('view', 'classwise') }}">
    <div class="subject-filter-field subject-filter-search">
        <input type="text" name="search" class="form-control" placeholder="Search subjects..."
            value="{{ request('search') }}">
    </div>
    <div class="subject-filter-field subject-filter-select">
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
    <div class="subject-filter-field subject-filter-select">
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
    <div class="subject-filter-field subject-filter-select">
        <select name="type" class="form-control">
            <option value="">All Types</option>
            <option value="mandatory" {{ request('type') == 'mandatory' ? 'selected' : '' }}>Mandatory</option>
            <option value="optional" {{ request('type') == 'optional' ? 'selected' : '' }}>Optional</option>
        </select>
    </div>
    <div class="subject-filter-field subject-filter-select">
        <select name="is_active" class="form-control">
            <option value="">All Status</option>
            <option value="1" {{ request('is_active') == '1' ? 'selected' : '' }}>Active</option>
            <option value="0" {{ request('is_active') == '0' ? 'selected' : '' }}>Inactive</option>
        </select>
    </div>
    <button type="submit" class="btn btn-primary subject-filter-action" title="Filter" aria-label="Filter">
        <i class="fas fa-search"></i>
    </button>
    <a href="{{ route('subjects.index') }}" class="btn btn-secondary subject-filter-action" title="Reset" aria-label="Reset">
        <i class="fas fa-undo-alt"></i>
    </a>
</form>

<style>
    .subject-filter-form {
        display: flex;
        align-items: center;
        flex-wrap: nowrap;
        gap: 0.85rem;
        padding: 0.9rem;
        border: 1px solid #e2e8f0;
        border-radius: 1rem;
        background: #fff;
    }

    .subject-filter-field {
        min-width: 0;
    }

    .subject-filter-search {
        flex: 1.35 1 0;
    }

    .subject-filter-select {
        flex: 1 1 0;
    }

    .subject-filter-form .form-control,
    .subject-filter-action {
        height: 42px;
        border-radius: 0.7rem;
    }

    .subject-filter-form .form-control {
        border-color: #dbe2ea;
        padding-left: 0.8rem;
        padding-right: 0.8rem;
    }

    .subject-filter-action {
        flex: 0 0 auto;
        min-width: 42px;
        padding-right: 0.9rem;
        padding-left: 0.9rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 0.7rem;
        font-size: 1rem;
    }

    .subject-filter-form .btn-secondary {
        background: #fff;
        border-color: #cbd5e1;
        color: #334155;
    }

    @media (max-width: 991.98px) {
        .subject-filter-form {
            flex-wrap: wrap;
        }

        .subject-filter-field {
            flex: 1 1 calc(50% - 0.85rem);
        }

        .subject-filter-action {
            flex: 1 1 calc(50% - 0.85rem);
        }
    }

    @media (max-width: 575.98px) {
        .subject-filter-form {
            gap: 0.6rem;
        }

        .subject-filter-field,
        .subject-filter-action {
            flex-basis: 100%;
        }
    }
</style>
