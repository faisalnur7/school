<div class="content-header ml-2">
    <div class="container-fluid">
        <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between" style="gap: .5rem;">
            <div style="min-width: 0;">
                <h1 class="m-0 text-truncate">{{ $pageTitle ?? 'Dashboard' }}</h1>
            </div>

            @if (!empty($hubRoute) && !empty($routeName ?? null) && ($routeName ?? null) !== $hubRoute)
                <div style="flex-shrink: 0;">
                    <a href="{{ route($hubRoute) }}" class="btn btn-outline-primary btn-sm rounded-pill">
                        <i class="fas fa-arrow-left mr-1"></i> Back to Hub
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
<!-- /.content-header -->
