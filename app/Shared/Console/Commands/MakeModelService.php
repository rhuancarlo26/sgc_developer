<?php

namespace App\Shared\Console\Commands;

use Illuminate\Console\Command;

class MakeModelService extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:model-service {service}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates a new Model Service class';

    private function getClassContents(string $namespace, string $className): string
    {
        $stub = file_get_contents(base_path('stubs') . '/model-service.stub');

        $contents = str_replace('{{ namespace }}', str_replace('/', '\\', $namespace), $stub);

        return str_replace('{{ class }}', $className, $contents);
    }

    private function getClassPath($namespace): string
    {
        $path = null;

        if (str_starts_with($namespace, 'App')) {
            $path = substr_replace($namespace, 'app', 0, 3);
        }

        return base_path('/') . str_replace('/', DIRECTORY_SEPARATOR, $path);
    }

    private function getClassNameAndNamespace(): array
    {

        $explodedServiceString = explode('/', $this->argument('service'));

        $className = last($explodedServiceString);

        unset($explodedServiceString[array_key_last($explodedServiceString)]);

        $namespace = implode('/', $explodedServiceString);

        return [$className, $namespace];
    }


    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        [$className, $namespace] = $this->getClassNameAndNamespace();

        $contents = $this->getClassContents($namespace, $className);

        $path = $this->getClassPath($namespace);

        try {

            \File::makeDirectory(path: $path, mode: 0774, recursive: true, force: true);

            $fullPath = $path . DIRECTORY_SEPARATOR . "$className.php";

            \File::put($fullPath, $contents);

            $this->output->success("Service created at $fullPath");

        } catch (\Throwable $tr) {
            $this->error($tr->getMessage());
        }


    }
}
