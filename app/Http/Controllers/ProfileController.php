<?php

namespace App\Http\Controllers;

use App\Models\Holiday;
use App\Models\LeaveBalance;
use App\Models\LeaveRequest;
use App\Models\SubjectClassAssignment;
use App\Models\TeacherSectionAssignment;
use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
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

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
