<?php

namespace App\Shared\Console\Commands;

use Illuminate\Console\Command;

class MakeViewComponent extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:vue-page-component {component}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates a new Vue component with Composition API';


    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $componentPath = explode('/', $this->argument('component'));
        $componentName = last($componentPath);

        unset($componentPath[array_key_last($componentPath)]);

        $componentPath = implode('/', $componentPath);
        $path = resource_path("js/Pages/$componentPath/");
        $fullPath = $path . "$componentName.vue";


        try {

            \File::makeDirectory(path: $path, mode: 0774, recursive: true);
            \File::put($fullPath, file_get_contents(base_path('stubs/') . 'vue-component.stub'));

            $this->output->success("Component created at $fullPath");

        } catch (\Throwable $tr) {
            $this->error($tr->getMessage());
        }


    }
}
