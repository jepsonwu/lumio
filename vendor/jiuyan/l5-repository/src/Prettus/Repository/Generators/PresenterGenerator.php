<?php
namespace Prettus\Repository\Generators;

/**
 * Class PresenterGenerator
 * @package Prettus\Repository\Generators
 */

class PresenterGenerator extends Generator
{
    /**
     * Get stub name.
     *
     * @var string
     */
    protected $stub = 'presenter/presenter';

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
        return 'presenters';
    }

    /**
     * Get array replacements.
     *
     * @return array
     */
    public function getReplacements()
    {
        $transformerGenerator = new TransformerGenerator([
            'name' => $this->name,
            'module' => trim($this->options['module'])
        ]);
        $transformer = $transformerGenerator->getRootNamespace() . '\\' . $transformerGenerator->getName() . 'Transformer';
        $transformer = str_replace([
            "\\",
            '/'
        ], '\\', $transformer);

        return array_merge(parent::getReplacements(), [
            'transformer' => $transformer
        ]);
    }

    /**
     * Get destination path for generated file.
     *
     * @return string
     */
    public function getPath()
    {
        return $this->getBasePath() . '/' . parent::getConfigGeneratorClassPath($this->getPathConfigNode(), true) . '/' . $this->getName() . 'Presenter.php';
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
