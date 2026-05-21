@extends('layouts.master')

@section('contents')
<div class="col-12 col-md-12">
    <div class="card">
        <div class="card-header bg-gradient-dark text-white">
            <h3 class="card-title text-white mb-0"><i class="fas fa-key mr-2"></i>Change Password</h3>
        </div>

        <div class="card-body">
            @include('hr._alerts')

            <form method="POST" action="{{ route('account.password.update') }}">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label>Current Password</label>
                    <input type="password" name="current_password" class="form-control" required>
                </div>

                <div class="form-group">
                    <label>New Password</label>
                    <input type="password" name="password" id="new_password" class="form-control" required>
                    <small class="text-muted">At least 8 chars with uppercase, lowercase, number, and special character.</small>
                    <div class="progress mt-2" style="height:8px;">
                        <div id="pwd_strength_bar" class="progress-bar" role="progressbar" style="width:0%"></div>
                    </div>
                    <small id="pwd_strength_text" class="text-muted">Strength: Not set</small>
                </div>

                <div class="form-group">
                    <label>Confirm New Password</label>
                    <input type="password" name="password_confirmation" class="form-control" required>
                </div>
                <small class="text-muted d-block mb-3">After submit, a 6-digit code will be sent to your email and you will verify on next page.</small>
                <button type="submit" class="btn btn-primary">Continue</button>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    const input = document.getElementById('new_password');
    const bar = document.getElementById('pwd_strength_bar');
    const text = document.getElementById('pwd_strength_text');

    function scorePassword(value) {
        let score = 0;
        if (value.length >= 8) score++;
        if (/[a-z]/.test(value)) score++;
        if (/[A-Z]/.test(value)) score++;
        if (/[0-9]/.test(value)) score++;
        if (/[^A-Za-z0-9]/.test(value)) score++;
        return score;
    }

    function render(score) {
        const percent = score * 20;
        bar.style.width = percent + '%';

        if (score <= 2) {
            bar.className = 'progress-bar bg-danger';
            text.textContent = 'Strength: Weak';
        } else if (score <= 4) {
            bar.className = 'progress-bar bg-warning';
            text.textContent = 'Strength: Medium';
        } else {
            bar.className = 'progress-bar bg-success';
            text.textContent = 'Strength: Strong';
        }
    }

    input.addEventListener('input', function () {
        render(scorePassword(input.value));
    });
})();
</script>
@endsection
