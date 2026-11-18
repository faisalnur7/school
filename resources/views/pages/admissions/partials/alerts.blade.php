@if (session('success'))
    <div class="alert alert-success border-0 shadow-sm" role="status">
        <i class="fas fa-check-circle mr-1"></i> {{ session('success') }}
    </div>
@endif

@if ($errors->any())
    <div class="alert alert-danger border-0 shadow-sm" role="alert">
        @if ($errors->count() === 1)
            <i class="fas fa-exclamation-circle mr-1"></i> {{ $errors->first() }}
        @else
            <strong><i class="fas fa-exclamation-circle mr-1"></i> Please review the following:</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        @endif
    </div>
@endif
