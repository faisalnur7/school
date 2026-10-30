<?php

namespace App\Http\Controllers;

use App\Models\AcademicSession;
use App\Models\PaymentRequest;
use App\Models\SchoolClass;
use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;

class PaymentRequestController extends Controller
{
    public function index(Request $request)
    {
        $this->removeExpired();

        $requests = PaymentRequest::query()
            ->with(['student', 'academicSession', 'schoolClass', 'section'])
            ->where('status', PaymentRequest::STATUS_PENDING)
            ->where('expires_at', '>', now())
            ->when($request->filled('student_id'), function ($query) use ($request) {
                $query->whereHas('student', fn ($student) => $student
                    ->where('student_cid', 'like', '%' . trim($request->string('student_id')) . '%')
                    ->orWhere('id', (int) $request->input('student_id')));
            })
            ->when($request->filled('academic_session_id'), fn ($query) => $query->where('academic_session_id', $request->integer('academic_session_id')))
            ->when($request->filled('school_class_id'), fn ($query) => $query->where('school_class_id', $request->integer('school_class_id')))
            ->when($request->filled('section_id'), fn ($query) => $query->where('section_id', $request->integer('section_id')))
            ->latest()
            ->paginate(25)
            ->withQueryString();

        return view('pages.fees.payment_requests.index', [
            'requests' => $requests,
            'sessions' => AcademicSession::orderByDesc('id')->get(),
            'classes' => SchoolClass::orderBy('name_en')->get(),
            'sections' => Section::orderBy('name_en')->get(),
        ]);
    }

    public function show(PaymentRequest $paymentRequest)
    {
        $this->ensurePending($paymentRequest);

        return view('pages.fees.payment_requests.show', [
            'paymentRequest' => $paymentRequest->load(['student', 'academicSession', 'schoolClass', 'section', 'items']),
        ]);
    }

    public function cancel(PaymentRequest $paymentRequest)
    {
        DB::transaction(function () use ($paymentRequest) {
            $paymentRequest = PaymentRequest::query()->lockForUpdate()->findOrFail($paymentRequest->id);
            $this->ensurePending($paymentRequest);
            $paymentRequest->update([
                'status' => PaymentRequest::STATUS_CANCELLED,
                'cancelled_at' => now(),
            ]);
        });

        return redirect()
            ->route('fees.collect_payment', ['student_id' => $paymentRequest->student_id])
            ->with('success', 'Payment request cancelled. You can continue with normal fee collection.');
    }

    public function collect(PaymentRequest $paymentRequest)
    {
        $this->ensurePending($paymentRequest);

        return redirect()->to(URL::temporarySignedRoute('fees.collect_payment', now()->addMinutes(15), [
            'student_id' => $paymentRequest->student_id,
            'payment_request' => $paymentRequest->id,
        ]));
    }

    private function ensurePending(PaymentRequest $paymentRequest): void
    {
        if ($paymentRequest->isPending() && $paymentRequest->isExpired()) {
            $paymentRequest->delete();
        }

        abort_unless($paymentRequest->isPending() && ! $paymentRequest->isExpired(), 404);
    }

    private function removeExpired(): void
    {
        PaymentRequest::query()
            ->where('status', PaymentRequest::STATUS_PENDING)
            ->where('expires_at', '<=', now())
            ->delete();
    }
}
