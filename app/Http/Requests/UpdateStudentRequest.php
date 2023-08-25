<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStudentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {     
        $studentId = $this->route('student')->id;
        return [
            'jmbag' => 'required|unique:students,jmbag,' . $studentId,
            'user_id' => 'required|unique:students,user_id,' . $studentId . '|exists:users,id'
        ];
    }
}
