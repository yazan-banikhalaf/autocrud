<?php

namespace Yazan\AutoCrud\Services;

use Illuminate\Support\Facades\Schema;

class MigrationParser
{
    protected string $filePath;

    public function __construct(string $filePath)
    {
        $this->filePath = $filePath;
    }

    public function getMigrationName(): array
    {
        $contents = $this->getContent();

        preg_match_all("/Schema::(?:create|table)\(\s*'([^']+)'/", $contents, $matches);

        return $matches[1] ?? [];
    }
    public function getColumns(): array
    {
        $contents = $this->getContent();

        preg_match_all('/\$table->(\w+)\([\'"](\w+)[\'"]\)([^;]*);/', $contents, $matches);

        return $this->extractColumnMetadata($matches);
    }
    private function getContent(): string
    {
        return file_get_contents($this->filePath);
    }

    private function extractColumnMetadata(array $matches): array
    {
        $columns = [];
        for ($i = 0; $i < count($matches[1]); $i++) {
            $modifiers = $matches[3][$i];

            $columns[] = [
                'name' => $matches[2][$i],
                'type' => $matches[1][$i],
                'nullable' => str_contains($modifiers, "->nullable()"),
                'unique' => str_contains($modifiers, "->unique()"),
            ];
        }

        return $columns;
    }

    public function hasSoftDelete(): bool
    {
        $content = $this->getContent();

        return str_contains($content, 'deleted_at') || str_contains($content, "->softDeletes();");
    }
}
