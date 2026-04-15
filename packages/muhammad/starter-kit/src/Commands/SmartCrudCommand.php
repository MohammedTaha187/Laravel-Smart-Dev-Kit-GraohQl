<?php

namespace Muhammad\StarterKit\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Muhammad\StarterKit\Services\DBAnalyzer;

class SmartCrudCommand extends Command
{
    protected $signature = 'smart:crud {name} 
                            {--api : Create an API controller}
                            {--with-service : Create a service class}
                            {--with-repository : Create a repository class}
                            {--with-resource : Create an API resource}
                            {--with-request : Create a form request}
                            {--with-data : Create a Spatie Data object (Type-Safe)}
                            {--with-contracts : Generate interfaces/contracts for services and repositories}
                            {--module= : Generate code inside a specific module}
                            {--with-policy : Create a policy}
                            {--with-tests : Create Pest tests}
                            {--with-media : Add media support using Spatie Media Library}
                            {--with-event : Generate model events}
                            {--with-notification : Generate a default notification class}
                            {--with-payments : Add payment support using Laravel Cashier}
                            {--with-logs : Add activity logging}
                            {--soft-delete : Add soft delete support}
                            {--translatable= : Comma-separated list of translatable fields}';

    protected $description = 'Generate a complete CRUD feature set';

    protected $model;
    protected $config;
    protected $analyzer;

    public function __construct(DBAnalyzer $analyzer)
    {
        parent::__construct();
        $this->analyzer = $analyzer;
    }

    public function handle()
    {
        $this->model = $this->argument('name');
        $this->config = config('starter-kit');

        $this->info("Generating CRUD for {$this->model}...");

        $this->generateModel();
        $this->generateMigration();

        if ($this->option('with-request')) $this->generateRequest();
        if ($this->option('with-resource')) $this->generateResource();
        if ($this->option('with-data')) $this->generateData();
        if ($this->option('with-service')) $this->generateService();
        if ($this->option('with-repository')) $this->generateRepository();
        if ($this->option('with-policy')) $this->generatePolicy();

        $this->generateController();

        if ($this->option('with-tests')) $this->generateTest();
        if ($this->option('with-notification')) $this->generateNotification();
        if ($this->option('with-event')) $this->generateEvent();

        $this->registerRoute();

        $this->info("CRUD for {$this->model} generated successfully!");
    }

    protected function getBasePath()
    {
        if ($module = $this->option('module')) {
            return base_path("Modules/{$module}/app");
        }
        return app_path();
    }

    protected function getBaseNamespace()
    {
        if ($module = $this->option('module')) {
            return "Modules\\{$module}";
        }
        return "App";
    }

    protected function generateModel()
    {
        $stub = File::get(__DIR__ . '/../../stubs/model.stub');
        $basePath = $this->getBasePath();
        $path = ($this->option('module')) ? "{$basePath}/Models/{$this->model}.php" : app_path("Models/{$this->model}.php");

        $replacements = [
            '{{Namespace}}' => $this->getBaseNamespace() . ($this->option('module') ? '\\Models' : '\\Models'),
            '{{Class}}' => $this->model,
            '{{SoftDeletesImport}}' => $this->option('soft-delete') ? 'use Illuminate\\Database\\Eloquent\\SoftDeletes;' : '',
            '{{SoftDeletesTrait}}' => $this->option('soft-delete') ? ', SoftDeletes' : '',
            '{{MediaImports}}' => $this->option('with-media') ? "use Spatie\\MediaLibrary\\HasMedia;\nuse Spatie\\MediaLibrary\\InteractsWithMedia;" : '',
            '{{MediaImplements}}' => $this->option('with-media') ? 'implements HasMedia' : '',
            '{{MediaTrait}}' => $this->option('with-media') ? 'use InteractsWithMedia;' : '',
            '{{TranslationImport}}' => $this->option('translatable') ? "use App\\Traits\\HasTranslations;" : '',
            '{{TranslationTrait}}' => $this->option('translatable') ? "use HasTranslations;" : '',
            '{{TranslatableFields}}' => $this->getTranslatableFields(),
            '{{EnterpriseTraits}}' => $this->getEnterpriseTraits(),
        ];

        $this->createFile($path, $stub, $replacements);
    }

    protected function generateMigration()
    {
        $table = Str::snake(Str::pluralStudly($this->model));
        $filename = date('Y_m_d_His') . "_create_{$table}_table.php";
        $path = database_path("migrations/{$filename}");

        $this->call('make:migration', [
            'name' => "create_{$table}_table",
            '--create' => $table
        ]);

        if ($this->option('soft-delete')) {
            $this->info("Note: Remember to add \$table->softDeletes(); to your migration.");
        }
    }

    protected function generateRequest()
    {
        $name = "{$this->model}Request";
        $path = $this->config['paths']['request'] ?? app_path('Http/Requests');
        $namespace = $this->config['namespaces']['request'] ?? 'App\\Http\\Requests';
        $table = Str::snake(Str::pluralStudly($this->model));

        $rules = [];
        if (in_array($table, array_column($this->analyzer->getTables(), 'name'))) {
            $rules = $this->analyzer->generateRules($table);
        }

        $rulesString = "";
        foreach ($rules as $col => $colRules) {
            $rulesString .= "\n            '{$col}' => ['" . implode("', '", $colRules) . "'],";
        }
        if (empty($rulesString)) $rulesString = "\n            //";

        $stub = File::get(__DIR__ . '/../../stubs/request.stub');
        $this->createFile("{$path}/{$name}.php", $stub, [
            '{{Namespace}}' => $namespace,
            '{{Class}}' => $name,
            '{{Rules}}' => $rulesString,
        ]);
    }

    protected function generateResource()
    {
        $name = "{$this->model}Resource";
        $path = $this->option('module') ? $this->getBasePath() . "/Http/Resources" : ($this->config['paths']['resource'] ?? app_path('Http/Resources'));
        $namespace = $this->option('module') ? $this->getBaseNamespace() . "\\Http\\Resources" : ($this->config['namespaces']['resource'] ?? 'App\\Http\\Resources');

        $stub = File::get(__DIR__ . '/../../stubs/resource.stub');
        $this->createFile("{$path}/{$name}.php", $stub, [
            '{{Namespace}}' => $namespace,
            '{{Class}}' => $name,
        ]);
    }

    protected function generateData()
    {
        $name = "{$this->model}Data";
        $path = $this->option('module') ? $this->getBasePath() . "/Data" : app_path('Data');
        $namespace = $this->option('module') ? $this->getBaseNamespace() . "\\Data" : 'App\\Data';

        $table = Str::snake(Str::pluralStudly($this->model));
        $rules = [];
        if (in_array($table, array_column($this->analyzer->getTables(), 'name'))) {
            $rules = $this->analyzer->generateRules($table);
        }

        $properties = [];
        foreach ($rules as $col => $colRules) {
            $rulesAttr = "#[Rule('" . implode("', '", $colRules) . "')]\n        ";
            $properties[] = "{$rulesAttr}public string \${$col}";
        }
        if (empty($properties)) $properties[] = "public string \$name";

        $stub = File::get(__DIR__ . '/../../stubs/data.stub');
        $this->createFile("{$path}/{$name}.php", $stub, [
            '{{Namespace}}' => $namespace,
            '{{Class}}' => $name,
            '{{Properties}}' => implode(",\n        ", $properties),
        ]);
    }

    protected function generateService()
    {
        $name = "{$this->model}Service";
        $path = $this->option('module') ? $this->getBasePath() . "/Services" : ($this->config['paths']['service'] ?? app_path('Services'));
        $namespace = $this->option('module') ? $this->getBaseNamespace() . "\\Services" : ($this->config['namespaces']['service'] ?? 'App\\Services');

        if ($this->option('with-contracts')) {
            $this->generateContract('Service', $namespace, $name);
        }

        $stub = File::get(__DIR__ . '/../../stubs/service.stub');
        $this->createFile("{$path}/{$name}.php", $stub, [
            '{{Namespace}}' => $namespace,
            '{{Class}}' => $name,
            '{{Implements}}' => $this->option('with-contracts') ? "implements {$name}Interface" : "",
        ]);
    }

    protected function generateRepository()
    {
        $name = "{$this->model}Repository";
        $path = $this->option('module') ? $this->getBasePath() . "/Repositories" : ($this->config['paths']['repository'] ?? app_path('Repositories'));
        $namespace = $this->option('module') ? $this->getBaseNamespace() . "\\Repositories" : ($this->config['namespaces']['repository'] ?? 'App\\Repositories');

        if ($this->option('with-contracts')) {
            $this->generateContract('Repository', $namespace, $name);
        }

        $stub = File::get(__DIR__ . '/../../stubs/repository.stub');
        $this->createFile("{$path}/{$name}.php", $stub, [
            '{{Namespace}}' => $namespace,
            '{{Class}}' => $name,
            '{{Implements}}' => $this->option('with-contracts') ? "implements {$name}Interface" : "",
        ]);
    }

    protected function generateContract($type, $namespace, $className)
    {
        $name = "{$className}Interface";
        $basePath = $this->option('module') ? base_path("Modules/{$this->option('module')}/app/Contracts") : app_path('Contracts');
        $contractNamespace = $this->option('module') ? "Modules\\{$this->option('module')}\\Contracts" : "App\\Contracts";

        $content = "<?php\n\nnamespace {$contractNamespace};\n\ninterface {$name}\n{\n    //\n}\n";
        
        $this->createFile("{$basePath}/{$name}.php", $content, []);
    }

    protected function generatePolicy()
    {
        $name = "{$this->model}Policy";
        $path = app_path('Policies');

        $stub = File::get(__DIR__ . '/../../stubs/policy.stub');
        $this->createFile("{$path}/{$name}.php", $stub, [
            '{{Namespace}}' => 'App\\Policies',
            '{{Class}}' => $name,
            '{{ModelPath}}' => "App\\Models\\{$this->model}",
            '{{ModelClass}}' => $this->model,
            '{{ModelVariable}}' => Str::camel($this->model),
            '{{UserPath}}' => "App\\Models\\User",
            '{{UserClass}}' => 'User',
        ]);
    }

    protected function generateController()
    {
        $name = "{$this->model}Controller";
        $path = $this->config['paths']['controller'];
        $namespace = $this->config['namespaces']['controller'];
        $stub = File::get(__DIR__ . '/../../stubs/controller.api.stub');

        $replacements = $this->getControllerReplacements($namespace, $name);
        $this->createFile("{$path}/{$name}.php", $stub, $replacements);
    }

    protected function getControllerReplacements($namespace, $className)
    {
        $reps = [
            '{{Namespace}}' => $namespace,
            '{{BaseControllerPath}}' => 'App\\Http\\Controllers\\Controller',
            '{{BaseController}}' => 'Controller',
            '{{Class}}' => $className,
            '{{ModelPath}}' => "App\\Models\\{$this->model}",
            '{{ModelClass}}' => $this->model,
            '{{ModelVariable}}' => Str::camel($this->model),
        ];

        // Service & Repo Logic
        $properties = [];
        $constructorParams = [];
        $constructorBody = [];

        if ($this->option('with-service')) {
            $serviceClass = "{$this->model}Service";
            $reps['{{ServiceImport}}'] = "use App\\Services\\{$serviceClass};";
            $properties[] = "protected \${$serviceClass};";
            $constructorParams[] = "{$serviceClass} \${$serviceClass}";
            $constructorBody[] = "\$this->{$serviceClass} = \${$serviceClass};";
        } else {
            $reps['{{ServiceImport}}'] = "";
        }

        if ($this->option('with-repository')) {
            $repoClass = "{$this->model}Repository";
            $reps['{{RepositoryImport}}'] = "use App\\Repositories\\{$repoClass};";
            $properties[] = "protected \${$repoClass};";
            $constructorParams[] = "{$repoClass} \${$repoClass}";
            $constructorBody[] = "\$this->{$repoClass} = \${$repoClass};";
        } else {
            $reps['{{RepositoryImport}}'] = "";
        }

        $reps['{{Properties}}'] = implode("\n    ", $properties);
        $reps['{{Constructor}}'] = implode(", ", $constructorParams);
        $reps['{{ConstructorBody}}'] = implode("\n        ", $constructorBody);

        // Request & Resource & Data Logic
        $module = $this->option('module');
        if ($this->option('with-data')) {
            $dataClass = "{$this->model}Data";
            $dataNamespace = $module ? "Modules\\{$module}\\Data" : "App\\Data";
            $reps['{{RequestImport}}'] = "use {$dataNamespace}\\{$dataClass};";
            $reps['{{RequestClass}}'] = $dataClass;
            $reps['{{ResourceImport}}'] = "";
            $reps['{{IndexBody}}'] = "return \$this->successResponse({$dataClass}::collection({$this->model}::all()));";
            $reps['{{StoreBody}}'] = "\$data = \$this->{$this->model}Service->create(\$request->toArray());\n        return \$this->successResponse({$dataClass}::from(\$data), '{$this->model} created.', 201);";
        } else {
            $requestClass = $this->option('with-request') ? "{$this->model}Request" : "Request";
            $requestNamespace = $module ? "Modules\\{$module}\\Http\\Requests" : "App\\Http\\Requests";
            $reps['{{RequestImport}}'] = $this->option('with-request') ? "use {$requestNamespace}\\{$requestClass};" : "use Illuminate\\Http\\Request;";
            $reps['{{RequestClass}}'] = $requestClass;

            if ($this->option('with-resource')) {
                $resourceClass = "{$this->model}Resource";
                $resourceNamespace = $module ? "Modules\\{$module}\\Http\\Resources" : "App\\Http\\Resources";
                $reps['{{ResourceImport}}'] = "use {$resourceNamespace}\\{$resourceClass};";
                $reps['{{IndexBody}}'] = "return \$this->successResponse({$resourceClass}::collection({$this->model}::all()));";
                $reps['{{StoreBody}}'] = "\$data = \$this->{$this->model}Service->create(\$request->validated());\n        return \$this->successResponse(new {$resourceClass}(\$data), '{$this->model} created.', 201);";
            } else {
                $reps['{{ResourceImport}}'] = "";
                $reps['{{IndexBody}}'] = "return \$this->successResponse({$this->model}::all());";
                $reps['{{StoreBody}}'] = "\$data = \$this->{$this->model}Service->create(\$request->all());\n        return \$this->successResponse(\$data, '{$this->model} created.', 201);";
            }
        }

        $reps['{{IndexBody}}'] = $reps['{{IndexBody}}'] ?? "";
        $reps['{{StoreBody}}'] = $reps['{{StoreBody}}'] ?? "";
        $reps['{{ShowBody}}'] = "";
        $reps['{{UpdateBody}}'] = "";
        $reps['{{DestroyBody}}'] = "";

        return $reps;
    }

    protected function generateTest()
    {
        $module = $this->option('module');
        $path = $module ? base_path("Modules/{$module}/Tests/Feature/{$this->model}Test.php") : base_path("tests/Feature/{$this->model}Test.php");
        $stub = File::get(__DIR__ . '/../../stubs/test.stub');

        $modelPath = $module ? "Modules\\{$module}\\Models\\{$this->model}" : "App\\Models\\{$this->model}";

        $this->createFile($path, $stub, [
            '{{ModelPath}}' => $modelPath,
            '{{ModelClass}}' => $this->model,
            '{{ModelVariable}}' => Str::camel($this->model),
            '{{ModelVariables}}' => Str::plural(Str::camel($this->model)),
            '{{Route}}' => Str::kebab(Str::plural($this->model)),
        ]);
    }

    protected function registerRoute()
    {
        $path = base_path('routes/api.php');
        $controllerNamespace = $this->config['namespaces']['controller'];
        $controllerClass = "{$this->model}Controller";
        $routeName = Str::kebab(Str::plural($this->model));

        $routeLine = "\nRoute::apiResource('{$routeName}', \\{$controllerNamespace}\\{$controllerClass}::class);";

        File::append($path, $routeLine);
        $this->info("Route registered: /api/v1/{$routeName}");
    }

    protected function generateNotification()
    {
        $name = "{$this->model}Notification";
        $path = app_path('Notifications');
        $stub = File::get(__DIR__ . '/../../stubs/notification.stub');

        $this->createFile("{$path}/{$name}.php", $stub, [
            '{{Namespace}}' => 'App\\Notifications',
            '{{Class}}' => $name,
            '{{Model}}' => $this->model,
        ]);
    }

    protected function generateEvent()
    {
        $this->call('make:event', ['name' => "{$this->model}Created"]);
        $this->call('make:event', ['name' => "{$this->model}Updated"]);
        $this->call('make:event', ['name' => "{$this->model}Deleted"]);
    }

    protected function getTranslatableFields()
    {
        if (! $this->option('translatable')) return "";
        
        $fields = explode(',', $this->option('translatable'));
        $fieldsString = "public \$translatable = ['" . implode("', '", array_map('trim', $fields)) . "'];";
        
        return $fieldsString;
    }

    protected function getEnterpriseTraits()
    {
        $traits = [];
        if ($this->option('with-payments')) {
            $traits[] = "use \\App\\Traits\\HasPayments;";
        }
        if ($this->option('with-logs')) {
            $traits[] = "use \\App\\Traits\\HasProfessionalLogs;";
        }
        return implode("\n    ", $traits);
    }

    protected function createFile($path, $stub, $replacements)
    {
        $directory = dirname($path);

        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        $content = str_replace(array_keys($replacements), array_values($replacements), $stub);

        if (File::exists($path)) {
            if (!$this->confirm("File {$path} already exists. Overwrite?")) {
                return;
            }
        }

        File::put($path, $content);
        $this->info("Created: {$path}");
    }
}
