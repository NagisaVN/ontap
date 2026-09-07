<?php

namespace App\Livewire\Teacher;

use App\Models\Exam;
use App\Models\Question;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Dashboard extends Component
{
    public function render()
    {
        $userId = Auth::id();

        // ── KPI cards ──────────────────────────────────────────────────────────
        // Tổng số câu hỏi trong hệ thống
        $totalQuestions = Question::count();

        // Câu hỏi mới trong tuần này
        $newThisWeek = Question::where('created_at', '>=', now()->startOfWeek())->count();

        // Câu hỏi đang chờ duyệt
        $pendingCount = Question::where('trang_thai', 'cho_duyet')->count();

        // Câu hỏi chờ duyệt mới từ hôm qua
        $pendingSinceYesterday = Question::where('trang_thai', 'cho_duyet')
            ->where('created_at', '>=', now()->subDay())
            ->count();

        // Đề thi trong hệ thống
        $examCount = Exam::count();

        // Đề thi mới tuần này
        $examThisWeek = Exam::where('created_at', '>=', now()->startOfWeek())->count();

        // ── Recent Activity: câu hỏi mới nhất từ TẤT CẢ GV (10 mục) ──────────
        $recentQuestions = Question::with(['nguoiDung', 'chuong.monHoc'])
            ->latest()
            ->limit(5)
            ->get()
            ->map(function ($q) use ($userId) {
                $user      = $q->nguoiDung;
                $name      = $user?->name ?? 'Không rõ';
                $words     = explode(' ', $name);
                $initials  = strtoupper(
                    substr($words[0] ?? '?', 0, 1) .
                    substr(end($words), 0, 1)
                );
                $isMe      = $user?->id === $userId;

                $subject = $q->chuong?->monHoc?->ten ?? '';
                $chuong  = $q->chuong?->ten ?? '';
                $where   = $subject ? $subject . ($chuong ? ' – ' . $chuong : '') : '';

                // Nguồn: ocr → uploaded via OCR, còn lại → added a question
                $src = $q->nguon?->value ?? $q->nguon ?? 'manual';
                $action = $src === 'ocr'
                    ? 'đã tải lên câu hỏi bằng OCR'
                    : 'đã thêm câu hỏi mới';

                // Màu avatar dựa trên ID
                $colors = [
                    'bg-indigo-100 text-indigo-700',
                    'bg-emerald-100 text-emerald-700',
                    'bg-amber-100 text-amber-700',
                    'bg-rose-100 text-rose-700',
                    'bg-blue-100 text-blue-700',
                    'bg-purple-100 text-purple-700',
                ];
                $color = $colors[($user?->id ?? 0) % count($colors)];

                return [
                    'actor'    => $isMe ? 'Bạn' : $name,
                    'initials' => $initials,
                    'action'   => $action,
                    'subject'  => $where,
                    'time'     => $q->created_at?->diffForHumans() ?? '',
                    'color'    => $color,
                ];
            });

        // Đề thi mới nhất
        $recentExams = Exam::with('nguoiDung')
            ->latest()
            ->limit(3)
            ->get()
            ->map(function ($e) use ($userId) {
                $user      = $e->nguoiDung;
                $name      = $user?->name ?? 'Không rõ';
                $words     = explode(' ', $name);
                $initials  = strtoupper(
                    substr($words[0] ?? '?', 0, 1) .
                    substr(end($words), 0, 1)
                );
                $isMe      = $user?->id === $userId;
                $colors = [
                    'bg-violet-100 text-violet-700',
                    'bg-teal-100 text-teal-700',
                    'bg-orange-100 text-orange-700',
                ];
                $color = $colors[($user?->id ?? 0) % count($colors)];
                return [
                    'actor'    => $isMe ? 'Bạn' : $name,
                    'initials' => $initials,
                    'action'   => 'đã tạo đề thi mới',
                    'subject'  => $e->ten ?? '',
                    'time'     => $e->created_at?->diffForHumans() ?? '',
                    'color'    => $color,
                ];
            });

        // Gộp và sắp xếp activity, giới hạn 5 mục
        $activities = $recentQuestions->concat($recentExams)
            ->sortByDesc(fn($a) => $a['time'])
            ->take(5)
            ->values();

        return view('livewire.teacher.dashboard', compact(
            'totalQuestions', 'newThisWeek',
            'pendingCount', 'pendingSinceYesterday',
            'examCount', 'examThisWeek',
            'activities'
        ));
    }
}
