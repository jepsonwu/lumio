<?php
namespace Prettus\Repository\Generators;

/**
 * Class TransformerGenerator
 * @package Prettus\Repository\Generators
 */

class TransformerGenerator extends Generator
{
    /**
     * Get stub name.
     *
     * @var string
     */
    protected $stub = 'transformer/transformer';

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
        return 'transformers';
    }

    /**
     * Get destination path for generated file.
     *
     * @return string
     */
    public function getPath()
    {
        return $this->getBasePath() . '/' . parent::getConfigGeneratorClassPath($this->getPathConfigNode(), true) . '/' . $this->getName() . 'Transformer.php';
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
        $modelGenerator = new ModelGenerator([
            'name' => $this->name,
            'module' => trim($this->options['module'])
        ]);
        $model = $modelGenerator->getRootNamespace() . '\\' . $modelGenerator->getName();
        $model = str_replace([
            "\\",
            '/'
        ], '\\', $model);

        return array_merge(parent::getReplacements(), [
            'model' => $model
        ]);
    }
}
