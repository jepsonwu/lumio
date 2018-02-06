<?php

namespace Prettus\Repository\Generators;

/**
 * Class CriteriaGenerator
 * @package Prettus\Repository\Generators
 */
class CriteriaGenerator extends Generator
{
    /**
     * Get stub name.
     *
     * @var string
     */
    protected $stub = 'criteria/criteria';

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
     * @return string
     */
    public function getPathConfigNode()
    {
        return 'criteria';
    }

    /**
     * Get destination path for generated file.
     *
     * @return string
     */
    public function getPath()
    {
        return $this->getBasePath() . '/' . parent::getConfigGeneratorClassPath($this->getPathConfigNode(), true) . '/' . $this->getName() . 'Criteria.php';
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
}
