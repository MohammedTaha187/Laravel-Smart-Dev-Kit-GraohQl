<?php

namespace Muhammad\StarterKit\Services;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class DBAnalyzer
{
    // Get all tables in the database
    public function getTables(): array
    {
        return Schema::getTables();
    }

    // Get foreign keys for a specific table
    public function getForeignKeys(string $table): array
    {
        try {
            return Schema::getForeignKeys($table);
        } catch (\Exception $e) {
            return [];
        }
    }

    // Identify relationships based on schema
    public function identifyRelationships(string $table): array
    {
        $relationships = [
            'belongsTo' => [],
            'hasMany' => [],
        ];

        // BelongsTo relationships
        foreach ($this->getForeignKeys($table) as $fk) {
            $foreignTable = $fk['foreign_table'];
            $relationships['belongsTo'][] = [
                'method' => Str::camel(Str::singular($foreignTable)),
                'model' => Str::studly(Str::singular($foreignTable)),
                'foreign_key' => $fk['columns'][0],
                'owner_key' => $fk['foreign_columns'][0],
            ];
        }

        // HasMany relationships
        foreach ($this->getTables() as $otherTableData) {
            $otherTable = $otherTableData['name'];
            if ($otherTable === $table) continue;

            foreach ($this->getForeignKeys($otherTable) as $fk) {
                if ($fk['foreign_table'] === $table) {
                    $relationships['hasMany'][] = [
                        'method' => Str::camel(Str::plural($otherTable)),
                        'model' => Str::studly(Str::singular($otherTable)),
                        'foreign_key' => $fk['columns'][0],
                        'local_key' => $fk['foreign_columns'][0],
                    ];
                }
            }
        }

        return $relationships;
    }

    /**
     * Check if a table is a pivot table by naming convention.
     */
    public function isPivotTable(string $table): bool
    {
        $parts = explode('_', $table);
        if (count($parts) !== 2) return false;

        // Simplified check: if it has 2 foreign keys and is named singular_singular
        $fks = $this->getForeignKeys($table);
        return count($fks) === 2;
    }

    /**
     * Get columns of a table.
     */
    public function getColumns(string $table): array
    {
        return Schema::getColumns($table);
    }

    /**
     * Generate validation rules based on table schema.
     */
    public function generateRules(string $table): array
    {
        $columns = $this->getColumns($table);
        $foreignKeys = $this->getForeignKeys($table);
        $rules = [];

        foreach ($columns as $column) {
            $name = $column['name'];
            
            // Skip auto-incrementing/timestamp columns usually
            if ($name === 'id' || $name === 'created_at' || $name === 'updated_at' || $name === 'deleted_at') {
                continue;
            }

            $colRules = [];

            // Type inference
            $type = strtolower($column['type_name']);
            if (Str::contains($name, 'email')) {
                $colRules[] = 'email';
            }

            if (Str::contains($type, 'varchar') || Str::contains($type, 'string') || Str::contains($type, 'text')) {
                $colRules[] = 'string';
                // Handle max length if available
                if (isset($column['type']) && preg_match('/\((.*)\)/', $column['type'], $matches)) {
                    $colRules[] = "max:{$matches[1]}";
                }
            } elseif (Str::contains($type, 'int')) {
                $colRules[] = 'integer';
            } elseif (Str::contains($type, 'bool') || Str::contains($type, 'tinyint(1)')) {
                $colRules[] = 'boolean';
            }

            // 3. Foreign Keys
            foreach ($foreignKeys as $fk) {
                if ($fk['columns'][0] === $name) {
                    $colRules[] = "exists:{$fk['foreign_table']},{$fk['foreign_columns'][0]}";
                }
            }

            $rules[$name] = $colRules;
        }

        return $rules;
    }
}
