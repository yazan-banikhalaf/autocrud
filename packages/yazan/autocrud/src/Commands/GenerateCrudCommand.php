<?php

namespace Yazan\AutoCrud\Commands;

use Illuminate\Console\Command;
use Log;
use Yazan\AutoCrud\Generators\ModelGenerator;
use Yazan\AutoCrud\Services\MigrationParser;

class GenerateCrudCommand extends Command
{
    protected $signature = 'autocrud:generate {MigrationFileName}';

    protected $description = 'Generate CRUD files from the migration';

    public function handle()
    {
        $fileName = $this->argument('MigrationFileName');
        $filePath = base_path('database/migrations/' . $fileName);
        $parser = new MigrationParser($filePath);
        $modelGenerator = new ModelGenerator();

        if (! file_exists($filePath)) {
            $this->error('❌ Migration file not found: ' . $filePath);
            $this->error('Expected path: ' . $filePath);
            return 1;
        }

        $tableNames = $parser->getMigrationName();
        $tableColumns = $parser->getColumns();
        Log::info($modelGenerator->editFileContent($filePath));
        if (count($tableNames) == 0 || count($tableNames) > 1) {
            $this->error('The migration file needs to have exactly one table');
            return 1;
        }

        $this->info('✅ Migration file found!');

        return 0;
    }
}
