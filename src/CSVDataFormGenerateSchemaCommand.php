<?php

namespace soc;

use Illuminate\Console\Command;
use soc\CSVDataForm;

class CSVDataFormGenerateSchemaCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'soc:schema {$file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        CSVDataForm::BuildSchemaBlock($this->option('file'));
        return 0;
    }
}
