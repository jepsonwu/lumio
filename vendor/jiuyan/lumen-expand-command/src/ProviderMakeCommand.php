<?php

namespace Jiuyan\LumenCommand;

use Illuminate\Support\Str;
use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputOption;

class ProviderMakeCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:provider';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new service provider class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Provider';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__ . '/stubs/provider.stub';
    }

    protected function rootNamespace()
    {
        return 'Modules\\' . str::ucfirst(trim($this->option('module')));
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\Providers';
    }

    protected function getPath($name)
    {
        $module = trim($this->option('module'));
        if (isset($module)) {
            $name = str_replace_first($this->rootNamespace(), '', $name);
            $suffix = 'ServiceProvider';
            if(stripos($name, $suffix) !== false)
                $suffix = '';
            return base_path() . '/Modules/' . $module . '/' . str_replace('\\', '/', $name) . $suffix.'.php';
        } else {
            return parent::getPath($name);
        }
    }

    /**
     * The array of command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            [
                'module',
                null,
                InputOption::VALUE_REQUIRED,
                'Skip set the module dir.',
                null
            ],
        ];
    }


}
