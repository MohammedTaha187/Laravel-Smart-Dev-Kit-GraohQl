<?php

namespace EasyDev\Laravel\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use EasyDev\Laravel\Services\DBAnalyzer;

class SmartCrudCommand extends Command
{
    protected $signature = 'smart:crud {name}
                            {--with-service : Create a service class}
                            {--with-repository : Create a repository class}
                            {--with-data : Create a Spatie Data object (Type-Safe)}
                            {--with-contracts : Generate interfaces/contracts for services and repositories}
                            {--module= : Generate code inside a specific module}
                            {--with-policy : Create a policy}
                            {--with-tests : Create tests}
                            {--with-media : Add media support using Spatie Media Library}
                            {--with-event : Generate model events}
                            {--with-notification : Generate a default notification class}
                            {--with-logs : Add activity logging}
                            {--soft-delete : Add soft delete support}
                            {--force : Overwrite existing files without confirmation}
                            {--translatable= : Comma-separated list of translatable fields}';

    protected $description = 'Generate a complete CRUD feature set (11 files)';

    protected string $model;
    protected ?string $module;
    protected array $config;
    protected DBAnalyzer $analyzer;

    public function __construct(DBAnalyzer $analyzer)
    {
        parent::__construct();
        $this->analyzer = $analyzer;
    }

    public function handle(): void
    {
        $this->model  = $this->argument('name');
        $this->module = $this->option('module');
        $this->config = config('starter-kit');

        $this->info("🚀 Generating CRUD for [{$this->model}]" . ($this->module ? " in module [{$this->module}]" : '') . '...');
        $this->newLine();

        if ($this->module) {
            $this->initializeModule();
        }

        // Layer 1 — Model + Migration
        $this->generateModel();
        $this->generateMigration();

        // Layer 2 — Service
        $this->generateService();

        // Layer 3 — Repository
        $this->generateRepository();

        // Layer 4 — DTO + Policy + Test
        $this->generateData();
        $this->generatePolicy();
        $this->generateFactory();
        $this->generateTest();

        // Optional extras
        if ($this->option('with-notification')) {
            $this->generateNotification();
        }
        if ($this->option('with-event')) {
            $this->generateEvent();
        }

        // GraphQL Schema registration
        $this->registerGraphQLSchema();

        $this->newLine();
        $this->info("✅ CRUD for [{$this->model}] generated successfully!");
        $this->newLine();
        $this->warn('⚡ Remember to:');
        $this->line('  1. Add bindings to RepositoryServiceProvider');
        $this->line('  2. Run: php artisan migrate');
        $this->line('  3. Grant permissions for the new model policies');
    }

    // ──────────────────────────────────────────────────
    // Path & Namespace Helpers
    // ──────────────────────────────────────────────────

    protected function getBasePath(): string
    {
        return $this->module
            ? base_path("Modules/{$this->module}")
            : app_path();
    }

    protected function getBaseNamespace(): string
    {
        return $this->module
            ? "Modules\\{$this->module}"
            : 'App';
    }

    /**
     * Api/V1/{Type} or Api/V1/{Model} sub-path.
     */
    protected function getApiSubPath(?string $type = null): string
    {
        $base = 'Api/V1';
        return $type ? "{$base}/{$type}" : "{$base}/{$this->model}";
    }

    protected function getApiSubNamespace(?string $type = null): string
    {
        $base = 'Api\\V1';
        return $type ? "{$base}\\{$type}" : "{$base}\\{$this->model}";
    }

    // ──────────────────────────────────────────────────
    // Layer 1 — Model + Migration
    // ──────────────────────────────────────────────────

    protected function generateModel(): void
    {
        $basePath = $this->getBasePath();
        $path     = "{$basePath}/Models/{$this->model}.php";
        $stub     = File::get(__DIR__ . '/../../stubs/model.stub');

        $namespace = $this->getBaseNamespace() . '\\Models';

        $table     = Str::snake(Str::pluralStudly($this->model));
        $columns   = array_column($this->analyzer->getColumns($table), 'name');

        $factoryNs = $this->getBaseNamespace() . "\\Database\\Factories\\{$this->model}";
        $factoryClass = "{$this->model}Factory";

        $relData = $this->getRelationshipData($table);

        $replacements = [
            '{{Namespace}}'         => $namespace,
            '{{Class}}'             => $this->model,
            '{{FactoryImport}}'     => "use {$factoryNs}\\{$factoryClass};",
            '{{FactoryDoc}}'        => "/** @use HasFactory<{$factoryClass}> */",
            '{{RelationshipImports}}' => $relData['imports'],
            '{{Relationships}}'     => $relData['methods'],
            '{{SoftDeletesImport}}' => $this->option('soft-delete')
                ? 'use Illuminate\\Database\\Eloquent\\SoftDeletes;'
                : '',
            '{{SoftDeletesTrait}}'  => $this->option('soft-delete')
                ? ', SoftDeletes'
                : '',
            '{{MediaImports}}'      => $this->option('with-media')
                ? "use Spatie\\MediaLibrary\\HasMedia;\nuse Spatie\\MediaLibrary\\InteractsWithMedia;"
                : '',
            '{{MediaImplements}}'   => $this->option('with-media') ? 'implements HasMedia' : '',
            '{{MediaTrait}}'        => $this->option('with-media') ? ', InteractsWithMedia' : '',
            '{{TranslationImport}}' => $this->option('translatable')
                ? 'use Spatie\\Translatable\\HasTranslations;'
                : '',
            '{{TranslationTrait}}'  => $this->option('translatable') ? ', HasTranslations' : '',
            '{{TranslatableFields}}' => $this->getTranslatableFields(),
            '{{EnterpriseTraits}}'  => $this->getEnterpriseTraits(),
        ];

        $this->createFile($path, $stub, $replacements);
    }

    protected function getRelationshipData(string $table): array
    {
        $relationships = $this->analyzer->identifyRelationships($table);
        $methods = [];
        $imports = [];

        foreach ($relationships['belongsTo'] as $rel) {
            $imports[] = "use Illuminate\\Database\\Eloquent\\Relations\\BelongsTo;";
            $methods[] = "    public function {$rel['method']}(): BelongsTo\n    {\n        return \$this->belongsTo({$rel['model']}::class, '{$rel['foreign_key']}', '{$rel['owner_key']}');\n    }\n";
        }

        foreach ($relationships['hasMany'] as $rel) {
            $imports[] = "use Illuminate\\Database\\Eloquent\\Relations\\HasMany;";
            $methods[] = "    public function {$rel['method']}(): HasMany\n    {\n        return \$this->hasMany({$rel['model']}::class, '{$rel['foreign_key']}', '{$rel['local_key']}');\n    }\n";
        }

        return [
            'methods' => implode("\n", $methods),
            'imports' => implode("\n", array_unique($imports)),
        ];
    }

    protected function getFactoryMethod(string $namespace): string
    {
        $factoryNs = $this->getBaseNamespace() . "\\Database\\Factories\\{$this->model}";
        $factoryClass = "{$factoryNs}\\{$this->model}Factory";
        
        return "\n    protected static function newFactory()\n    {\n        return \\{$factoryClass}::new();\n    }";
    }

    protected function initializeModule(): void
    {
        $basePath = base_path("Modules/{$this->module}");

        // 1. Create module.json if missing
        if (! File::exists("{$basePath}/module.json")) {
            $stub = File::get(__DIR__ . '/../../stubs/module.json.stub');
            $this->createFile("{$basePath}/module.json", $stub, [
                '{{Module}}'      => $this->module,
                '{{ModuleLower}}' => Str::lower($this->module),
            ]);
        }

        // 2. Create ServiceProvider if missing
        $providerPath = "{$basePath}/{$this->module}ServiceProvider.php";
        if (! File::exists($providerPath)) {
            $stub = File::get(__DIR__ . '/../../stubs/module-provider.stub');
            $this->createFile($providerPath, $stub, [
                '{{Module}}' => $this->module,
            ]);

            // Try to enable the module if nwidart is present
            try {
                $this->callSilent('module:enable', ['module' => $this->module]);
            } catch (\Exception $e) {
                // Silently skip if command not found
            }
        }
    }

    protected function generateMigration(): void
    {
        $table = Str::snake(Str::pluralStudly($this->model));
        $migrationExists = ! empty(glob(database_path("migrations/*create_{$table}_table.php")));

        if ($migrationExists) {
            $this->warn("  ⤳ Migration already exists for table [{$table}], skipping.");
            return;
        }

        $this->call('make:migration', [
            'name'     => "create_{$table}_table",
            '--create' => $table,
        ]);

        if ($this->option('soft-delete')) {
            $this->warn("  ⚠ Remember to add \$table->softDeletes(); to your migration.");
        }
    }

    // ──────────────────────────────────────────────────
    // Layer 2 — Service (Interface + Implementation)
    // ──────────────────────────────────────────────────

    protected function generateService(): void
    {
        $basePath       = $this->getBasePath();
        $baseNs         = $this->getBaseNamespace();
        $serviceNs      = "{$baseNs}\\Services\\{$this->model}";
        $contractNs     = "{$serviceNs}\\Contracts";
        $contractPath   = "{$basePath}/Services/{$this->model}/Contracts";
        $servicePath    = "{$basePath}/Services/{$this->model}";
        $modelNs        = "{$baseNs}\\Models\\{$this->model}";
        $dataNs         = "{$baseNs}\\DTOs\\{$this->model}\\{$this->model}Data";
        $repoContractNs = "{$baseNs}\\Repositories\\{$this->model}\\Contracts";
        $repoInterface  = "{$this->model}RepositoryInterface";

        $table = Str::snake(Str::pluralStudly($this->model));
        $columns = $this->analyzer->getColumns($table);
        $fileCols = array_filter($columns, fn($c) => $this->analyzer->isFileColumn($c['name']));

        $fileUploadLogic = '';
        $fileUpdateLogic = '';
        $hasFiles = count($fileCols) > 0;

        foreach ($fileCols as $col) {
            $name = $col['name'];
            $fileUploadLogic .= "if (\$data->{$name}) {\n            \$payload['{$name}'] = \$this->fileService->upload(\$data->{$name}, '{$table}');\n        }\n";
            
            $fileUpdateLogic .= "if (\$data->{$name}) {\n            \$old = \$this->getById(\$id);\n            if (\$old && \$old->{$name}) {\n                \$this->fileService->delete(\$old->{$name});\n            }\n            \$payload['{$name}'] = \$this->fileService->upload(\$data->{$name}, '{$table}');\n        }\n";
        }

        $replacements = [
            '{{Namespace}}'               => $serviceNs,
            '{{Class}}'                   => "{$this->model}Service",
            '{{ModelClass}}'              => $this->model,
            '{{ModelPath}}'               => $modelNs,
            '{{DataPath}}'                => $dataNs,
            '{{RepositoryInterface}}'      => $repoInterface,
            '{{RepositoryContractPath}}'   => $repoContractNs,
            '{{FileServiceImport}}'       => $hasFiles ? "use EasyDev\\Laravel\\Services\\FileService;" : '',
            '{{FileServiceProperty}}'     => $hasFiles ? ",\n        private readonly FileService \$fileService" : '',
            '{{FileUploadLogic}}'         => trim($fileUploadLogic),
            '{{FileUpdateLogic}}'         => trim($fileUpdateLogic),
        ];

        // 1. Interface
        $interfaceStub = File::get(__DIR__ . '/../../stubs/service-interface.stub');
        $this->createFile("{$contractPath}/{$this->model}ServiceInterface.php", $interfaceStub, [
            '{{Namespace}}'  => $serviceNs,
            '{{Class}}'      => $this->model,
            '{{ModelClass}}' => $this->model,
            '{{ModelPath}}'  => $modelNs,
            '{{DataPath}}'   => $dataNs,
        ]);

        // 2. Implementation
        $serviceStub = File::get(__DIR__ . '/../../stubs/service.stub');
        $this->createFile("{$servicePath}/{$this->model}Service.php", $serviceStub, $replacements);
    }

    // ──────────────────────────────────────────────────
    // Layer 4 — Repository (Interface + Implementation)
    // ──────────────────────────────────────────────────

    protected function generateRepository(): void
    {
        $basePath     = $this->getBasePath();
        $baseNs       = $this->getBaseNamespace();
        $repoNs       = "{$baseNs}\\Repositories\\{$this->model}";
        $contractNs   = "{$repoNs}\\Contracts";
        $contractPath = "{$basePath}/Repositories/{$this->model}/Contracts";
        $repoPath     = "{$basePath}/Repositories/{$this->model}";
        $modelNs      = "{$baseNs}\\Models\\{$this->model}";

        // 1. Interface
        $interfaceStub = File::get(__DIR__ . '/../../stubs/repository-interface.stub');
        $this->createFile("{$contractPath}/{$this->model}RepositoryInterface.php", $interfaceStub, [
            '{{Namespace}}' => $repoNs,
            '{{Class}}'     => $this->model,
        ]);

        // 2. Implementation
        $repoStub = File::get(__DIR__ . '/../../stubs/repository.stub');
        $this->createFile("{$repoPath}/{$this->model}Repository.php", $repoStub, [
            '{{Namespace}}' => $repoNs,
            '{{Class}}'     => "{$this->model}Repository",
            '{{ModelClass}}' => $this->model,
            '{{ModelPath}}' => $modelNs,
        ]);
    }

    // ──────────────────────────────────────────────────
    // Layer 5 — DTO + Policy + Test
    // ──────────────────────────────────────────────────

    protected function generateData(): void
    {
        $basePath  = $this->getBasePath();
        $namespace = $this->getBaseNamespace() . "\\DTOs\\{$this->model}";
        $path      = "{$basePath}/DTOs/{$this->model}/{$this->model}Data.php";
        $table     = Str::snake(Str::pluralStudly($this->model));

        // Build typed readonly properties from DB schema
        $rawRules = [];
        if (in_array($table, array_column($this->analyzer->getTables(), 'name'))) {
            $rawRules = $this->analyzer->generateRules($table);
        }

        $properties = [];
        foreach ($rawRules as $col => $colRules) {
            $type       = $this->inferPhpType($colRules);
            $nullable   = in_array('nullable', $colRules) ? '?' : '';
            $ruleAttr   = "#[Rule(['" . implode("', '", $colRules) . "'])]";
            $properties[] = "{$ruleAttr}\n        public readonly {$nullable}{$type} \${$col}";
        }

        if (empty($properties)) {
            $properties[] = 'public readonly string $name';
        }

        $stub = File::get(__DIR__ . '/../../stubs/data.stub');
        $this->createFile($path, $stub, [
            '{{Namespace}}' => $namespace,
            '{{Class}}'     => "{$this->model}Data",
            '{{Properties}}' => implode(",\n        ", $properties),
        ]);
    }

    protected function generatePolicy(): void
    {
        $basePath  = $this->getBasePath();
        $namespace = $this->getBaseNamespace() . "\\Policies\\{$this->model}";
        $path      = "{$basePath}/Policies/{$this->model}/{$this->model}Policy.php";

        $stub = File::get(__DIR__ . '/../../stubs/policy.stub');
        $this->createFile($path, $stub, [
            '{{Namespace}}'     => $namespace,
            '{{Class}}'         => "{$this->model}Policy",
            '{{ModelPath}}'     => $this->getBaseNamespace() . "\\Models\\{$this->model}",
            '{{ModelClass}}'    => $this->model,
            '{{ModelVariable}}' => Str::camel($this->model),
            '{{modelVariable}}' => Str::camel($this->model),
            '{{UserPath}}'      => 'App\\Models\\User',
            '{{UserClass}}'     => 'User',
        ]);
    }

    protected function generateTest(): void
    {
        $table = Str::snake(Str::pluralStudly($this->model));

        $path = $this->module
            ? base_path("Modules/{$this->module}/Tests/Feature/{$this->model}/{$this->model}Test.php")
            : base_path("tests/Feature/{$this->model}/{$this->model}Test.php");

        $stub = File::get(__DIR__ . '/../../stubs/test.stub');
        $this->createFile($path, $stub, [
            '{{ModelPath}}'      => $this->getBaseNamespace() . "\\Models\\{$this->model}",
            '{{ModelClass}}'     => $this->model,
            '{{ModelVariable}}'  => Str::camel($this->model),
            '{{ModelVariables}}' => Str::plural(Str::camel($this->model)),
            '{{Route}}'          => Str::kebab(Str::plural($this->model)),
            '{{Table}}'          => $table,
        ]);
    }

    protected function generateFactory(): void
    {
        $basePath  = $this->getBasePath();
        $namespace = $this->getBaseNamespace() . "\\Database\\Factories\\{$this->model}";
        $path      = "{$basePath}/Database/Factories/{$this->model}/{$this->model}Factory.php";

        $stub = File::get(__DIR__ . '/../../stubs/factory.stub');
        $this->createFile($path, $stub, [
            '{{Namespace}}' => $namespace,
            '{{ModelPath}}' => $this->getBaseNamespace() . "\\Models\\{$this->model}",
        ]);
    }

    // ──────────────────────────────────────────────────
    // Optional generators
    // ──────────────────────────────────────────────────

    protected function generateNotification(): void
    {
        $name      = "{$this->model}Notification";
        $path      = app_path("Notifications/{$name}.php");
        $stub      = File::get(__DIR__ . '/../../stubs/notification.stub');

        $this->createFile($path, $stub, [
            '{{Namespace}}' => 'App\\Notifications',
            '{{Class}}'     => $name,
            '{{Model}}'     => $this->model,
        ]);
    }

    protected function generateEvent(): void
    {
        foreach (['Created', 'Updated', 'Deleted'] as $suffix) {
            $this->call('make:event', ['name' => "{$this->model}{$suffix}"]);
        }
    }

    // ──────────────────────────────────────────────────
    // GraphQL Schema registration
    // ──────────────────────────────────────────────────

    protected function registerGraphQLSchema(): void
    {
        $mainSchemaFile = base_path('graphql/schema.graphql');
        
        if (! File::exists($mainSchemaFile)) {
            $this->warn("  ⚠ graphql/schema.graphql not found, skipping GraphQL registration.");
            return;
        }

        $table = Str::snake(Str::pluralStudly($this->model));
        $columns = $this->analyzer->getColumns($table);

        if (empty($columns)) {
            $this->warn("  ⚠ Could not find columns for table [{$table}], skipping GraphQL schema generation.");
            return;
        }

        $typeName = $this->model;
        $pluralName = Str::camel(Str::plural($this->model));
        $singularName = Str::camel($this->model);

        $typeFields = "";
        $inputFields = "";
        $updateFields = "    id: ID! @rules(apply: [\"required\"])\n";

        foreach ($columns as $column) {
            $name = $column['name'];
            $type = $this->mapToGraphQLType($column);
            $required = $column['nullable'] === false ? '!' : '';

            // Type Fields
            $typeFields .= "    {$name}: {$type}{$required}\n";

            // Input Fields
            if (! in_array($name, ['id', 'created_at', 'updated_at', 'deleted_at'])) {
                $inputFields .= "    {$name}: {$type}{$required}\n";
                $updateFields .= "    {$name}: {$type}\n";
            }
        }

        $schemaSnippet = "\n\"--------------------------------------------------------------------------\n| {$typeName} GraphQL Types\n--------------------------------------------------------------------------\"\n";
        $schemaSnippet .= "type {$typeName} {\n{$typeFields}}\n\n";
        $schemaSnippet .= "input Create{$typeName}Input {\n{$inputFields}}\n\n";
        $schemaSnippet .= "input Update{$typeName}Input {\n{$updateFields}}\n\n";

        $schemaSnippet .= "extend type Query {\n";
        $schemaSnippet .= "    {$singularName}(id: ID! @eq): {$typeName} @find\n";
        $schemaSnippet .= "    {$pluralName}: [{$typeName}!]! @paginate(defaultCount: 10)\n";
        $schemaSnippet .= "}\n\n";

        $schemaSnippet .= "extend type Mutation {\n";
        $schemaSnippet .= "    create{$typeName}(input: Create{$typeName}Input! @spread): {$typeName} @create\n";
        $schemaSnippet .= "    update{$typeName}(input: Update{$typeName}Input! @spread): {$typeName} @update\n";
        $schemaSnippet .= "    delete{$typeName}(id: ID! @whereKey): {$typeName} @delete\n";
        $schemaSnippet .= "}\n";

        // Determine target file
        $targetFile = $mainSchemaFile;
        if ($this->module) {
            $moduleDir = base_path("Modules/{$this->module}/GraphQL");
            if (! File::exists($moduleDir)) {
                File::makeDirectory($moduleDir, 0755, true);
            }
            $targetFile = "{$moduleDir}/schema.graphql";
            
            // Register import in main schema if not exists
            $importStatement = "#import ../Modules/{$this->module}/GraphQL/*.graphql\n";
            $mainContent = File::get($mainSchemaFile);
            if (! Str::contains($mainContent, $importStatement)) {
                File::prepend($mainSchemaFile, $importStatement);
            }
        }

        // Write to target file
        if (File::exists($targetFile)) {
            $content = File::get($targetFile);
            if (Str::contains($content, "type {$typeName} {")) {
                $this->warn("  ⤳ GraphQL type [{$typeName}] already exists in {$targetFile}, skipping.");
                return;
            }
            File::append($targetFile, $schemaSnippet);
        } else {
            File::put($targetFile, $schemaSnippet);
        }

        $this->info("  ✓ GraphQL Schema registered in: {$targetFile}");
    }

    protected function mapToGraphQLType(array $column): string
    {
        $name = $column['name'];
        $dbType = strtolower($column['type_name'] ?? '');
        $fullType = strtolower($column['type'] ?? '');

        if ($name === 'id') return 'ID';
        
        // Handle Boolean for tinyint(1)
        if (Str::contains($fullType, 'tinyint(1)') || Str::contains($dbType, 'bool')) {
            return 'Boolean';
        }

        if (Str::contains($dbType, ['int', 'integer'])) return 'Int';
        if (Str::contains($dbType, ['decimal', 'float', 'double'])) return 'Float';
        if (Str::contains($dbType, ['date', 'timestamp', 'datetime'])) return 'DateTime';

        return 'String';
    }

    // ──────────────────────────────────────────────────
    // Smart Validation Logic
    // ──────────────────────────────────────────────────

    protected function resolveRules(string $table, bool $sometimes = false): string
    {
        $rawRules = [];
        if (in_array($table, array_column($this->analyzer->getTables(), 'name'))) {
            $rawRules = $this->analyzer->generateRules($table);
        }

        $rulesString = '';
        foreach ($rawRules as $col => $colRules) {
            if ($sometimes) {
                array_unshift($colRules, 'sometimes');
            }
            $rulesString .= "\n            '{$col}' => ['" . implode("', '", $colRules) . "'],";
        }

        return empty($rulesString) ? "\n            //" : $rulesString;
    }

    protected function inferPhpType(array $rules): string
    {
        if (in_array('integer', $rules)) return 'int';
        if (in_array('numeric', $rules)) return 'float';
        if (in_array('boolean', $rules)) return 'bool';
        if (in_array('date', $rules)) return 'string'; // Or Carbon if we want to be fancy
        return 'string';
    }

    // ──────────────────────────────────────────────────
    // Helpers
    // ──────────────────────────────────────────────────

    protected function getTranslatableFields(): string
    {
        if (! $this->option('translatable')) {
            return '';
        }

        $fields = array_map('trim', explode(',', $this->option('translatable')));
        return "public \$translatable = ['" . implode("', '", $fields) . "'];";
    }

    protected function getEnterpriseTraits(): string
    {
        $traits = [];

        if ($this->option('with-logs')) {
            $traits[] = 'use \\Spatie\\Activitylog\\Traits\\LogsActivity;';
        }

        return implode("\n    ", $traits);
    }

    protected function createFile(string $path, string $stub, array $replacements): void
    {
        $directory = dirname($path);

        if (! File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        $table = Str::snake(Str::pluralStudly($this->model));

        $defaultReplacements = [
            '{{ModelClass}}' => $this->model,
            '{{Model}}'      => $this->model,
            '{{Module}}'     => $this->module ?? '',
            '{{IdType}}'     => $this->analyzer->isUuidPrimaryKey($table) ? 'string' : 'int',
            '{{UUIDsImport}}' => $this->analyzer->isUuidPrimaryKey($table)
                ? "use Illuminate\\Database\\Eloquent\\Concerns\\HasUuids;"
                : '',
            '{{UUIDsTrait}}'  => $this->analyzer->isUuidPrimaryKey($table)
                ? ', HasUuids'
                : '',
        ];

        $replacements = array_merge($defaultReplacements, $replacements);
        $content = str_replace(array_keys($replacements), array_values($replacements), $stub);

        if (File::exists($path) && ! $this->option('force')) {
            if (! $this->confirm("File already exists: {$path}\nOverwrite?", false)) {
                $this->warn("  ⤳ Skipped: {$path}");
                return;
            }
        }

        File::put($path, $content);
        $this->line("  <fg=green>✓</> Created: <fg=cyan>{$path}</>");
    }
}
