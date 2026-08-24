<?php

namespace App\Http\Requests;

use App\Enums\ExamMode;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TaoBaiThiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'mon_hoc_id'    => ['required', 'integer', 'exists:mon_hoc,id'],
            'ten_bai_thi'   => ['required', 'string', 'max:255'],
            'che_do'        => ['required', Rule::enum(ExamMode::class)],
            'so_cau_hoi'    => ['required', 'integer', 'min:5', 'max:200'],
            'thoi_gian_phut'=> ['required', 'integer', 'min:5', 'max:180'],

            // Cấu hình ma trận — bắt buộc khi chọn chế độ tỷ lệ tùy chỉnh
            'cau_hinh_ma_tran' => [
                Rule::requiredIf(fn() => $this->che_do === ExamMode::TyLeTuyChon->value),
                'nullable',
                'array',
                'min:1',
            ],
            'cau_hinh_ma_tran.*.chuong_id'  => ['required_with:cau_hinh_ma_tran', 'integer', 'exists:chuong,id'],
            'cau_hinh_ma_tran.*.phan_tram'  => ['required_with:cau_hinh_ma_tran', 'integer', 'min:1', 'max:100'],

            // Cấu hình theo độ khó
            'cau_hinh_do_kho'               => [
                Rule::requiredIf(fn() => $this->che_do === ExamMode::TheoDoKho->value),
                'nullable',
                'array',
            ],
            'cau_hinh_do_kho.de'            => ['nullable', 'integer', 'min:0', 'max:100'],
            'cau_hinh_do_kho.trung_binh'    => ['nullable', 'integer', 'min:0', 'max:100'],
            'cau_hinh_do_kho.kho'           => ['nullable', 'integer', 'min:0', 'max:100'],
        ];
    }

    public function withValidator($validator): void
    {
        // Kiểm tra tổng phần trăm theo chương = 100
        $validator->after(function ($v) {
            if ($this->che_do === ExamMode::TyLeTuyChon->value) {
                $tongPhan = collect($this->cau_hinh_ma_tran ?? [])
                    ->sum('phan_tram');

                if ($tongPhan !== 100) {
                    $v->errors()->add(
                        'cau_hinh_ma_tran',
                        "Tổng phần trăm các chương phải bằng 100%, hiện tại là {$tongPhan}%."
                    );
                }
            }

            if ($this->che_do === ExamMode::TheoDoKho->value) {
                $tongPhan = ($this->cau_hinh_do_kho['de'] ?? 30)
                    + ($this->cau_hinh_do_kho['trung_binh'] ?? 50)
                    + ($this->cau_hinh_do_kho['kho'] ?? 20);

                if ($tongPhan !== 100) {
                    $v->errors()->add(
                        'cau_hinh_do_kho',
                        "Tổng phần trăm dễ/TB/khó phải bằng 100%, hiện tại là {$tongPhan}%."
                    );
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'mon_hoc_id.required'    => 'Vui lòng chọn môn học.',
            'mon_hoc_id.exists'      => 'Môn học không tồn tại.',
            'ten_bai_thi.required'   => 'Vui lòng nhập tên bài thi.',
            'che_do.required'        => 'Vui lòng chọn chế độ thi.',
            'so_cau_hoi.min'         => 'Số câu hỏi tối thiểu là 5 câu.',
            'so_cau_hoi.max'         => 'Số câu hỏi tối đa là 200 câu.',
            'thoi_gian_phut.min'     => 'Thời gian tối thiểu là 5 phút.',
            'thoi_gian_phut.max'     => 'Thời gian tối đa là 180 phút.',
        ];
    }
}
