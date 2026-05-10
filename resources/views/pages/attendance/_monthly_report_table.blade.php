@if($rows->isEmpty())
<div class="card card-body text-center text-muted py-5">
    <i class="fas fa-users fa-3x mb-3"></i>
    <p>No students found for the selected filters.</p>
</div>
@else
<div class="card card-outline card-info">
    <div class="card-header d-flex justify-content-between align-items-center py-2">
        <span class="font-weight-bold">
            <i class="fas fa-calendar-check mr-1"></i>
            Working Days: <strong class="text-primary">{{ $workingDaysCount }}</strong>
        </span>
        <button type="button" id="btnDownloadPdf" class="btn btn-sm btn-danger">
            <i class="fas fa-file-pdf mr-1"></i>Export PDF
        </button>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-sm table-bordered table-hover mb-0" style="font-size:12px;">
                <thead class="thead-light">
                    <tr>
                        <th class="text-center" style="min-width:50px;">Roll</th>
                        <th class="text-center" style="min-width:80px;">Student ID</th>
                        <th style="min-width:140px;">Name</th>
                        @foreach($allDates as $date)
                        <th class="text-center px-1"
                            style="min-width:28px;{{ isset($nonWorkingDates[$date->toDateString()]) ? 'background:#f0f0f0;' : '' }}"
                            title="{{ $date->format('D, d M Y') }}">
                            {{ $date->format('d') }}
                        </th>
                        @endforeach
                        <th class="text-center text-success" style="min-width:40px;">P</th>
                        <th class="text-center text-danger"  style="min-width:40px;">A</th>
                        <th class="text-center text-info"    style="min-width:60px;">%</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rows as $row)
                    <tr>
                        <td class="text-center">{{ $row['roll'] ?? '-' }}</td>
                        <td class="text-center">{{ $row['student_cid'] }}</td>
                        <td>{{ $row['name'] }}</td>
                        @foreach($allDates as $date)
                        @php $cell = $row['cells'][$date->toDateString()]; @endphp
                        <td class="text-center px-1
                            @if($cell === 'P') text-success font-weight-bold
                            @elseif($cell === 'A') text-danger
                            @else text-muted
                            @endif"
                            style="{{ isset($nonWorkingDates[$date->toDateString()]) ? 'background:#f8f8f8;' : '' }}">
                            {{ $cell }}
                        </td>
                        @endforeach
                        <td class="text-center font-weight-bold text-success">{{ $row['present'] }}</td>
                        <td class="text-center font-weight-bold text-danger">{{ $row['absent'] }}</td>
                        <td class="text-center font-weight-bold
                            @if($row['percentage'] >= 75) text-success
                            @elseif($row['percentage'] >= 50) text-warning
                            @else text-danger
                            @endif">
                            {{ $row['percentage'] }}%
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif
