<?php
namespace Prettus\Repository\Generators;

/**
 * Class ControllerGenerator
 * @package Prettus\Repository\Generators
 */
class ControllerGenerator extends Generator
{

    /**
     * Get stub name.
     *
     * @var string
     */
    protected $stub = 'controller/controller';

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
        return str_replace('/', '\\', parent::getRootNamespace() . $middle . parent::getConfigGeneratorClassPath($this->getPathConfigNode()));
    }

    /**
     * Get generator path config node.
     *
     * @return string
     */
    public function getPathConfigNode()
    {
        return 'controllers';
    }

    /**
     * Get destination path for generated file.
     *
     * @return string
     */
    public function getPath()
    {
        return $this->getBasePath() . '/' . parent::getConfigGeneratorClassPath($this->getPathConfigNode(), true) . '/' . $this->getControllerName() . 'Controller.php';
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
     * Gets controller name based on model
     *
     * @return string
     */
    public function getControllerName()
    {

        return ucfirst($this->getPluralName());
    }

    /**
     * Gets plural name based on model
     *
     * @return string
     */
    public function getPluralName()
    {

        return str_plural(lcfirst(ucwords($this->getClass())));
    }

    /**
     * Get array replacements.
     *
     * @return array
     */
    public function getReplacements()
    {

        return array_merge(parent::getReplacements(), [
            'controller' => $this->getControllerName(),
            'plural' => $this->getPluralName(),
            'singular' => $this->getSingularName(),
            'validator' => $this->getValidator(),
            'service' => $this->getService(),
            'modulename' => $this->getModuleName(),
        ]);
    }

    public function getModuleName()
    {
        $middle = '';
        if (isset($this->options['module']))
            $middle = $this->options['module'] . "\\";
        return str_replace('/', '\\', parent::getRootNamespace() . $middle );
    }

    /**
     * Gets singular name based on model
     *
     * @return string
     */
    public function getSingularName()
    {
        return str_singular(lcfirst(ucwords($this->getClass())));
    }

    /**
     * Gets validator full class name
     *
     * @return string
     */
    public function getValidator()
    {
        $validatorGenerator = new ValidatorGenerator([
            'name' => $this->name,
            'module' => trim($this->options['module'])
        ]);

        $validator = $validatorGenerator->getRootNamespace() . '\\' . $validatorGenerator->getName();

        return 'use ' . str_replace([
            "\\",
            '/'
        ], '\\', $validator) . 'Validator;';
    }


    /**
     * Gets repository full class name
     *
     * @return string
     */
    public function getService()
    {
        $repositoryGenerator = new ServiceGenerator([
            'name' => $this->name,
            'module' => trim($this->options['module'])
        ]);

        $repository = $repositoryGenerator->getRootNamespace() . '\\' . $repositoryGenerator->getName();

        return 'use ' . str_replace([
            "\\",
            '/'
        ], '\\', $repository) . 'Service;';
    }
}
