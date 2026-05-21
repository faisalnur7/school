@extends('layouts.master')

@section('contents')
<div class="col-12 col-md-6">
    <div class="card">
        <div class="card-header bg-gradient-info text-white">
            <h3 class="card-title text-white mb-0"><i class="fas fa-shield-alt mr-2"></i>Verify Password Change</h3>
        </div>

        <div class="card-body">
            @include('hr._alerts')

            <p class="text-muted">Enter the 6-digit verification code sent to your email.</p>

            <form method="POST" action="{{ route('account.password.verify.submit') }}">
                @csrf

                <div class="form-group">
                    <label>6-digit Verification Code</label>
                    <input type="text" name="verification_code" maxlength="6" pattern="[0-9]{6}" class="form-control" required>
                </div>

                <button type="submit" class="btn btn-primary">Verify and Change Password</button>
                <a href="{{ route('account.password.edit') }}" class="btn btn-light">Back</a>
            </form>
        </div>
    </div>
</div>
@endsection
