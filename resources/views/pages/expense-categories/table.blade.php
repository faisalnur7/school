<div class="card">
    <div class="card-header text-white rounded-top d-flex justify-content-between align-items-center shadow p-3">
        <h3 class="card-title mb-0 text-white text-lg">Expense Categories</h3>
        <a href="{{ route('expense-categories.create') }}" class="btn btn-primary btn-sm ml-auto text-bold">
            + Add Expense Category
        </a>
    </div>

    <div class="card-body px-0 pb-4 pt-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Slug</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th width="120">Action</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($categories as $category)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $category->name }}</td>
                            <td><code>{{ $category->slug }}</code></td>
                            <td>{{ Str::limit($category->description, 40) }}</td>

                            <td>
                                <span class="badge badge-{{ $category->is_active ? 'success' : 'secondary' }}">
                                    {{ $category->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>

                            <td style="display: flex; justify-content: center; align-items: self-start; gap: 5px;">
                                <a href="{{ route('expense-categories.edit', $category->id) }}"
                                    class="btn btn-sm btn-dark">
                                    <i class="fas fa-edit"></i>
                                </a>

                                <form action="{{ route('expense-categories.destroy', $category->id) }}" method="POST"
                                    class="btn btn-sm btn-danger d-inline m-0"
                                    onsubmit="return confirm('Delete this expense category?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" style="background:none;border:none;padding:0;color:inherit;">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach

                    @if ($categories->isEmpty())
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">No expense categories found</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <div class="px-3 pt-3">
            {{ $categories->links() }}
        </div>
    </div>
</div>
