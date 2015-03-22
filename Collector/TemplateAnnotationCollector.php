<?php

namespace PHPMentors\AnnotationCheckerBundle\Collector;


use DomainCoder\Metamodel\Code\Element\Method\MethodCollection;
use PHPMentors\AnnotationCheckerBundle\Provider\ModelProvider;
use JMS\DiExtraBundle\Annotation as DI;
use PHPMentors\AnnotationCheckerBundle\Resolver\TemplateAnnotationResolver;

/**
 * @DI\Service("annotation_checker.template_annotation_collector")
 */
class TemplateAnnotationCollector
{
    /**
     * @var ModelProvider
     */
    private $modelProvider;
    /**
     * @var TemplateAnnotationResolver
     */
    private $templateAnnotationResolver;

    /**
     * @param ModelProvider $modelProvider
     * @param TemplateAnnotationResolver $templateAnnotationResolver
     * @DI\InjectParams({
     *  "modelProvider" = @DI\Inject("annotation_checker.model_provider"),
     *  "templateAnnotationResolver" = @DI\Inject("annotation_checker.template_annotation_resolver")
     * })
     */
    public function __construct(ModelProvider $modelProvider, TemplateAnnotationResolver $templateAnnotationResolver)
    {
        $this->modelProvider = $modelProvider;
        $this->templateAnnotationResolver = $templateAnnotationResolver;
    }

    /**
     * @return array
     */
    public function collect()
    {
        return $this->modelProvider->get()
            ->methodCollection
            ->withAnnotationName('Template')
            ->map(function($annotation) {
                return $this->templateAnnotationResolver->resolve($annotation);
            });
    }
}