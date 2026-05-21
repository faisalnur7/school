<?php

namespace App\Http\Controllers;

use App\Mail\PasswordChangeVerificationCodeMail;
use App\Models\Holiday;
use App\Models\LeaveBalance;
use App\Models\LeaveRequest;
use App\Models\SubjectClassAssignment;
use App\Models\TeacherSectionAssignment;
use App\Services\LeaveService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AccountSettingsController extends Controller
{
    public function __construct(private LeaveService $leaveService)
    {
    }

    public function editProfile(Request $request)
    {
        $user = $request->user()->load(['role', 'employee.designation']);
        $employee = $user->employee;

        $teacherAssignments = collect();
        $assignedSubjects = collect();
        $leaveBalances = collect();
        $holidays = collect();

        if ($employee && $employee->employee_type === 'teacher') {
            $teacherAssignments = TeacherSectionAssignment::query()
                ->with(['session', 'schoolClass', 'section'])
                ->where('user_id', $user->id)
                ->latest('id')
                ->get();

            $classIds = $teacherAssignments->pluck('class_id')->unique()->values();

            if ($classIds->isNotEmpty()) {
                $assignedSubjects = SubjectClassAssignment::query()
                    ->with(['subject', 'schoolClass'])
                    ->whereIn('school_class_id', $classIds)
                    ->get()
                    ->unique(fn ($item) => $item->subject_id.'-'.$item->school_class_id)
                    ->values();
            }

            $leaveBalances = LeaveBalance::query()
                ->where('employee_id', $employee->id)
                ->orderBy('leave_type')
                ->get();

            $holidays = Holiday::query()
                ->whereDate('date', '>=', now()->toDateString())
                ->orderBy('date')
                ->limit(30)
                ->get();
        }

        return view('account.profile-edit', compact(
            'user',
            'employee',
            'teacherAssignments',
            'assignedSubjects',
            'leaveBalances',
            'holidays'
        ));
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $user = $request->user();
        $employee = $user->employee;

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:1000'],
            'image' => ['nullable', 'mimes:jpg,jpeg,png,webp', 'max:100'],
        ]);

        $user->name = $data['name'];
        $user->email = $data['email'];

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $path = 'images/user_image';
            if (! is_dir(public_path($path))) {
                mkdir(public_path($path), 0775, true);
            }
            $filename = 'profile_'.$user->id.'_'.time().'.'.$file->getClientOriginalExtension();
            $file->move(public_path($path), $filename);
            $user->image = $path.'/'.$filename;
        }

        $user->save();

        if ($employee) {
            $employee->phone = $data['phone'] ?? $employee->phone;
            $employee->address = $data['address'] ?? $employee->address;
            $employee->name = $data['name'];

            $employee->save();
        }

        return redirect()->route('account.profile.edit')->with('success', 'Profile updated successfully.');
    }

    public function editPassword()
    {
        return view('account.change-password');
    }

    public function sendPasswordCode(Request $request): RedirectResponse
    {
        $user = $request->user();

        if (! $user->email) {
            return back()->with('error', 'No email found for this account.');
        }

        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $user->forceFill([
            'password_change_verification_code' => Hash::make($code),
            'password_change_verification_expires_at' => now()->addMinutes(10),
        ])->save();

        Mail::to($user->email)->send(new PasswordChangeVerificationCodeMail($user, $code));

        return back()->with('success', 'Verification code sent to your email.');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => [
                'required',
                'string',
                'min:8',
                'max:64',
                'confirmed',
                'regex:/[a-z]/',
                'regex:/[A-Z]/',
                'regex:/[0-9]/',
                'regex:/[^A-Za-z0-9]/',
            ],
        ], [
            'password.regex' => 'Password must include uppercase, lowercase, number and special character.',
        ]);

        if (! $user->email) {
            return back()->with('error', 'No email found for this account.');
        }

        $oneTimeCode = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $request->session()->put('password_change.code_hash', Hash::make($oneTimeCode));
        $request->session()->put('password_change.code_expires_at', now()->addMinutes(10)->timestamp);
        $request->session()->put('password_change.pending_hash', Hash::make($validated['password']));

        Mail::to($user->email)->send(new PasswordChangeVerificationCodeMail($user, $oneTimeCode));

        return redirect()->route('account.password.verify.form')->with('success', 'Verification code sent to your email.');
    }

    public function showVerifyPasswordCode()
    {
        if (! session()->has('password_change.pending_hash')) {
            return redirect()->route('account.password.edit')->with('error', 'Start password change first.');
        }

        return view('account.verify-password-code');
    }

    public function verifyPasswordCode(Request $request): RedirectResponse
    {
        $request->validate([
            'verification_code' => ['required', 'digits:6'],
        ]);

        $user = $request->user();

        if (! $request->session()->has('password_change.pending_hash')) {
            return back()->withErrors(['verification_code' => 'Password update session expired. Submit password form again.']);
        }

        $codeHash = $request->session()->get('password_change.code_hash');
        $expiresAt = (int) $request->session()->get('password_change.code_expires_at', 0);

        if (! $codeHash || ! $expiresAt) {
            return back()->withErrors(['verification_code' => 'No active verification code. Submit password form again.']);
        }

        if (now()->timestamp > $expiresAt) {
            return back()->withErrors(['verification_code' => 'Verification code expired. Submit password form again.']);
        }

        if (! Hash::check((string) $request->verification_code, $codeHash)) {
            return back()->withErrors(['verification_code' => 'Invalid verification code.']);
        }

        $user->forceFill([
            'password' => $request->session()->pull('password_change.pending_hash'),
        ])->save();
        $request->session()->forget('password_change.code_hash');
        $request->session()->forget('password_change.code_expires_at');

        return redirect()->route('account.password.edit')->with('success', 'Password changed successfully.');
    }

    public function createLeave()
    {
        $employee = auth()->user()->employee;
        abort_unless($employee && $employee->employee_type === 'teacher', 403);

        $leaveBalances = LeaveBalance::query()
            ->where('employee_id', $employee->id)
            ->orderBy('leave_type')
            ->get();

        $leaveRequests = LeaveRequest::query()
            ->where('employee_id', $employee->id)
            ->latest()
            ->limit(15)
            ->get();

        return view('account.apply-leave', compact('employee', 'leaveBalances', 'leaveRequests'));
    }

    public function storeLeave(Request $request): RedirectResponse
    {
        $employee = $request->user()->employee;
        abort_unless($employee && $employee->employee_type === 'teacher', 403);

        $data = $request->validate([
            'leave_type' => 'required|in:casual,sick,annual,maternity,other',
            'date_from' => 'required|date',
            'date_to' => 'required|date|after_or_equal:date_from',
            'reason' => 'required|string',
        ]);

        $data['employee_id'] = $employee->id;

        try {
            $this->leaveService->submit($data);
            return redirect()->route('account.leave.create')->with('success', 'Leave request submitted successfully.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }
}
