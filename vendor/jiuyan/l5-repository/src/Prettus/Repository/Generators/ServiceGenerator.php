<?php

namespace Prettus\Repository\Generators;

use Prettus\Repository\Generators\Migrations\SchemaParser;

/**
 * Class RepositoryInterfaceGenerator
 * @package Prettus\Repository\Generators
 */
class ServiceGenerator extends Generator
{

    /**
     * Get stub name.
     *
     * @var string
     */
    protected $stub = 'service/service';

    /**
     * Get root namespace.
     *
     * @return string
     */
    public function getRootNamespace()
    {
        $middle = '';
        if (isset($this->options['module']))
            $middle = $this->options['module'] . "\\";
        return parent::getRootNamespace() . $middle . parent::getConfigGeneratorClassPath($this->getPathConfigNode());
    }

    /**
     * Get generator path config node.
     *
     * @return string
     */
    public function getPathConfigNode()
    {
        return 'services';
    }

    /**
     * Get destination path for generated file.
     *
     * @return string
     */
    public function getPath()
    {
        return $this->getBasePath() . '/' . parent::getConfigGeneratorClassPath($this->getPathConfigNode(), true) . '/' . $this->getName() . 'Service.php';
    }

    /**
     * Get base path of destination file.
     *
     * @return string
     */
    public function getBasePath()
    {
        if (isset($this->options['module'])) {
            return base_path() . '/Modules/' . trim($this->options['module']);
        } else {
            return config('repository.generator.basePath', app_path());
        }
    }

    /**
     * Get array replacements.
     *
     * @return array
     */
    public function getReplacements()
    {
        $service = parent::getRootNamespace() . parent::getConfigGeneratorClassPath('service') . '\\' . $this->name . 'Services;';
        $service = str_replace([
            "\\",
            '/'
        ], '\\', $service);

        return array_merge(parent::getReplacements(), [
            'service' => $service,
            'repository' => $this->getRepository(),
        ]);
    }

    /**
     * Gets repository full class name
     *
     * @return string
     */
    public function getRepository()
    {
        $repositoryGenerator = new RepositoryInterfaceGenerator([
            'name' => $this->name,
            'module' => trim($this->options['module'])
        ]);
        $repository = $repositoryGenerator->getRootNamespace() . '\\' . $repositoryGenerator->getName();
        return 'use ' . str_replace([
            "\\",
            '/'
        ], '\\', $repository) . 'Repository;';
    }
}
