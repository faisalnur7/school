{{-- Modern Form Layout Component --}}
@props([
    'title' => 'Form',
    'icon' => 'fas fa-edit',
    'backRoute' => null,
    'submitText' => 'Save',
    'submitIcon' => 'fas fa-save',
    'errors' => null,
])

<div class="container-fluid px-3 py-3">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-gradient-primary text-white py-3">
            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-stretch align-items-sm-center gap-2">
                <h4 class="card-title mb-0 font-weight-bold text-white">
                    <i class="{{ $icon }} mr-2"></i>{{ $title }}
                </h4>
                @if($backRoute)
                    <a href="{{ $backRoute }}" class="btn btn-light btn-sm w-100 w-sm-auto">
                        <i class="fas fa-arrow-left mr-1"></i> Back
                    </a>
                @endif
            </div>
        </div>

        <form method="POST" action="{{ $attributes['action'] }}" id="modernForm" {{ $attributes->merge(['class' => '']) }}>
            @csrf
            @if(isset($method))
                @method($method)
            @endif

            <div class="card-body p-3">
                @if($errors && $errors->any())
                    <div class="alert alert-danger alert-dismissible fade show border-0 mb-3" role="alert">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        <strong>Errors:</strong>
                        <ul class="mb-0 mt-1 ml-4">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                {{ $slot }}
            </div>

            <div class="card-footer bg-light border-top py-2 px-3">
                <div class="d-flex flex-column flex-sm-row justify-content-between gap-2">
                    @if($backRoute)
                        <a href="{{ $backRoute }}" class="btn btn-secondary btn-sm w-100 w-sm-auto">
                            <i class="fas fa-times mr-1"></i>Cancel
                        </a>
                    @endif
                    <button type="submit" class="btn btn-primary btn-sm w-100 w-sm-auto">
                        <i class="{{ $submitIcon }} mr-1"></i>{{ $submitText }}
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@section('styles')
@include('components.form-styles')
@endsection

@section('scripts')
<script>
    $(function () {
        // Auto-scroll to first error
        if ($('.is-invalid').length > 0) {
            $('html, body').animate({
                scrollTop: $('.is-invalid').first().offset().top - 50
            }, 300);
        }

        // Form validation feedback
        $('#modernForm').on('submit', function() {
            if (!this.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            $(this).addClass('was-validated');
        });
    });
</script>
@endsection
