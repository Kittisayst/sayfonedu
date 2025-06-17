<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class PrintController extends Controller
{
    /**
     * ພິມໃບບິນແບບ HTML
     */
    public function receipt(Payment $payment)
    {
        // ກວດສອບສິດທິການເຂົ້າເຖິງ
        $this->authorize('view', $payment);

        // Load relationships
        $payment->load(['student', 'academicYear', 'discount', 'receiver', 'images']);

        return view('print.receipt', compact('payment'));
    }

    /**
     * ພິມໃບບິນແບບ PDF
     */
    public function receiptPdf(Payment $payment)
    {
        $this->authorize('view', $payment);

        $payment->load(['student', 'academicYear', 'discount', 'receiver', 'images']);

        // ສ້າງ PDF
        $pdf = Pdf::loadView('print.receipt-pdf', compact('payment'))
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'dpi' => 150,
                'defaultFont' => 'NotoSansLao', // ຟອນທ์ສຳລັບພາສາລາວ
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
            ]);

        $filename = "receipt-{$payment->receipt_number}.pdf";

        return $pdf->stream($filename);
    }

    /**
     * ສະແດງຕົວຢ່າງໃບບິນ
     */
    public function receiptPreview(Payment $payment)
    {
        $this->authorize('view', $payment);

        $payment->load(['student', 'academicYear', 'discount', 'receiver', 'images']);

        return view('print.receipt-preview', compact('payment'));
    }

    /**
     * ລາຍງານສະຫຼຸບການຊຳລະ
     */
    public function paymentSummary($from, $to)
    {
        $this->authorize('viewAny', Payment::class);

        $fromDate = Carbon::parse($from)->startOfDay();
        $toDate = Carbon::parse($to)->endOfDay();

        $payments = Payment::with(['student', 'receiver'])
            ->whereBetween('payment_date', [$fromDate, $toDate])
            ->confirmed()
            ->orderBy('payment_date')
            ->get();

        $summary = [
            'total_amount' => $payments->sum('total_amount'),
            'total_cash' => $payments->sum('cash'),
            'total_transfer' => $payments->sum('transfer'),
            'total_count' => $payments->count(),
            'date_range' => [
                'from' => $fromDate->format('d/m/Y'),
                'to' => $toDate->format('d/m/Y')
            ]
        ];

        return view('print.payment-summary', compact('payments', 'summary'));
    }

    /**
     * ໃບແຈ້ງໜີ້ນັກຮຽນ
     */
    public function studentStatement(Student $student)
    {
        $this->authorize('view', $student);

        $student->load([
            'payments' => function ($query) {
                $query->confirmed()->orderBy('payment_date', 'desc');
            }
        ]);

        $academicYear = $student->enrollments->first()?->academicYear;

        return view('print.student-statement', compact('student', 'academicYear'));
    }

    /**
     * Helper: ຂໍ້ມູນໂຮງຮຽນ
     */
    protected function getSchoolInfo(): array
    {
        return [
            'name_lao' => config('school.name_lao', 'ໂຮງຮຽນເອກະຊົນໄຊຟອນ'),
            'name_en' => config('school.name_en', 'Sayfone Private School'),
            'address' => config('school.address', 'ບ້ານ..., ເມືອງ..., ແຂວງວຽງຈັນ'),
            'phone' => config('school.phone', '020 XXXX XXXX'),
            'email' => config('school.email', 'info@sayfone.edu.la'),
            'logo' => config('school.logo', '/images/logo.png'),
            'website' => config('school.website', 'www.sayfone.edu.la'),
        ];
    }
}