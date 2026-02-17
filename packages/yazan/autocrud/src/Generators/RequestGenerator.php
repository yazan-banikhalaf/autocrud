<?php

namespace Yazan\AutoCrud\Generators;

use Yazan\AutoCrud\Services\MigrationParser;

class RequestGenerator
{
    public function generate($filePath): void
    {
        $parser = new MigrationParser($filePath);

        $controllerGenerator = new ControllerGenerator();

        $tableName = $parser->getMigrationName()[0];

        $stub = $this->getStub();

        $columns = $parser->getColumns();

        $rules = $this->defineRules($columns);

        $stub = str_replace('{{ rules }}', $rules, $stub);

        $stub = str_replace('{{ requestName }}', $controllerGenerator->resolveRequestName($tableName), $stub);

        file_put_contents(app_path("Http/Requests/{$controllerGenerator->resolveRequestName($tableName)}.php"), $stub);
    }

    private function getStub(): string
    {
        return file_get_contents(__DIR__ . '/../Stubs/request.stub');
    }

    private function defineRules($columns)
    {
        $rules = [];

        foreach ($columns as $column) {
            $typeChecker = $column['type'];

            if (str_contains($typeChecker, 'text')) {
                $typeChecker = "string";
            } elseif (str_contains($typeChecker, 'integer') || str_contains($typeChecker, 'foreign')) {
                $typeChecker = "integer";
            } elseif (str_contains($typeChecker, 'decimal')) {
                $typeChecker = "decimal";
            }

            $validationRules = [$column['nullable'] ? 'nullable' : 'required', $typeChecker];

            $rules[] = "'{$column['name']}' => ['" . implode("', '", $validationRules) . "']";
        }

        return "\n        " . implode(",\n        ", $rules) . ",\n    ";
    }
}
