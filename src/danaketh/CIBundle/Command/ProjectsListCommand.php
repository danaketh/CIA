<?php

namespace danaketh\CIBundle\Command;

use danaketh\CIBundle\Entity\Project;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Security\Core\Util\SecureRandom;

class ProjectsListCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('ci:projects:list')
            ->setDescription('List projects');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $projects = $this->getContainer()->get('doctrine.orm.entity_manager')->getRepository('danakethCIBundle:Project')->findAll();

        if (count($projects) === 0) {
            $output->writeln('No projects found.');
            exit(0);
        }

        foreach ($projects as $p) {
            $output->writeln($p->getId() . ": " . $p->getName() . "\t" . $p->getReference() . " (" . $p->getType() . ")");
        }
    }
}
