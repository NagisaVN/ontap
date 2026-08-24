<?php

namespace App\Enums;

enum QuestionStatus: string
{
    case ChoDuyet = 'cho_duyet'; // OCR → chờ teacher duyệt
    case DaDuyet  = 'da_duyet';  // Đã được duyệt, có thể dùng trong đề
    case TuChoi   = 'tu_choi';   // Bị từ chối, không dùng

    public function nhanHien(): string
    {
        return match($this) {
            self::ChoDuyet => 'Chờ duyệt',
            self::DaDuyet  => 'Đã duyệt',
            self::TuChoi   => 'Từ chối',
        };
    }

    public function mauSac(): string
    {
        return match($this) {
            self::ChoDuyet => 'yellow',
            self::DaDuyet  => 'green',
            self::TuChoi   => 'red',
        };
    }
}
