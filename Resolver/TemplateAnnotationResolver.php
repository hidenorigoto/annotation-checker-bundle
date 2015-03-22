<?php
namespace PHPMentors\AnnotationCheckerBundle\Resolver;

use DomainCoder\Metamodel\Code\Element\Annotation;
use DomainCoder\Metamodel\Code\Element\Method;
use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Bundle\FrameworkBundle\Templating\TemplateReference;
use Symfony\Component\Templating\Loader\LoaderInterface;
use Symfony\Component\Templating\TemplateNameParser;

/**
 * @DI\Service("annotation_checker.template_annotation_resolver")
 */
class TemplateAnnotationResolver
{
    /**
     * @var KernelInterface
     */
    private $kernel;
    /**
     * @var TemplateNameParser
     */
    private $templateNameParser;
    /**
     * @var LoaderInterface
     */
    private $loader;

    /**
     * @DI\InjectParams({
     *     "kernel" = @DI\Inject("kernel"),
     *     "templateNameParser" = @DI\Inject("templating.name_parser"),
     *     "loader" = @DI\Inject("templating.loader")
      * })
     */
    public function __construct(KernelInterface $kernel, TemplateNameParser $templateNameParser, LoaderInterface $loader)
    {
        $this->kernel = $kernel;
        $this->templateNameParser = $templateNameParser;
        $this->loader = $loader;
    }

    /**
     * @param Method $method
     * @return array
     */
    public function resolve(Method $method)
    {
        /** @var Annotation $annotation */
        $annotation = $method->annotations->withName('Template')->first();

        // テンプレート名を取得する
        $name = array_shift($annotation->parameters);

        if (!$name) {
            $templateReference = $this->guessTemplateName($method->class->getFQCN(), str_replace('Action', '', $method->name));
        } else {
            $templateReference = $this->templateNameParser->parse($this->kernel->getRootDir().'/Resources/views/'. $name);
        }

        $template = $this->loader->load($templateReference);

        return ['method' => $method, 'name' => $name, 'template' => $templateReference, 'path' => $template];
    }

    // modified from Sensio\Bundle\FrameworkExtraBundle\Templating\TemplateGuesser#guessTemplateName
    public function guessTemplateName($controllerClassName, $actionName)
    {
        if (!preg_match('/Controller\\\(.+)Controller$/', $controllerClassName, $matchController)) {
            throw new \InvalidArgumentException(sprintf(''));
        }

        $bundle = $this->getBundleForClass($controllerClassName);

        if ($bundle) {
            while ($bundleName = $bundle->getName()) {
                if (null === $parentBundleName = $bundle->getParent()) {
                    $bundleName = $bundle->getName();

                    break;
                }

                $bundles = $this->kernel->getBundle($parentBundleName, false);
                $bundle = array_pop($bundles);
            }
        } else {
            $bundleName = null;
        }

        return new TemplateReference($bundleName, $matchController[1], $actionName, 'html', 'twig');
    }

    /**
     * Returns the Bundle instance in which the given class name is located.
     *
     * @param  string      $class A fully qualified controller class name
     * @return Bundle|null $bundle A Bundle instance
     */
    protected function getBundleForClass($class)
    {
        $reflectionClass = new \ReflectionClass($class);
        $bundles = $this->kernel->getBundles();

        do {
            $namespace = $reflectionClass->getNamespaceName();
            foreach ($bundles as $bundle) {
                if (0 === strpos($namespace, $bundle->getNamespace())) {
                    return $bundle;
                }
            }
            $reflectionClass = $reflectionClass->getParentClass();
        } while ($reflectionClass);
    }

}