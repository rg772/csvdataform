<?php

namespace soc;

use Illuminate\Support\Str;

class CSVDataForm
{
    static public function test()
    {
        return true;
    }

    static public function loadCSV(string $filename): array
    {

        $data = [];

        $expected_types = [
            'text',
            'string',
            'number',
            'link',
            'label',
            'file',
            'boolean',
            'email'
        ];

        $expect_number_of_rows = 2;


        $f = fopen($filename, 'r') or die('file does not exist');

        // define starting section
        $section = 'Start';


        // loop
        while ($row = fgetcsv($f)) {

            // blank line
            if (count($row) < $expect_number_of_rows) {
                continue;
            }

            // parts
            $label = filter_var(trim($row[0]), FILTER_SANITIZE_STRING);
            $type = strtolower(filter_var(trim($row[1]), FILTER_SANITIZE_STRING));
            $required = (Str::endsWith($label, '*'));

            // do we need to auto generate the slug
            if (isset($row[2]) && !is_null($row[2])) {
                $slug = trim(filter_var($row[3]), FILTER_SANITIZE_STRING);
            } else {
                $slug = implode('_', array_slice(explode(' ', $label), 0, 10));
            }

            // tighten up slug
            $slug = trim($slug);
            $slug = str_replace(' ', '_', $slug);
            $slug = str_replace('_*', '', $slug);
            $slug = preg_replace('/[^A-Za-z0-9\-_]/', '', $slug);

            if (array_search($type, $expected_types) === false) {

            };

            // set label if necessary and skip rest
            if ($type == 'label') {
                $section = $label;
                continue;
            }

            $input = self::MakeInitialInputStatement($type, $slug, $label, $required);


            $data[$section][$slug] = [
                'label' => $label,
                'type' => $type,
                'required' => $required,
                'input' => $input
            ];


        }

        return $data;

    }

    /**
     * @param string $type
     * @param string|null $slug
     * @param string $label
     * @param bool $required
     * @return string
     */
    public static function MakeInitialInputStatement(string $type, string $slug, string $label, bool $required): string
    {
        // Create input statement
        $input = "";

        // need text representation here
        $required = $required ? 'true' : 'false';


        switch ($type) {
            case 'string':
            case 'email':
            case 'link':

                if ($type == 'string') $input_type = 'text';
                if ($type == 'email') $input_type = 'email';
                if ($type == 'link') $input_type = 'link';


                $input = sprintf(
                    "<label for='%s'>%s</label>",
                    $slug,
                    $label
                );
                $input .= sprintf(
                    "<input type=\"%s\" placeholder=\"%s\" name=\"%s\" required='%s'/>",
                    $input_type,
                    $label,
                    $slug,
                    $required
                );

                return trim($input);

            case 'number':
                $input = sprintf(
                    "<label for='%s'>%s</label>",
                    $slug,
                    $label
                );
                $input .= sprintf(
                    "<input type=\"number\"> placeholder=\"%s\" name=\"%s\" required='%s'/>",
                    $label,
                    $slug,
                    $required
                );

                return trim($input);

            case 'text':

                $input = sprintf(
                    "<label for='%s'>%s</label>",
                    $slug,
                    $label
                );
                $input .= sprintf(
                    "<textarea placeholder=\"%s\" name=\"%s\" required='%s'> </textarea>",
                    $label,
                    $slug,
                    $required
                );

                return trim($input);

            case 'boolean':

                $input = <<<RADIO
                    <span>$label</span>
                    <input type="radio" name="$slug" id="{$slug}_yes" value="yes" required="$required">
                    <label for="{$slug}_yes">Yes</label>
                    <input type="radio" name="$slug" id="{$slug}_no" value="no"  required="$required">
                    <label for="{$slug}_no">No</label>
                RADIO;

                return trim($input);
        }
        return $input;
    }

}
