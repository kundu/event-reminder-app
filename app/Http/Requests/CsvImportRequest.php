<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CsvImportRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Assuming any authenticated user can import events
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'csv_file' => 'required|file|mimes:csv,txt',
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $this->validateCsvContent($validator);
        });
    }

    /**
     * Validate the content of the CSV file.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    protected function validateCsvContent($validator)
    {
        if (!$this->hasFile('csv_file')) {
            return;
        }

        $file = $this->file('csv_file');
        $events = array_map('str_getcsv', file($file->getPathname()));
        $header = array_shift($events);

        if ($header !== ['title', 'description', 'start_time', 'end_time']) {
            $validator->errors()->add('csv_file', 'The CSV file must have the correct headers: title, description, start_time, end_time');
            return;
        }

        $eventsData = [];
        foreach ($events as $index => $event) {
            if (count($event) !== 4) {
                $validator->errors()->add('csv_file', "Row " . ($index + 2) . " does not have the correct number of columns");
                continue;
            }

            $eventData = array_combine($header, $event);
            $rowValidator = validator($eventData, [
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'start_time' => 'required|date_format:Y-m-d H:i:s|after:now',
                'end_time' => 'required|date_format:Y-m-d H:i:s|after:start_time',
            ]);

            if ($rowValidator->fails()) {
                foreach ($rowValidator->errors()->all() as $error) {
                    $validator->errors()->add('csv_file', "Row " . ($index + 2) . ": " . $error);
                }
            } else {
                $eventsData[] = $eventData;
            }
        }

        $this->merge(['events' => $eventsData]);
    }
}
