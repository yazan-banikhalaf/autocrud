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

    public function getMigrationNames(): array
    {
        $contents = $this->getContent();

        preg_match_all("/Schema::(?:create|table)\(\s*'([^']+)'/", $contents, $matches);

        return $matches[1] ?? [];
    }
    public function getColumns(): array
    {
        $contents = $this->getContent();

        preg_match_all('/\$table->(\w+)\([\'"](\w+)[\'"]\)([^;]*);/', $contents, $matches);

        return $matches;
    }
    private function getContent(): string
    {
        return file_get_contents($this->filePath);
    }
}
