<?php

namespace App\Enums;

enum QuestionSource: string
{
    case ThuCong = 'thu_cong'; // Tạo thủ công bởi teacher
    case Ocr     = 'ocr';      // Trích xuất từ PDF/ảnh qua AI
    case AiSinh  = 'ai_sinh';  // Do AI tự sinh (AdaptiveQuestionSynthesis)

    public function nhanHien(): string
    {
        return match($this) {
            self::ThuCong => 'Thủ công',
            self::Ocr     => 'OCR từ PDF/Ảnh',
            self::AiSinh  => 'AI sinh ra',
        };
    }
}
