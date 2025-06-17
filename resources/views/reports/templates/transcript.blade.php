// resources/views/reports/templates/transcript.blade.php
<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>ໃບຄະແນນ</title>
    <style>
        body {
            font-family: 'Phetsarath OT', sans-serif;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .school-name {
            font-size: 24px;
            font-weight: bold;
        }

        .report-title {
            font-size: 18px;
            margin-top: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .footer {
            margin-top: 30px;
        }

        .signature {
            float: right;
            margin-top: 50px;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="header">
        <div class="school-name">ໂຮງຮຽນສາຍຝົນ</div>
        <div class="report-title">ໃບຄະແນນນັກຮຽນ</div>
        <div>
            ສົກຮຽນ: {{ $data['academic_year']['year_name'] ?? '' }}
            @if(isset($data['class']))
                | ຫ້ອງຮຽນ: {{ $data['class']['class_name'] ?? '' }}
            @endif
        </div>
    </div>

    @if(isset($data['student']))
        <!-- ສະແດງຂໍ້ມູນສຳລັບນັກຮຽນຄົນດຽວ -->
        <div>
            <p><strong>ລະຫັດນັກຮຽນ:</strong> {{ $data['student']['student_code'] }}</p>
            <p><strong>ຊື່-ນາມສະກຸນ:</strong> {{ $data['student']['first_name_lao'] }}
                {{ $data['student']['last_name_lao'] }}</p>
        </div>

        <table>
            <thead>
                <tr>
                    <th>ລຳດັບ</th>
                    <th>ລະຫັດວິຊາ</th>
                    <th>ຊື່ວິຊາ</th>
                    <th>ການສອບເສັງ</th>
                    <th>ຄະແນນ</th>
                    <th>ເກຣດ</th>
                    <th>ໝາຍເຫດ</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data['grades'] as $index => $grade)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $grade['subject']['subject_code'] }}</td>
                        <td>{{ $grade['subject']['subject_name_lao'] }}</td>
                        <td>{{ $grade['examination']['exam_name'] }}</td>
                        <td>{{ $grade['marks'] }}</td>
                        <td>{{ $grade['grade_letter'] }}</td>
                        <td>{{ $grade['comments'] }}</td>
                    </tr>
                @endforeach

                @if(count($data['grades']) == 0)
                    <tr>
                        <td colspan="7" style="text-align: center;">ບໍ່ມີຂໍ້ມູນຄະແນນ</td>
                    </tr>
                @endif
            </tbody>
        </table>
    @else
        <!-- ສະແດງຂໍ້ມູນສຳລັບຫຼາຍນັກຮຽນໃນຫ້ອງດຽວກັນ -->
        <table>
            <thead>
                <tr>
                    <th>ລຳດັບ</th>
                    <th>ລະຫັດນັກຮຽນ</th>
                    <th>ຊື່-ນາມສະກຸນ</th>
                    @foreach($data['subjects'] ?? [] as $subject)
                        <th>{{ $subject['subject_code'] }}</th>
                    @endforeach
                    <th>ຄະແນນລວມ</th>
                    <th>ສະເລ່ຍ</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data['students'] as $index => $student)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $student['student_code'] }}</td>
                            <td>{{ $student['first_name_lao'] }} {{ $student['last_name_lao'] }}</td>

                            @php
                                $totalMarks = 0;
                                $subjectCount = 0;
                            @endphp

                            @foreach($data['subjects'] ?? [] as $subject)
                                    @php
                                        $studentGrade = collect($student['grades'])->first(function ($grade) use ($subject) {
                                            return $grade['subject_id'] == $subject['subject_id'];
                                        });

                                        if ($studentGrade) {
                                            $totalMarks += $studentGrade['marks'];
                                            $subjectCount++;
                                        }
                                    @endphp

                                    <td>{{ $studentGrade ? $studentGrade['marks'] : '-' }}</td>
                            @endforeach

                            <td>{{ $totalMarks }}</td>
                            <td>{{ $subjectCount > 0 ? number_format($totalMarks / $subjectCount, 2) : '-' }}</td>
                        </tr>
                @endforeach

                @if(count($data['students']) == 0)
                    <tr>
                        <td colspan="{{ 5 + count($data['subjects'] ?? []) }}" style="text-align: center;">ບໍ່ມີຂໍ້ມູນນັກຮຽນ
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    @endif

    <div class="footer">
        <p>ວັນທີສ້າງລາຍງານ: {{ date('d/m/Y') }}</p>

        <div class="signature">
            <p>ຜູ້ອຳນວຍການໂຮງຮຽນ</p>
            <br><br><br>
            <p>______________________</p>
        </div>
    </div>
</body>

</html>