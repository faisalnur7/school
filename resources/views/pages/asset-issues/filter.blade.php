<!-- Filter Section -->
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <label class="form-label">Asset Category *</label>
        <select id="asset_category_id" name="asset_category_id" class="form-control" required>
            <option value="">Select Asset Category</option>
            @foreach($assetCategories as $category)
                <option value="{{ $category->id }}" {{ request('asset_category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
            @endforeach
        </select>
    </div>

    <div class="col-md-2 mb-3">
        <label class="form-label">Department</label>
        <select id="department_id" name="department_id" class="form-control">
            <option value="">All Department</option>
            @foreach($departments as $department)
                <option value="{{ $department->id }}" {{ request('department_id') == $department->id ? 'selected' : '' }}>{{ $department->name }}</option>
            @endforeach
        </select>
    </div>


    <div class="col-md-1">
        <label class="form-label">&nbsp;</label>
        @isset($isIndex)
            <button type="submit" class="btn btn-primary btn-block">
                <i class="fas fa-search"></i>
            </button>
        @else
            <button type="button" id="loadAssets" class="btn btn-primary btn-block">
                <i class="fas fa-search"></i>
            </button>
        @endisset
    </div>
</div>
