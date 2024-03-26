<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProjectRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $id = $this->route('project');

        return [
            'title' => ['required', 'string', Rule::unique('projects')->ignore($id), 'min:10', 'max:40'],
            'type_id' => 'nullable|exists:types,id',
            'description' => 'required|string',
            'image' => 'nullable|image|mimes:png,jpg,jpeg',
            'is_completed' => 'nullable|boolean'
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'E\' necessario inserire un titolo',
            'title.unique' => 'E\' già presente un progetto con lo stesso titolo',
            'title.min' => 'Il titolo deve essere almeno di :min caratteri',
            'title.max' => 'Il titolo non può superare i :max caratteri',
            'type_id.exists' => 'La tipologia selezionata non è valida',
            'description.required' => 'E\' necessario inserire una descrizione',
            'image.image' => 'Il file deve essere un immagine',
            'image.mimes' => 'Il file immagine può avere estensioni jpg, jpeg, png',
            'is_completed.boolean' => 'Lo stato deve essere un dato booleano'
        ];
    }
}
