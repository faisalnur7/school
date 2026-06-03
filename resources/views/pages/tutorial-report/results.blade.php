@extends('layouts.master')

@section('contents')
<div class="col-12">
    <div class="d-flex justify-content-between align-items-center mb-4 no-print">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-circle d-flex align-items-center justify-content-center shadow"
                style="width:52px;height:52px;background:linear-gradient(135deg,#0891b2,#0e7490);flex-shrink:0">
                <i class="fas fa-clipboard-list text-white fa-lg"></i>
            </div>
            <div>
                <h4 class="mb-0 font-weight-bold text-white">Tutorial Exam Report</h4>
                <small class="text-muted">{{ $exam->name }} &mdash;
                    {{ $exam->academicSession->name_en ?? ($exam->academicSession->name_bn ?? '') }}</small>
            </div>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('result.tutorial-report.pdf', $filters) }}" target="_blank" class="btn btn-danger btn-sm">
                <i class="fas fa-file-pdf mr-1"></i> PDF
            </a>
            <button onclick="window.print()" class="btn btn-info btn-sm no-print">
                <i class="fas fa-print mr-1"></i> Print
            </button>
            <a href="{{ route('result.tutorial-report.index') }}" class="btn btn-secondary btn-sm no-print">
                <i class="fas fa-arrow-left mr-1"></i> Back
            </a>
        </div>
    </div>

    @foreach($studentsData as $data)
        @php
            $student = $data['student'];
            $rows = $data['rows'];
        @endphp
        <div class="card mb-4">
            <div class="card-header flex justify-between">
                <div>
                    <strong class="text-white">{{ $student->full_name_en }}</strong>
                    <span class="text-white ml-2">ID: {{ $student->student_cid ?? $student->id }}</span>
                </div>
                <div class="ml-auto">
                    <span class="badge badge-info">Total Obtained: {{ number_format($data['total_obtained'], 1) }}</span>
                    {{-- <span class="badge ml-2 js-email-status {{ !empty($statusMap[$student->id]) ? 'badge-success' : 'badge-secondary' }}"
                        id="tutorial-email-status-{{ $student->id }}">
                        {{ !empty($statusMap[$student->id]) ? 'Email Sent' : 'Not Sent' }}
                    </span> --}}
                    {{-- <button type="button"
                        class="btn btn-sm btn-success ml-2 js-send-result-email"
                        data-url="{{ route('result.tutorial-report.email') }}"
                        data-student-id="{{ $student->id }}"
                        data-session-id="{{ $filters['session_id'] }}"
                        data-class-id="{{ $filters['class_id'] }}"
                        data-section-id="{{ $filters['section_id'] }}"
                        data-exam-id="{{ $filters['exam_id'] }}"
                        data-status-id="tutorial-email-status-{{ $student->id }}">
                        <i class="fas fa-envelope mr-1"></i> Send to Parents
                    </button> --}}
                </div>
            </div>
            <div class="card-body p-0">
                <table class="table table-sm table-bordered mb-0">
                    <thead class="thead-dark">
                        <tr>
                            <th>Subject</th>
                            <th class="text-center" style="width:160px">Obtained</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rows as $r)
                            <tr class="{{ $r['is_absent'] ? 'table-secondary' : '' }}">
                                <td>{{ $r['subject_name'] }}</td>
                                <td class="text-center">{{ $r['is_absent'] ? 'AB' : number_format($r['obtained'], 1) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="text-center text-muted py-3">No marks found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endforeach
</div>
@endsection

@section('scripts')
<script>
document.querySelectorAll('.js-send-result-email').forEach((btn) => {
    btn.addEventListener('click', async () => {
        if (btn.dataset.sending === '1') return;
        btn.dataset.sending = '1';
        btn.disabled = true;
        const originalHtml = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Sending...';

        const payload = {
            session_id: btn.dataset.sessionId,
            class_id: btn.dataset.classId,
            section_id: btn.dataset.sectionId,
            exam_id: btn.dataset.examId,
            student_id: btn.dataset.studentId,
        };

        try {
            const res = await fetch(btn.dataset.url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(payload)
            });
            const data = await res.json();
            if (!res.ok || !data.ok) {
                throw new Error(data.message || 'Failed to send email.');
            }

            const statusEl = document.getElementById(btn.dataset.statusId);
            if (statusEl) {
                statusEl.classList.remove('badge-secondary');
                statusEl.classList.add('badge-success');
                statusEl.textContent = 'Email Sent';
            }
            btn.innerHTML = '<i class="fas fa-check mr-1"></i> Sent';
        } catch (e) {
            alert(e.message || 'Failed to send email.');
            btn.innerHTML = originalHtml;
            btn.disabled = false;
        } finally {
            btn.dataset.sending = '0';
        }
    });
});
</script>
@endsection
