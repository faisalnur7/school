@php
    $schoolName = $setting?->name ?: 'School Name';
    $schoolPhone = implode(', ', array_filter([$setting?->contact_number_1, $setting?->contact_number_2]));
    $accent = $cardSettings?->card_solid_color ?: ($cardSettings?->card_color_gradient_1 ?: '#1e3a5f');
    $imagePath = function (?string $path): ?string {
        return $path && file_exists(public_path($path)) ? public_path($path) : null;
    };
    $placeholder = public_path('assets/img/male-placeholder.png');
@endphp

@foreach($employees as $employee)
    @php
        $photo = $imagePath($employee->photo) ?: $placeholder;
        $department = is_string($employee->department) ? $employee->department : ($employee->department?->name ?: '—');
        $designation = $employee->designation?->name ?: '—';
        $employeeType = ucfirst((string) $employee->employee_type);
        $joiningDate = $employee->joining_date?->format('d M Y') ?: '—';
    @endphp
    <div style="text-align: center;">
        <div style="width: 520px; margin: 18px auto 0; border: 1px solid #cbd5e1; background-color: #ffffff; text-align: left;">
            <div style="padding: 22px 28px 18px; color: #ffffff; background-color: {{ $accent }}; text-align: center;">
                <div style="font-size: 20px; font-weight: bold; text-transform: uppercase;">{{ $schoolName }}</div>
                @if($setting?->slogan)<div style="font-size: 11px; margin-top: 6px;">{{ $setting->slogan }}</div>@endif
                <div style="margin-top: 10px; font-size: 11px; font-weight: bold; letter-spacing: 1px;">EMPLOYEE ID</div>
            </div>
            <div style="padding: 24px 32px;">
                <img src="{{ $photo }}" alt="Employee photo" width="105" height="132" style="display: block; margin: 0 auto 12px; border: 1px solid #dbe3ee;">
                <div style="text-align: center; font-size: 22px; font-weight: bold; color: {{ $accent }};">{{ $employee->name }}</div>
                <div style="border-top: 2px solid {{ $accent }}; margin: 14px 70px;"></div>
                <div style="font-size: 13px; line-height: 1.8;">
                    <div><b style="display: inline-block; width: 125px; font-size: 10px; color: #64748b;">ID</b>{{ $employee->employee_id ?: '—' }}</div>
                    <div><b style="display: inline-block; width: 125px; font-size: 10px; color: #64748b;">ROLE</b>{{ $designation }}</div>
                    <div><b style="display: inline-block; width: 125px; font-size: 10px; color: #64748b;">DEPARTMENT</b>{{ $department }}</div>
                    <div><b style="display: inline-block; width: 125px; font-size: 10px; color: #64748b;">TYPE</b>{{ $employeeType }}</div>
                    <div><b style="display: inline-block; width: 125px; font-size: 10px; color: #64748b;">JOINING DATE</b>{{ $joiningDate }}</div>
                    @if($employee->phone)<div><b style="display: inline-block; width: 125px; font-size: 10px; color: #64748b;">PHONE</b>{{ $employee->phone }}</div>@endif
                </div>
            </div>
            <div style="padding: 10px 16px; color: #ffffff; background-color: {{ $accent }}; text-align: center; font-size: 10px;">{{ $schoolPhone ?: ($setting?->address ?: $schoolName) }}</div>
        </div>
    </div>
    <pagebreak />
    <div style="text-align: center;">
        <div style="width: 520px; margin: 18px auto 0; border: 1px solid #cbd5e1; background-color: #ffffff; text-align: left;">
            <div style="padding: 22px 28px 18px; color: #ffffff; background-color: {{ $accent }}; text-align: center;">
                <div style="font-size: 20px; font-weight: bold; text-transform: uppercase;">{{ $schoolName }}</div>
                <div style="margin-top: 10px; font-size: 11px; font-weight: bold; letter-spacing: 1px;">BACK</div>
            </div>
            <div style="padding: 30px 42px 55px;">
                <div style="color: {{ $accent }}; font-size: 22px; font-weight: bold; border-bottom: 2px solid {{ $accent }}; padding-bottom: 12px; margin-bottom: 18px;">Employee Details</div>
                <div style="font-size: 13px; line-height: 1.9;">
                    <div><b style="display: inline-block; width: 135px; font-size: 10px; color: #64748b;">NAME</b>{{ $employee->name }}</div>
                    <div><b style="display: inline-block; width: 135px; font-size: 10px; color: #64748b;">EMPLOYEE ID</b>{{ $employee->employee_id ?: '—' }}</div>
                    <div><b style="display: inline-block; width: 135px; font-size: 10px; color: #64748b;">ROLE</b>{{ $designation }}</div>
                    <div><b style="display: inline-block; width: 135px; font-size: 10px; color: #64748b;">DEPARTMENT</b>{{ $department }}</div>
                    @if($employee->email)<div><b style="display: inline-block; width: 135px; font-size: 10px; color: #64748b;">EMAIL</b>{{ $employee->email }}</div>@endif
                    @if($employee->address)<div><b style="display: inline-block; width: 135px; font-size: 10px; color: #64748b;">ADDRESS</b>{{ $employee->address }}</div>@endif
                </div>
            </div>
            <div style="padding: 10px 16px; color: #ffffff; background-color: {{ $accent }}; text-align: center; font-size: 10px;">If found, please return this card to {{ $schoolName }}.</div>
        </div>
    </div>
    @if(!$loop->last)<pagebreak />@endif
@endforeach
