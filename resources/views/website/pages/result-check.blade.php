@extends('website.layouts.app')

@section('title', 'Check Student Result - ' . ($settings['school_name'] ?? config('app.name')))

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Check Student Result</h3>
                </div>
                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    <form action="{{ route('website.result.show') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="student_cid" class="form-label">Student CID</label>
                            <input type="text" class="form-control" id="student_cid" name="student_cid" placeholder="Enter Student CID (e.g., 000001)" required>
                            <div class="form-text">Enter the 6-digit Student CID to check results</div>
                        </div>
                        <div class="mb-3">
                            <label for="session_id" class="form-label">Select Session</label>
                            <select class="form-select" id="session_id" name="session_id" required>
                                <option value="">-- Select Session --</option>
                                @foreach($sessions as $session)
                                    <option value="{{ $session->id }}">{{ $session->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="exam_type" class="form-label">Select Exam Type</label>
                            <select class="form-select" id="exam_type" name="exam_type" required>
                                <option value="">-- Select Exam Type --</option>
                                @foreach($examTypes as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Check Result</button>
                    </form>
                    <div class="mt-4 text-center">
                        <p class="text-muted">Don't know the Student CID? Contact the school office for assistance.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
