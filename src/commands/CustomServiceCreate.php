<?php

namespace HydraService\Commands;


use Illuminate\Console\Command;

class CustomServiceCreate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:service {path} {--author=unidentified}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a service';
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $bar = $this->output->createProgressBar(100);
        $bar->start();
        $path = $this->argument('path');
        $author = $this->option('author') ?? ''; // Use a default author if not provided

        $servicePath = app_path('Services' . '/'. $path . '.php');
        $namespaceParts = explode('/', $path);
        array_pop($namespaceParts);
        $namespacePath = implode('\\', $namespaceParts);

        // Extract directory path from the service path
        $directoryPath = dirname($servicePath);
        $bar->advance(50);

        // Create directories if they don't exist
        if (!file_exists($directoryPath))
        {
            mkdir($directoryPath, 0755, true);
        }

        if (file_exists($servicePath))
        {
            $this->error("{$path} already exists!");
            return;
        }

        // Create the service file with the class name
        $className = basename($servicePath, '.php');
        $classContent = <<<EOT
<?php

/**
 * Novax Organization's Code
 *
 * This file contains code developed by $author.
 * It serves as an example of how to include a comment or
 * documentation header to indicate the origin of the code.
 *
 * @organization     Novax
 * @author      $author
 * @license     MIT
 */

namespace App\Services\\$namespacePath;

class $className
{
    //
}
EOT;


        file_put_contents($servicePath, $classContent);
        $bar->advance(50);
        $bar->finish();
        $this->info("{$path} created successfully!");
    }
}
