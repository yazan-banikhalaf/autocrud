<?php

namespace Yazan\AutoCrud\Generators;

use Illuminate\Support\Str;
use Yazan\AutoCrud\Services\MigrationParser;

class ControllerGenerator
{
    public function generate(string $filePath): void
    {
        $parser = new MigrationParser($filePath);

        $modelGenerator = new ModelGenerator();

        $stub = $this->getStub();

        $tableName = $parser->getMigrationName()[0];

        $controllerName = $this->resolveControllerName($tableName);

        $stub = str_replace('{{ controllerName }}', $controllerName, $stub);
        $stub = str_replace('{{ modelName }}', $modelGenerator->resolveModelName($tableName), $stub);
        $stub = str_replace('{{ importModel }}', $this->getModelPath($modelGenerator->resolveModelName($tableName)), $stub);
        $stub = str_replace('{{ requestName }}', $this->resolveRequestName($tableName), $stub);
        $stub = str_replace('{{ importRequest }}', $this->getRequestPath($tableName), $stub);
        $stub = str_replace('{{ variable }}', $this->resolveVariableName($tableName), $stub);

        file_put_contents(app_path("Http/Controllers/{$controllerName}.php"), $stub);
    }

    public function resolveControllerName(string $tableName): string
    {
        return Str::studly(Str::singular($tableName) . 'Controller');
    }

    private function getStub(): string
    {
        return file_get_contents(__DIR__ . '/../Stubs/controller.stub');
    }

    private function getModelPath(string $modelName): string
    {
        return "App\Models\\" . $modelName . ";";
    }

    private function getRequestPath(string $tableName): string
    {
        return "App\Http\Requests\\" . $this->resolveRequestName($tableName) . ";";
    }

    public function resolveRequestName(string $tableName): string
    {
        return Str::studly(Str::singular($tableName) . 'Request');
    }

    private function resolveVariableName($tableName): string
    {
        return Str::singular($tableName);
    }
}
