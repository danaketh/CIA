<?php

namespace danaketh\CIBundle\Command;

use danaketh\CIBundle\Entity\Project;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ProjectChangeCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('ci:project:change')
            ->setDescription('Change existing project')
            ->addArgument('id', InputArgument::REQUIRED, 'Project ID (use `ci:project:change` to get list of IDs')
            ->addOption('name', null, InputOption::VALUE_REQUIRED, 'Name of the project')
            ->addOption('reference', null, InputOption::VALUE_REQUIRED, 'Valid reference/path to the repository')
            ->addOption('bitbucket', null, InputOption::VALUE_OPTIONAL, 'Set the project repository type to BitBucket')
            ->addOption('github', null, InputOption::VALUE_OPTIONAL, 'Set the project repository type to GitHub')
            ->addOption('git', null, InputOption::VALUE_OPTIONAL, 'Set the project repository type to Git')
            ->addOption('config', null, InputOption::VALUE_OPTIONAL, 'Path to the file with build config to import')
            ->addOption('private-key', null, InputOption::VALUE_OPTIONAL, 'Path to the file with private key to import')
            ->addOption('public-key', null, InputOption::VALUE_OPTIONAL, 'Path to the file with public key to import');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $entityManager = $this->getContainer()->get('doctrine.orm.entity_manager');
        $id = $input->getArgument('id');
        $project = $entityManager->getRepository('danakethCIBundle:Project')->find($id);

        if ($input->getOption('name')) {
            $name = $input->getOption('name');
            if (preg_match('~"(.*?)"~', $name, $match)) {
                $name = $match[1];
            }
            $project->setName($name);
        }

        if ($input->getOption('reference')) {
            $project->setReference($input->getOption('reference'));
        }

        if ($input->getOption('bitbucket') == true) {
            $project->setType('bitbucket');
        } else if ($input->getOption('github') == true) {
            $project->setType('github');
        } else if ($input->getOption('git') == true) {
            $project->setType('git');
        }

        $config = $input->getOption('config');
        if ($config && file_exists($config)) {
            $project->setBuildConfig(file_get_contents($config));
        }

        $public_key = $input->getOption('public-key');
        if ($public_key && file_exists($public_key)) {
            $project->setPublicKey(file_get_contents($public_key));
        }

        $private_key = $input->getOption('private-key');
        if ($private_key && file_exists($private_key)) {
            $project->setPrivateKey(file_get_contents($private_key));
        }

        $entityManager->persist($project);
        $entityManager->flush();

        $output->writeln('Changes to ' . $project->getName() . ' has been saved');
    }
}
