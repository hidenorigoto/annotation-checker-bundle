<?php

namespace PHPMentors\AnnotationCheckerBundle\Command;

use PHPMentors\AnnotationCheckerBundle\Collector\TemplateAnnotationCollector;
use PHPMentors\AnnotationCheckerBundle\Provider\ModelProvider;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateCacheCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('ac:cache')
            ->setDescription('create cache')
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     * @codeCoverageIgnore
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var ModelProvider $modelProvider */
        $modelProvider = $this->getContainer()->get('annotation_checker.model_provider');
        $modelProvider->createCache();

        $output->writeln("done");
    }
}
