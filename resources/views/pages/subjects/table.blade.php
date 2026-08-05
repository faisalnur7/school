<div class="table-responsive">
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>SL</th>
                <th>Name</th>
                <th>Code</th>
                <th>Type</th>
                <th>Papers</th>
                <th>Total Marks</th>
                <th>Pass Mark</th>
                <th>Status</th>
                <th>Assigned Classes</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($subjects as $key => $subject)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>
                        {{ $subject->name }}
                        @if($subject->is_parent)
                            <span class="badge badge-warning" title="Combined Subject (Parent)">Parent</span>
                        @endif
                        @if($subject->is_paper)
                            <span class="badge badge-info" title="Paper of {{ $subject->parent?->name }}">Paper</span>
                        @endif
                    </td>
                    <td>{{ $subject->code ?? 'N/A' }}</td>
                    <td>
                        <span class="badge badge-{{ $subject->type == 'mandatory' ? 'primary' : 'info' }}">
                            {{ ucfirst($subject->type) }}
                        </span>
                    </td>
                    <td>
                        @if($subject->is_parent)
                            <span class="badge badge-warning">{{ $subject->papers_count ?? $subject->papers()->count() }} papers</span>
                        @elseif($subject->is_paper)
                            <small>Part of: {{ $subject->parent?->name ?? 'N/A' }}</small>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td>{{ number_format($subject->total_marks, 2) }}</td>
                    <td>{{ number_format($subject->pass_mark, 2) }}</td>
                    <td>
                        <span class="badge badge-{{ $subject->is_active ? 'success' : 'danger' }}">
                            {{ $subject->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td>
                        @forelse($subject->classAssignments as $assignment)
                            <span class="badge badge-secondary mr-1">
                                {{ $assignment->schoolClass->name_en ?? 'N/A' }}
                            </span>
                        @empty
                            <span class="text-muted">Not assigned</span>
                        @endforelse
                    </td>
                    <td>
                        <div class="flex gap-1 items-center">
                            @if(auth()->user()?->hasPermission('view_subjects'))
                                <a href="{{ route('subjects.show', $subject->id) }}"
                                    class="btn btn-info btn-sm p-1 flex items-center justify-center" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                            @endif

                            @if(auth()->user()?->hasPermission('edit_subjects'))
                                <a href="{{ route('subjects.edit', $subject->id) }}"
                                    class="btn btn-primary btn-sm p-1 flex items-center justify-center" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                            @endif

                            @if(auth()->user()?->hasPermission('delete_subjects'))
                                <form action="{{ route('subjects.delete', $subject->id) }}" method="POST"
                                    class="inline-block m-0">
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit"
                                        class="btn btn-danger btn-sm p-1 flex items-center justify-center" title="Delete"
                                        onclick="return confirm('Are you sure?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="text-center">No subjects found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{ $subjects->links() }}
