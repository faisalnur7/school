<form method="GET" action="{{ route('subjects.index') }}" class="mb-3">
    <div class="row">
        <div class="col-md-3">
            <div class="form-group">
                <input type="text" name="search" class="form-control" placeholder="Search subjects..." 
                    value="{{ request('search') }}">
            </div>
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
                <button type="submit" class="btn btn-primary btn-block">
                    <i class="fas fa-search"></i> Filter
                </button>
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                <a href="{{ route('subjects.index') }}" class="btn btn-secondary btn-block">
                    <i class="fas fa-reset"></i> Reset
                </a>
            </div>
        </div>
    </div>
</form>