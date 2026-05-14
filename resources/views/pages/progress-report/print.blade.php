<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Terminal Progress Report</title>

    <!-- TailwindCSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        @media print {
            body {
                background: white !important;
            }

            .report-card {
                box-shadow: none !important;
                border: none !important;
            }
        }

        table {
            border-collapse: collapse;
        }

        td,
        th {
            border: 1px solid #555;
        }
    </style>
</head>

<body class="bg-gray-100 py-10">

    @foreach($studentsData as $data)
    @php
        $student     = $data['student'];
        $info        = $data['academicInfo'];
        $subjectRows = $data['subjectRows'];
        $summary     = $data['summary'];
        $attendancePresent = $data['attendancePresent'];
        $attendanceTotal = $data['attendanceTotal'];
    @endphp

    <div class="max-w-5xl mx-auto bg-white p-8 report-card shadow-lg border border-gray-300 mb-10">

        <!-- Header -->
        <div class="text-center border-b pb-4 relative">

            <h1 class="text-3xl font-bold text-green-700 uppercase tracking-wide">
        {{ $school->name ?? 'Green Chartered School & College' }}

            <p class="text-sm text-gray-700 mt-1">
                {{ $school->address ?? 'CIP Tower, Hazari-digir-phar, Dohajari, Chandanish, Chattogram' }}
            </p>

            <h2 class="text-2xl font-bold text-orange-700 italic mt-5 uppercase">
                Progress Report
            </h2>

            <!-- Grade Table -->
            <div class="absolute top-0 right-0">
                <table class="text-xs border border-gray-700">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-3 py-1">Letter Grade</th>
                            <th class="px-3 py-1">Point</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($gradeScale as $grade)
                        <tr>
                            <td class="px-3 py-1">{{ $grade['min'] }}-{{ $grade['max'] }} = {{ $grade['letter'] }}</td>
                            <td class="px-3 py-1">{{ number_format($grade['gpa'], 1) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Exam Info -->
        <div class="mt-6 flex justify-between items-start">

            <div>
                <h3 class="font-bold text-xl underline">
                    {{ $exam->name }}
                </h3>

                <div class="mt-4 space-y-1 text-sm">
                    <p><span class="font-semibold">Name</span> : {{ $student->full_name_en }}</p>
                    <p><span class="font-semibold">Class</span> : {{ $info?->schoolClass?->name_en ?? '—' }}</p>
                    <p><span class="font-semibold">ID</span> : {{ $student->student_cid ?? $student->id }}</p>
                </div>
            </div>

        </div>

        <!-- Result Table -->
        <div class="mt-6 overflow-x-auto">

            <table class="w-full text-sm border border-gray-700">

                <thead class="bg-gray-100 text-center">
                    <tr>
                        <th class="px-3 py-2 text-left">Subjects</th>
                        <th class="px-3 py-2">Full Marks</th>
                        <th class="px-3 py-2">Obtained Marks</th>
                        <th class="px-3 py-2">Highest Marks</th>
                        <th class="px-3 py-2">Total Marks</th>
                        <th class="px-3 py-2">Letter Grade</th>
                        <th class="px-3 py-2">Grade Point</th>
                    </tr>
                </thead>

                <tbody>

                    @foreach($subjectRows as $row)
                    @if(!empty($row['papers']))
                        @foreach($row['papers'] as $paperIndex => $paper)
                        <tr>
                            <td class="px-3 py-2 font-medium">
                                {{ $paper['subject_name'] }}
                            </td>

                            <td class="text-center">{{ number_format($paper['full_marks'], 0) }}</td>
                            <td class="text-center">{{ $paper['obtained'] ? number_format($paper['obtained'], 0) : '—' }}</td>
                            <td class="text-center">{{ number_format($paper['highest'], 0) }}</td>

                            @if($paperIndex === 0)
                                <td rowspan="{{ count($row['papers']) }}" class="text-center align-middle font-semibold">
                                    {{ is_null($row['obtained']) ? '—' : number_format($row['obtained'], 0) }}
                                </td>
                                <td rowspan="{{ count($row['papers']) }}" class="text-center align-middle font-semibold">
                                    {{ $row['grade'] }}
                                </td>
                                <td rowspan="{{ count($row['papers']) }}" class="text-center align-middle font-semibold">
                                    {{ number_format($row['gpa'], 1) }}
                                </td>
                            @endif
                        </tr>
                        @endforeach
                    @else
                    <!-- Single Subject -->
                    <tr>
                        <td class="px-3 py-2 font-medium">
                            {{ $row['subject_name'] }}
                        </td>

                        <td class="text-center">{{ number_format($row['full_marks'], 0) }}</td>
                        <td class="text-center">{{ $row['obtained'] ? number_format($row['obtained'], 0) : '—' }}</td>
                        <td class="text-center">{{ number_format($row['highest'], 0) }}</td>
                        <td class="text-center">{{ $row['obtained'] ? number_format($row['obtained'], 0) : '—' }}</td>
                        <td class="text-center">{{ $row['grade'] }}</td>
                        <td class="text-center">{{ number_format($row['gpa'], 1) }}</td>
                    </tr>
                    @endif
                    @endforeach

                </tbody>
            </table>

        </div>

        <!-- Summary -->
        <div class="mt-6">

            <table class="w-full text-sm border border-gray-700">

                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-3 py-2">Summary</th>
                        <th class="px-3 py-2">Total Exam Marks</th>
                        <th class="px-3 py-2">Obtained Total Marks/Percent</th>
                        <th class="px-3 py-2">GPA</th>
                        <th class="px-3 py-2">Letter Grade</th>
                    </tr>
                </thead>

                <tbody>
                    <tr class="text-center">
                        <td class="py-2"></td>

                        <!-- Total Full Marks -->
                        <td>{{ number_format($summary['fullMarks'], 0) }}</td>

                        <!-- Total Obtained -->
                        <td>{{ number_format($summary['obtained'], 0) }} / {{ number_format($summary['percentage'], 2) }}%</td>

                        <!-- Correct GPA -->
                        <td>{{ number_format($summary['gpa'], 2) }}</td>

                        <!-- Final Grade -->
                        <td>{{ $summary['grade'] }}</td>
                    </tr>
                </tbody>

            </table>

        </div>

        <!-- Remarks -->
        <div class="mt-6 text-sm">

            <h4 class="font-bold underline mb-2">
                Remarks:
            </h4>

            <div class="space-y-1">
                @if($summary['gpa'] >= 4.0)
                <p class="inline-block bg-green-200 px-2 rounded">(i) Excellent</p>
                @elseif($summary['gpa'] >= 3.0)
                <p class="inline-block bg-green-200 px-2 rounded">(ii) Good</p>
                @elseif($summary['gpa'] >= 2.0)
                <p>(iii) Satisfactory</p>
                @else
                <p>(iv) Need to be improved</p>
                @endif
            </div>

        </div>

        <!-- Comments -->
        <div class="mt-6 border border-gray-400 p-4 text-sm">

            <ul class="list-disc pl-5 space-y-2">
                <li>{{ $student->full_name_en }} was present {{ $attendancePresent }} days out of {{ $attendanceTotal }} days.</li>
                @if($summary['gpa'] >= 4.0)
                <li>Excellent results! You faithfully perform classroom tasks.</li>
                @elseif($summary['gpa'] >= 3.0)
                <li>Good results! Keep up the good work.</li>
                @else
                <li>Need to improve performance.</li>
                @endif
            </ul>

        </div>

        <!-- Footer -->
        <div class="mt-10 flex justify-between items-end text-sm">

            <div>
                <p class="font-semibold">
                    Published Date: {{ now()->format('d-m-Y') }}
                </p>

                <div class="mt-12">
                    <div class="border-t border-black w-40"></div>
                    <p>Class Teacher</p>
                </div>
            </div>

            <div class="text-right">
                <div class="border-t border-black w-40 ml-auto"></div>
                <p>Principal</p>
            </div>

        </div>

    </div>

    @endforeach

</body>

</html>