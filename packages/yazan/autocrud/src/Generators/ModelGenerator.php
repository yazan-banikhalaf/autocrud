<?php

namespace Yazan\AutoCrud\Generators;

use Yazan\AutoCrud\Services\MigrationParser;
use Illuminate\Support\Str;

class ModelGenerator
{
    public function addFileContent(string $filePath): void
    {
        $parser = new MigrationParser($filePath);

        $tableName = $parser->getMigrationName()[0];
        $modelName = $this->resolveModelName($tableName);
        $fillable  = $this->buildFillable($parser->getColumns());
        $hasSoftDeletes = $parser->hasSoftDelete();

        $stub = $this->getStub();

        $replacements = [
            '{{ namespace }}' => 'App\Models;',
            '{{ className }}' => $modelName,
            '{{ tableName }}' => $tableName,
            '{{ fillable }}'  => $fillable,
        ];

        if ($hasSoftDeletes) {
            $replacements['{{ softDeletesTrait }}'] = 'use SoftDeletes;';
            $replacements['{{ softDeletesImport }}'] = 'use Illuminate\Database\Eloquent\SoftDeletes;';
        } else {
            $replacements['{{ softDeletesTrait }}'] = '';
            $replacements['{{ softDeletesImport }}'] = '';
        }

        $content = $this->replacePlaceholders($stub, $replacements);

        $content = preg_replace("/^[ \t]+$/m", "", $content);
        $content = preg_replace("/\n{3,}/", "\n\n", $content);

        file_put_contents(app_path("Models/{$modelName}.php"), $content);
    }

    private function getStub(): string
    {
        return file_get_contents(__DIR__ . '/../Stubs/model.stub');
    }

    private function resolveModelName(string $tableName): string
    {
        return Str::studly(Str::singular($tableName));
    }

    private function buildFillable(array $columns): string
    {
        $names = collect($columns)->pluck('name')->toArray();

        return "'" . implode("', '", $names) . "'";
    }

    private function replacePlaceholders(string $stub, array $replacements): string
    {
        return str_replace(
            array_keys($replacements),
            array_values($replacements),
            $stub
        );
    }
}
