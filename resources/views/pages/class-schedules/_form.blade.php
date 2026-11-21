@php($schedule = $schedule ?? null)
<div class="row">
    <div class="col-md-6 mb-3">
        <label for="class-schedules--form-name" class="font-weight-bold">Schedule Name <span class="text-danger">*</span></label>
        <input id="class-schedules--form-name" type="text" name="name" class="form-control" value="{{ old('name', $schedule?->name) }}" placeholder="e.g. 1st Period" required>
    </div>
    <div class="col-md-6 mb-3">
        <label for="class-schedules--form-kind" class="font-weight-bold">Schedule Type <span class="text-danger">*</span></label>
        <select id="class-schedules--form-kind" name="kind" class="form-control" required>
            @foreach(['teaching' => 'Class Period', 'assembly' => 'Assembly', 'tiffin' => 'Tiffin Time', 'prayer' => 'Prayer Time'] as $value => $label)
                <option value="{{ $value }}" @selected(old('kind', $schedule?->kind ?? 'teaching') === $value)>{{ $label }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4 mb-3">
        <label for="class-schedules--form-start-time" class="font-weight-bold">Start Time <span class="text-danger">*</span></label>
        <input id="class-schedules--form-start-time" type="time" name="start_time" class="form-control" value="{{ old('start_time', $schedule?->start_time ? substr($schedule->start_time, 0, 5) : '') }}" required>
    </div>
    <div class="col-md-4 mb-3">
        <label for="class-schedules--form-end-time" class="font-weight-bold">End Time <span class="text-danger">*</span></label>
        <input id="class-schedules--form-end-time" type="time" name="end_time" class="form-control" value="{{ old('end_time', $schedule?->end_time ? substr($schedule->end_time, 0, 5) : '') }}" required>
    </div>
    <div class="col-md-4 mb-3">
        <label for="class-schedules--form-sort-order" class="font-weight-bold">Display Order <span class="text-danger">*</span></label>
        <input id="class-schedules--form-sort-order" type="number" name="sort_order" class="form-control" min="1" value="{{ old('sort_order', $schedule?->sort_order ?? 1) }}" required>
    </div>
    <div class="col-12 mb-2">
        <div class="custom-control custom-checkbox">
            <input type="checkbox" name="is_active" value="1" class="custom-control-input" id="schedule-active" @checked(old('is_active', $schedule?->is_active ?? true))>
            <label class="custom-control-label" for="schedule-active">Active</label>
        </div>
    </div>
</div>
