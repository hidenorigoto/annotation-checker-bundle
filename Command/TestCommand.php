<?php

namespace PHPMentors\AnnotationCheckerBundle\Command;

use PHPMentors\AnnotationCheckerBundle\Collector\TemplateAnnotationCollector;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TestCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('ac:test')
            ->setDescription('test')
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
        /** @var TemplateAnnotationCollector $collector */
        $collector = $this->getContainer()->get('annotation_checker.template_annotation_collector');
        $methods = $collector->collect();

        //var_dump($methods);

        $output->writeln("done");
    }
}
