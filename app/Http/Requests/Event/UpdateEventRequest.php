<?php

namespace App\Http\Requests\Event;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEventRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'sometimes|string|max:255',
            'description' => 'sometimes|string|max:2000',
            'image' => 'nullable|image|max:2048',
            'min_participants' => 'sometimes|integer|min:1',
            'max_participants' => 'sometimes|integer|min:1|gte:min_participants',
            'event_date' => 'sometimes|date|after:today',
            'start_time' => 'sometimes|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after:start_time',
            'address' => 'sometimes|string|max:255',
            'voting_expiry_time' => 'sometimes|date|after:today|before:event_date',
        ];
    }

    public function messages()
    {
        return [
            'max_participants.gte' => 'Maksimal ishtirokchilar soni minimal ishtirokchilar sonidan katta yoki teng bo\'lishi kerak.',
            'event_date.after' => 'Tadbir sanasi kelajakdagi sana bo\'lishi kerak.',
            'voting_expiry_time.after' => 'Ovoz berish muddati kelajakdagi sana bo\'lishi kerak.',
            'voting_expiry_time.before' => 'Ovoz berish muddati tadbir sanasidan oldin bo\'lishi kerak.',
        ];
    }
}
