<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\TeacherReportService;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class TeacherReportExportController extends Controller
{
    public function __invoke(Request $request, string $format, TeacherReportService $reports)
    {
        abort_unless(in_array($format, ['pdf', 'excel'], true), 404);

        $filters = $request->validate([
            'subject_id' => ['nullable', 'integer', 'exists:mon_hoc,id'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
            'fail_threshold' => ['nullable', 'integer', 'min:0', 'max:100'],
        ]);

        /** @var User $user */
        $user = $request->user();
        $report = $reports->build($user, $filters);
        $filename = 'bao-cao-lop-hoc-'.now()->format('Ymd-His');

        return $format === 'pdf'
            ? $this->pdf($report, $filename)
            : $this->excel($report, $filename);
    }

    private function pdf(array $report, string $filename)
    {
        $options = new Options;
        $options->setDefaultFont('DejaVu Sans');
        $options->setIsRemoteEnabled(false);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml(view('exports.teacher-report-pdf', compact('report'))->render(), 'UTF-8');
        $dompdf->setPaper('a4', 'landscape');
        $dompdf->render();

        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => "attachment; filename=\"{$filename}.pdf\"",
        ]);
    }

    private function excel(array $report, string $filename)
    {
        $spreadsheet = new Spreadsheet;
        $spreadsheet->removeSheetByIndex(0);

        $summary = $spreadsheet->createSheet()->setTitle('Tổng quan');
        $summary->fromArray([
            ['BÁO CÁO LỚP & KẾT QUẢ HỌC TẬP'],
            ['Môn học', $report['subjectName']],
            ['Thời gian', $report['periodLabel']],
            ['Nhóm dữ liệu', $report['cohortLabel']],
            [],
            ['Học viên', 'Lượt thi', 'Điểm trung bình', 'Tỷ lệ đạt'],
            [
                $report['summary']['students'],
                $report['summary']['attempts'],
                $report['summary']['averageScore'].'%',
                $report['summary']['passRate'].'%',
            ],
            [],
            ['PHÂN BỐ ĐIỂM'],
            ['Khoảng điểm', 'Số lượt thi', 'Tỷ lệ'],
        ]);
        foreach ($report['scoreDistribution'] as $bucket) {
            $summary->fromArray([[$bucket['label'], $bucket['count'], $bucket['percentage'].'%']], null, 'A'.($summary->getHighestRow() + 1));
        }
        $this->styleSheet($summary, [1, 6, 9, 10]);
        $summary->mergeCells('A1:D1');

        $roster = $spreadsheet->createSheet()->setTitle('Danh sách học viên');
        $roster->fromArray([['Hạng', 'Học viên', 'Điểm TB', 'Số bài', 'Trạng thái']], null, 'A1');
        foreach ($report['roster'] as $student) {
            $roster->fromArray([[
                '#'.$student['rank'],
                $student['name'],
                $student['score'].'%',
                $student['exams'],
                $student['status'] === 'active' ? 'Đang hoạt động' : 'Không hoạt động',
            ]], null, 'A'.($roster->getHighestRow() + 1));
        }
        $this->styleSheet($roster, [1]);

        $items = $spreadsheet->createSheet()->setTitle('Phân tích câu hỏi');
        $items->fromArray([['Câu hỏi', 'Chủ đề', 'Độ khó', 'Lượt trả lời', 'Số lần sai', 'Tỷ lệ sai', 'Luyện tập', 'Tỷ lệ sai luyện tập']], null, 'A1');
        foreach ($report['difficultyAnalysis'] as $item) {
            $items->fromArray([[
                $item['question'],
                $item['topic'],
                $item['difficulty'],
                $item['responses'],
                $item['wrongCount'],
                $item['failRate'].'%',
                $item['practiceAttempts'],
                $item['practiceFailRate'] === null ? '—' : $item['practiceFailRate'].'%',
            ]], null, 'A'.($items->getHighestRow() + 1));
        }
        $this->styleSheet($items, [1]);
        $items->getColumnDimension('A')->setWidth(58);
        $items->getColumnDimension('B')->setWidth(24);

        return response()->streamDownload(function () use ($spreadsheet) {
            (new Xlsx($spreadsheet))->save('php://output');
        }, "{$filename}.xlsx", [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    private function styleSheet($sheet, array $headerRows): void
    {
        foreach ($headerRows as $row) {
            $range = "A{$row}:{$sheet->getHighestColumn()}{$row}";
            $sheet->getStyle($range)->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
            $sheet->getStyle($range)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('4F46E5');
        }

        foreach (range('A', $sheet->getHighestColumn()) as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        $sheet->getStyle($sheet->calculateWorksheetDimension())->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle($sheet->calculateWorksheetDimension())->getAlignment()->setWrapText(true);
    }
}
