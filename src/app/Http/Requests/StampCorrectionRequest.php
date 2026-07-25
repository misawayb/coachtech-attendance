<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Carbon\Carbon;

class StampCorrectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'clock_in' => ['nullable', 'date_format:H:i'],
            'clock_out' => ['nullable', 'date_format:H:i'],
            'breaks.*.break_in' => ['nullable', 'date_format:H:i'],
            'breaks.*.break_out' => ['nullable', 'date_format:H:i'],
            'comment' => ['required'],
        ];
    }

    public function messages(): array
    {
        return [
            'comment.required' => '備考を記入してください',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $clockIn = $this->input('clock_in');
            $clockOut = $this->input('clock_out');

            $clockInTime = $clockIn ? Carbon::createFromFormat('H:i', $clockIn) : null;
            $clockOutTime = $clockOut ? Carbon::createFromFormat('H:i', $clockOut) : null;

            if ($clockInTime && $clockOutTime && $clockInTime->gte($clockOutTime)) {
                $validator->errors()->add('clock_in', '出勤時間もしくは退勤時間が不適切な値です');
            }

            foreach ($this->input('breaks', []) as $index => $break) {
                $breakIn = $break['break_in'] ?? null;
                $breakOut = $break['break_out'] ?? null;

                if ($breakIn) {
                    $breakInTime = Carbon::createFromFormat('H:i', $breakIn);

                    if (($clockInTime && $breakInTime->lt($clockInTime)) || ($clockOutTime && $breakInTime->gt($clockOutTime))) {
                        $validator->errors()->add("breaks.$index.break_in", '休憩時間が不適切な値です');
                    }
                }

                if ($breakOut && $clockOutTime) {
                    $breakOutTime = Carbon::createFromFormat('H:i', $breakOut);

                    if ($breakOutTime->gt($clockOutTime)) {
                        $validator->errors()->add("breaks.$index.break_out", '休憩時間もしくは退勤時間が不適切な値です');
                    }
                }
            }
        });
    }
}
