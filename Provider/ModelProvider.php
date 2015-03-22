<?php
namespace PHPMentors\AnnotationCheckerBundle\Provider;

use DomainCoder\Metamodel\Code\Command\Exception\CacheNotFoundException;
use DomainCoder\Metamodel\Code\Parser\ProjectParser;
use DomainCoder\Metamodel\Code\Util\Model;
use JMS\DiExtraBundle\Annotation as DI;

/**
 * @DI\Service("annotation_checker.model_provider")
 */
class ModelProvider
{
    /**
     * @var ProjectParser
     */
    private $parser;

    /**
     * @var string
     */
    private $rootDir;

    /**
     * @var string
     */
    private $cacheDir;

    /**
     * @param ProjectParser $parser
     * @param $kernelDir
     * @param $cacheDir
     * @DI\InjectParams({
     *   "parser" = @DI\Inject("annotation_checker.metamodel.project_parser"),
     *   "kernelDir" = @DI\Inject("%kernel.root_dir%"),
     *   "cacheDir" = @DI\Inject("%kernel.cache_dir%"),
     * })
     */
    public function __construct(ProjectParser $parser, $kernelDir, $cacheDir)
    {
        $this->parser = $parser;
        $this->rootDir = $this->getSrcDir($kernelDir);
        $this->cacheDir = $cacheDir;
    }

    public function createCache()
    {
        $model = $this->parser->parse($this->rootDir);

        $this->writeCache($this->cacheFilePath($this->rootDir), $model);
    }

    /**
     * @return Model
     */
    public function get()
    {
        return $this->loadFromCache($this->cacheFilePath($this->rootDir));
    }


    /**
     * @param string $kernelDir
     * @return string
     */
    private function getSrcDir($kernelDir)
    {
        return $kernelDir.'/..'.DIRECTORY_SEPARATOR .'src';
    }


    /**
     * @param $path
     * @return mixed
     */
    protected function loadFromCache($path)
    {
        $cachePath = $this->cacheFilePath($path);

        if (!file_exists($cachePath)) {
            throw new CacheNotFoundException();
        }

        return unserialize(file_get_contents($cachePath));
    }

    /**
     * @param $path
     * @param $data
     */
    protected function writeCache($path, $data)
    {
        $cachePath = $this->cacheFilePath($path);

        file_put_contents($cachePath, serialize($data));
    }

    /**
     * @param $path
     * @return string
     */
    protected function cacheFilePath($path)
    {
        $cachename = md5(realpath($path));
        return $this->cacheDir.DIRECTORY_SEPARATOR.$cachename;
    }

}