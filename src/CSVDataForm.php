<?php

namespace soc;

use App\Console\Kernel;
use App\Providers\AppServiceProvider;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Symfony\Component\Console\Output\ConsoleOutput;


class CSVDataFormServiceProvider extends ServiceProvider {

    protected $newCommands = [
        CSVDataFormGenerateSchemaCommand::class
    ];


    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->commands($this->newCommands);
    }

    /**
     * Register the 'eternaltree:install' command.
     *
     * @return void
     */
    protected function registerInstallCommand()
    {
       $this->register();
    }

}



class CSVDataForm
{


    static public function test()
    {
        return 'This is from the test() function.';
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
            if ((isset($row[2])) && (Str::length($row[2]) > 2)) {
                $slug = trim(filter_var($row[2]), FILTER_SANITIZE_STRING);
            } else {
                $slug = implode('_', array_slice(explode(' ', $label), 0, 10));
            }

            //Clean up slug
            $slug = self::CleanUpTextValue($slug);

            // Halt everything if we have a bad/unexpected data type
            if (array_search($type, $expected_types) === false) {
                throw new \Exception("There was a bad data type. Export ended.");
            };

            // set label if necessary and skip rest
            if ($type == 'label') {
                $section = $label;
                continue;
            }

            $input = self::MakeInitialInputStatement($type, $slug, $label, $required);
            $schema = self::MakeInitialSchemaStatement($type, $slug, $label, $required);


            $data[$section][$slug] = [
                'label' => $label,
                'type' => $type,
                'required' => $required,
                'input' => $input,
                'schema' => $schema,
            ];


        }

        return $data;

    }


    static public function BuildSchemaBlock(string $filename)
    {
        $data = self::loadCSV($filename);

        $toReturn = '';

        foreach ($data as $section_label => $section) {
            $toReturn .= "// $section_label " . "\n";

            foreach ($section as $item) {
                $toReturn .= $item['schema'] . "\n";
            }
        }

        return $toReturn;

    }

    /**
     * @param string $type
     * @param string|null $slug
     * @param string $label
     * @param bool $required
     * @return string
     */
    private static function MakeInitialInputStatement(string $type, string $slug, string $label, bool $required): string
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

                break;

            case 'number':
                $input = sprintf(
                    "<label for='%s'>%s</label>",
                    $slug,
                    $label
                );
                $input .= sprintf(
                    "<input type=\"number\" placeholder=\"%s\" name=\"%s\" required='%s'/>",
                    $label,
                    $slug,
                    $required
                );

                break;

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
                break;

            // FILE
            case 'file':

                $input = sprintf(
                    "<label for='%s'>%s</label>",
                    $slug,
                    $label
                );
                $input .= sprintf(
                    "<input type=\"file\" placeholder=\"%s\" name=\"%s\" required='%s'/>",
                    $label,
                    $slug,
                    $required
                );


                break;

            case 'boolean':

                $input = <<<RADIO
                    <span>$label</span>
                    <input type="radio" name="$slug" id="{$slug}_yes" value="yes" required="$required">
                    <label for="{$slug}_yes">Yes</label>
                    <input type="radio" name="$slug" id="{$slug}_no" value="no"  required="$required">
                    <label for="{$slug}_no">No</label>
                RADIO;

                break;
        }
        return trim($input);
    }


    /**
     * @param string $incoming
     * @return string|string[]|null
     */
    private static function CleanUpTextValue(string $incoming)
    {

        $incoming = trim($incoming);    // trim
        $incoming = str_replace(' ', '_', $incoming); // take out space
        $incoming = str_replace('_*', '', $incoming); // take out ending asterisks
        $incoming = preg_replace('/[^A-Za-z0-9\-_]/', '', $incoming); // filter down characters
        $incoming = strtolower($incoming);
        return $incoming;
    }


    public static function BuildFormSegment(string $filename)
    {

        $toReturn = '';

        $data = self::loadCSV($filename);

        foreach ($data as $label => $section) {
            $toReturn .= sprintf("<div class='form-header'>%s</div>", $label);

            foreach ($section as $key => $items) {

                $formatted_label_input = str_replace("</label><input", "</label> \n \t\t <input", $items['input']);

                $toReturn .= sprintf(
                    "\t<div class='%s'> \n\t\t %s \n\t </div>",
                    $key,
                    $formatted_label_input
                );
            }

            $toReturn .= "<div>";

        }

        return $toReturn;

    }


    private static function MakeInitialSchemaStatement($type, $slug, $label, $required): string
    {
        $toReturn = '';

        switch ($type) {
            case 'string':
            case 'email':
            case 'link':
            case 'file':
                $toReturn = sprintf(
                    " \$table->string('%s')",
                    $slug
                );
                break;

            case 'number':
                $toReturn = sprintf(
                    " \$table->interger('%s')",
                    $slug
                );
                break;

            case 'text':
                $toReturn = sprintf(
                    " \$table->text('%s')",
                    $slug
                );
                break;

            case 'boolean':
                $toReturn = sprintf(
                    " \$table->boolean('%s')",
                    $slug
                );
                break;


            default:
                $toReturn = "// insert manually";

        }


        // Determine if Not required then add the nullable()
        if (!$required) {
            $toReturn .= "->nullable()";
        }

        // Add on ;
        $toReturn = trim($toReturn) . ';';

        return $toReturn;

    }

    static private function out(string $out)
    {
        (new ConsoleOutput())->write($out . PHP_EOL);
    }
}
