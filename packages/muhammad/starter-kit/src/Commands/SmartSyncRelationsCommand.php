<?php

namespace Muhammad\StarterKit\Commands;

use Illuminate\Console\Command;
use Muhammad\StarterKit\Services\DBAnalyzer;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class SmartSyncRelationsCommand extends Command
{
    protected $signature = 'smart:sync-relations {--module= : The module to sync models for}';
    protected $description = 'Scan DB and sync relationships (belongsTo & hasMany) into Models';

    protected $analyzer;

    public function __construct(DBAnalyzer $analyzer)
    {
        parent::__construct();
        $this->analyzer = $analyzer;
    }

    public function handle()
    {
        $this->info("Scanning models and database schema...");

        $models = $this->getModels();

        if (empty($models)) {
            $this->warn("No models found.");
            return;
        }

        foreach ($models as $modelData) {
            $this->syncModel($modelData);
        }

        $this->info("\nRelationships synced successfully!");
    }

    protected function getModels(): array
    {
        $models = [];

        // Discover app-level models
        if (!$this->option('module')) {
            $appPath = app_path('Models');
            if (File::exists($appPath)) {
                foreach (File::files($appPath) as $file) {
                    $name = $file->getBasename('.php');
                    if ($name === 'User') continue; // Skip standard user
                    $models[] = [
                        'name' => $name,
                        'path' => $file->getRealPath(),
                        'namespace' => 'App\\Models',
                    ];
                }
            }
        }

        // Discover module-level models
        $module = $this->option('module');
        $modulePaths = $module ? [base_path("Modules/{$module}")] : glob(base_path('Modules/*'), GLOB_ONLYDIR);

        foreach ($modulePaths as $path) {
            $moduleName = basename($path);
            $modelPath = "{$path}/app/Models";
            if (File::exists($modelPath)) {
                foreach (File::files($modelPath) as $file) {
                    $models[] = [
                        'name' => $file->getBasename('.php'),
                        'path' => $file->getRealPath(),
                        'namespace' => "Modules\\{$moduleName}\\Models",
                    ];
                }
            }
        }

        return $models;
    }

    protected function syncModel(array $modelData)
    {
        $fullClass = $modelData['namespace'] . "\\" . $modelData['name'];
        
        try {
            $modelInstance = new $fullClass;
            $table = $modelInstance->getTable();
        } catch (\Exception $e) {
            $this->error("Could not instantiate {$fullClass}. Skpping.");
            return;
        }

        $this->info("\nAnalyzing {$modelData['name']} (Table: {$table})...");
        $relationships = $this->analyzer->identifyRelationships($table);

        $content = File::get($modelData['path']);
        $originalContent = $content;

        // Process BelongsTo
        foreach ($relationships['belongsTo'] as $rel) {
            $content = $this->injectRelationship($content, 'belongsTo', $rel, $modelData['name']);
        }

        // Process HasMany
        foreach ($relationships['hasMany'] as $rel) {
            $content = $this->injectRelationship($content, 'hasMany', $rel, $modelData['name']);
        }

        if ($content !== $originalContent) {
            File::put($modelData['path'], $content);
            $this->info("Updated {$modelData['name']} with new relationships.");
        } else {
            $this->line(" - No new relationships to add for {$modelData['name']}.");
        }
    }

    protected function injectRelationship(string $content, string $type, array $rel, string $modelName): string
    {
        $methodName = $rel['method'];
        
        if (Str::contains($content, "function {$methodName}()")) {
            return $content;
        }

        $this->info(" + Adding {$type}: [{$methodName}] to {$modelName}.");

        $returnType = $type === 'belongsTo' ? 'return $this->belongsTo(' : 'return $this->hasMany(';
        $params = $type === 'belongsTo' 
            ? "\\App\\Models\\{$rel['model']}::class, '{$rel['foreign_key']}', '{$rel['owner_key']}'"
            : "\\App\\Models\\{$rel['model']}::class, '{$rel['foreign_key']}', '{$rel['local_key']}'";

        // Defaulting to full-path imports for modular compatibility
        $code = "\n    public function {$methodName}()\n    {\n        {$returnType}{$params});\n    }\n";

        // Insert before the last closing brace
        return preg_replace('/}([^}]*)$/', $code . '}$1', $content);
    }
}
