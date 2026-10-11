@extends('layouts.master')
@section('contents')
<div class="container-fluid px-3 py-3"><div class="card shadow-sm border-0"><div class="card-header"><h4 class="mb-0">Edit Platform Admin</h4></div>
<form method="POST" action="{{ route('platform-admins.update', $user) }}">@csrf @method('PUT')
<div class="card-body"><div class="row">
<div class="col-md-6 mb-3"><label>Name</label><input name="name" class="form-control" value="{{ old('name', $user->name) }}" required></div>
<div class="col-md-6 mb-3"><label>Email</label><input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required></div>
<div class="col-md-6 mb-3"><label>New Password</label><input type="password" name="password" class="form-control"></div>
<div class="col-md-6 mb-3"><label>Confirm Password</label><input type="password" name="password_confirmation" class="form-control"></div>
</div></div><div class="card-footer d-flex justify-content-between"><a href="{{ route('platform-admins.index') }}" class="btn btn-secondary btn-sm">Cancel</a><button class="btn btn-primary btn-sm">Update</button></div>
</form></div></div>
@endsection
