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

        if (! file_exists($filePath)) {
            $this->error('❌ Migration file not found: ' . $filePath);
            $this->error('Expected path: ' . $filePath);
            return 1;
        }

        $this->info('📂 Migration file found.');

        $parser = new MigrationParser($filePath);

        $tableNames = $parser->getMigrationName();

        if (count($tableNames) === 0) {
            $this->warn('⚠️ No tables detected in this migration.');
            return self::FAILURE;
        }

        if (count($tableNames) > 1) {
            $this->error('❌ The migration file must contain exactly one table.');
            return self::FAILURE;
        }

        $this->comment('🔄 Generating model...');

        $modelGenerator = new ModelGenerator();

        $modelGenerator->generate($filePath);

        $this->info('✅ Model added successfully!');

        return 0;
    }
}
