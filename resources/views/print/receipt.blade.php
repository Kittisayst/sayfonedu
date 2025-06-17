<!DOCTYPE html>
<html lang="lo">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ໃບບິນຮັບເງິນ - {{ $payment->receipt_number }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Lao:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Noto Sans Lao', sans-serif;
            font-size: 14px;
            line-height: 1.6;
            color: #333;
            background: white;
        }

        .receipt-container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background: white;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .header {
            text-align: center;
            border-bottom: 3px solid #2563eb;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .school-logo {
            width: 80px;
            height: 80px;
            margin: 0 auto 15px;
            border-radius: 50%;
            object-fit: cover;
        }

        .school-name {
            font-size: 24px;
            font-weight: 700;
            color: #1e40af;
            margin-bottom: 5px;
        }

        .school-name-en {
            font-size: 16px;
            color: #6b7280;
            margin-bottom: 10px;
        }

        .school-contact {
            font-size: 12px;
            color: #6b7280;
            line-height: 1.4;
        }

        .receipt-title {
            text-align: center;
            font-size: 20px;
            font-weight: 600;
            color: #dc2626;
            margin: 20px 0;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .receipt-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }

        .info-group {
            background: #f8fafc;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #3b82f6;
        }

        .info-label {
            font-weight: 600;
            color: #374151;
            margin-bottom: 5px;
            font-size: 13px;
        }

        .info-value {
            font-size: 14px;
            color: #111827;
        }

        .student-section {
            background: #f0f9ff;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 25px;
            border: 1px solid #e0f2fe;
        }

        .section-title {
            font-size: 16px;
            font-weight: 600;
            color: #0369a1;
            margin-bottom: 15px;
            border-bottom: 2px solid #bae6fd;
            padding-bottom: 5px;
        }

        .student-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        .payment-details {
            background: #fefce8;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 25px;
            border: 1px solid #fef3c7;
        }

        .payment-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        .payment-table th,
        .payment-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }

        .payment-table th {
            background: #f9fafb;
            font-weight: 600;
            color: #374151;
            font-size: 13px;
        }

        .payment-table .amount {
            text-align: right;
            font-weight: 500;
        }

        .total-row {
            background: #dcfce7;
            font-weight: 600;
            color: #166534;
        }

        .months-section {
            background: #f0fdf4;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 25px;
            border: 1px solid #dcfce7;
        }

        .months-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-top: 15px;
        }

        .month-list {
            background: white;
            padding: 15px;
            border-radius: 8px;
            border: 1px solid #d1fae5;
        }

        .month-list h4 {
            font-size: 14px;
            font-weight: 600;
            color: #065f46;
            margin-bottom: 10px;
        }

        .month-list ul {
            list-style: none;
            padding: 0;
        }

        .month-list li {
            padding: 4px 0;
            color: #047857;
            font-size: 13px;
        }

        .month-list li:before {
            content: "✓ ";
            color: #10b981;
            font-weight: bold;
            margin-right: 5px;
        }

        .footer-section {
            margin-top: 40px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
        }

        .signature-box {
            text-align: center;
            padding: 20px;
            border: 1px dashed #d1d5db;
            border-radius: 8px;
            background: #fafafa;
        }

        .signature-label {
            font-weight: 600;
            color: #374151;
            margin-bottom: 50px;
        }

        .signature-line {
            border-top: 1px solid #6b7280;
            margin-top: 50px;
            padding-top: 10px;
            font-size: 12px;
            color: #6b7280;
        }

        .note-section {
            margin-top: 20px;
            padding: 15px;
            background: #fef7f0;
            border-left: 4px solid #f97316;
            border-radius: 4px;
        }

        .note-label {
            font-weight: 600;
            color: #ea580c;
            margin-bottom: 5px;
        }

        .print-info {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            font-size: 11px;
            color: #9ca3af;
        }

        /* Print Styles */
        @media print {
            body {
                margin: 0;
                padding: 0;
            }

            .receipt-container {
                box-shadow: none;
                margin: 0;
                max-width: 100%;
            }

            .no-print {
                display: none !important;
            }
        }

        /* Responsive */
        @media (max-width: 768px) {

            .receipt-info,
            .student-info,
            .months-grid,
            .footer-section {
                grid-template-columns: 1fr;
            }

            .receipt-container {
                margin: 10px;
                padding: 15px;
            }
        }
    </style>
</head>

<body>
    <div class="receipt-container">
        <!-- Header -->
        <div class="header">
            <img src="{{ asset('images/logo.png') }}" alt="Logo" class="school-logo">
            <div class="school-name">ໂຮງຮຽນເອກະຊົນໄຊຟອນ</div>
            <div class="school-name-en">Sayfone Private School</div>
            <div class="school-contact">
                ບ້ານ..., ເມືອງ..., ແຂວງວຽງຈັນ<br>
                ໂທ: 020 XXXX XXXX | Email: info@sayfone.edu.la
            </div>
        </div>

        <!-- Receipt Title -->
        <div class="receipt-title">ໃບບິນຮັບເງິນ</div>

        <!-- Receipt Info -->
        <div class="receipt-info">
            <div class="info-group">
                <div class="info-label">ເລກທີ່ໃບບິນ:</div>
                <div class="info-value">{{ $payment->receipt_number }}</div>
            </div>
            <div class="info-group">
                <div class="info-label">ວັນທີອອກໃບບິນ:</div>
                <div class="info-value">{{ $payment->getFormattedDate() }}</div>
            </div>
            <div class="info-group">
                <div class="info-label">ສົກຮຽນ:</div>
                <div class="info-value">{{ $payment->academicYear->year_name ?? 'N/A' }}</div>
            </div>
            <div class="info-group">
                <div class="info-label">ສະຖານະ:</div>
                <div class="info-value">{{ $payment->getStatusLabel() }}</div>
            </div>
        </div>

        <!-- Student Information -->
        <div class="student-section">
            <div class="section-title">ຂໍ້ມູນນັກຮຽນ</div>
            <div class="student-info">
                <div class="info-group">
                    <div class="info-label">ລະຫັດນັກຮຽນ:</div>
                    <div class="info-value">{{ $payment->student->student_code }}</div>
                </div>
                <div class="info-group">
                    <div class="info-label">ຊື່ ແລະ ນາມສະກຸນ:</div>
                    <div class="info-value">{{ $payment->student->getFullName() }}</div>
                </div>
                <div class="info-group">
                    <div class="info-label">ຫ້ອງຮຽນ:</div>
                    <div class="info-value">
                        {{ $payment->student->enrollments->first()?->schoolClass?->class_name ?? 'N/A' }}
                    </div>
                </div>
                <div class="info-group">
                    <div class="info-label">ຜູ້ປົກຄອງ:</div>
                    <div class="info-value">
                        {{ $payment->student->parents->first()?->getFullName() ?? 'N/A' }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Details -->
        <div class="payment-details">
            <div class="section-title">ລາຍລະອຽດການຊຳລະ</div>
            <table class="payment-table">
                <thead>
                    <tr>
                        <th>ລາຍການ</th>
                        <th class="amount">ຈຳນວນເງິນ (ກີບ)</th>
                    </tr>
                </thead>
                <tbody>
                    @if($payment->cash > 0)
                        <tr>
                            <td>ເງິນສົດ</td>
                            <td class="amount">{{ number_format($payment->cash, 0, '.', ',') }}</td>
                        </tr>
                    @endif

                    @if($payment->transfer > 0)
                        <tr>
                            <td>ເງິນໂອນ</td>
                            <td class="amount">{{ number_format($payment->transfer, 0, '.', ',') }}</td>
                        </tr>
                    @endif

                    @if($payment->food_money > 0)
                        <tr>
                            <td>ຄ່າອາຫານ</td>
                            <td class="amount">{{ number_format($payment->food_money, 0, '.', ',') }}</td>
                        </tr>
                    @endif

                    @if($payment->late_fee > 0)
                        <tr>
                            <td>ຄ່າປັບຊ້າ</td>
                            <td class="amount">{{ number_format($payment->late_fee, 0, '.', ',') }}</td>
                        </tr>
                    @endif

                    @if($payment->discount_amount > 0)
                        <tr style="color: #dc2626;">
                            <td>ສ່ວນຫຼຸດ ({{ $payment->discount->discount_name ?? 'N/A' }})</td>
                            <td class="amount">-{{ number_format($payment->discount_amount, 0, '.', ',') }}</td>
                        </tr>
                    @endif

                    <tr class="total-row">
                        <td><strong>ລວມທັງໝົດ</strong></td>
                        <td class="amount"><strong>{{ number_format($payment->total_amount, 0, '.', ',') }}</strong>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Months Paid -->
        <div class="months-section">
            <div class="section-title">ເດືອນທີ່ຊຳລະ</div>
            <div class="months-grid">
                @if(!empty($payment->tuition_months))
                    <div class="month-list">
                        <h4>ເດືອນຄ່າຮຽນ ({{ count($payment->tuition_months) }} ເດືອນ)</h4>
                        <ul>
                            @foreach($payment->tuition_months as $month)
                                <li>{{ $month }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if(!empty($payment->food_months))
                    <div class="month-list">
                        <h4>ເດືອນຄ່າອາຫານ ({{ count($payment->food_months) }} ເດືອນ)</h4>
                        <ul>
                            @foreach($payment->food_months as $month)
                                <li>{{ $month }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        </div>

        <!-- Note -->
        @if($payment->note)
            <div class="note-section">
                <div class="note-label">ໝາຍເຫດ:</div>
                <div>{{ $payment->note }}</div>
            </div>
        @endif

        <!-- Footer -->
        <div class="footer-section">
            <div class="signature-box">
                <div class="signature-label">ຜູ້ຊຳລະເງິນ</div>
                <div class="signature-line">
                    ລາຍເຊັນ: .............................<br>
                    ວັນທີ: {{ now()->format('d/m/Y') }}
                </div>
            </div>
            <div class="signature-box">
                <div class="signature-label">ຜູ້ຮັບເງິນ</div>
                <div class="signature-line">
                    {{ $payment->receiver->name ?? 'N/A' }}<br>
                    ວັນທີ: {{ $payment->getFormattedDateOnly() }}
                </div>
            </div>
        </div>

        <!-- Print Info -->
        <div class="print-info">
            ພິມເມື່ອ: {{ now()->format('d/m/Y H:i:s') }} | ລະບົບຈັດການໂຮງຮຽນໄຊຟອນ v1.0
        </div>

        <!-- Print Button (Hidden when printing) -->
        <div class="no-print" style="text-align: center; margin-top: 30px;">
            <button onclick="window.print()" style="
                background: #3b82f6;
                color: white;
                border: none;
                padding: 12px 24px;
                border-radius: 6px;
                font-size: 14px;
                cursor: pointer;
                margin-right: 10px;
            ">🖨️ ພິມໃບບິນ</button>

            <a href="{{ route('print.receipt.pdf', $payment) }}" style="
                background: #dc2626;
                color: white;
                text-decoration: none;
                padding: 12px 24px;
                border-radius: 6px;
                font-size: 14px;
                display: inline-block;
            ">📄 ດາວໂຫຼດ PDF</a>
        </div>
    </div>

    <script>
        // Auto print option
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('autoprint') === '1') {
            window.onload = function () {
                setTimeout(() => {
                    window.print();
                }, 500);
            };
        }
    </script>
</body>

</html>