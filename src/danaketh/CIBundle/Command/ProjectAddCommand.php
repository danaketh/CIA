<?php

namespace danaketh\CIBundle\Command;

use danaketh\CIBundle\Entity\Project;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Security\Core\Util\SecureRandom;

class ProjectAddCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('ci:project:add')
            ->setDescription('Add new project')
            ->addArgument('name', InputArgument::REQUIRED, 'Name your project')
            ->addOption('reference', null, InputOption::VALUE_REQUIRED, 'Valid reference/path to the repository')
            ->addOption('bitbucket', null, InputOption::VALUE_OPTIONAL, 'Set the project repository type to BitBucket')
            ->addOption('github', null, InputOption::VALUE_OPTIONAL, 'Set the project repository type to GitHub')
            ->addOption('remote', null, InputOption::VALUE_OPTIONAL, 'Set the project repository type to remote')
            ->addOption('config', null, InputOption::VALUE_OPTIONAL, 'Path to the file with build config to import')
            ->addOption('private-key', null, InputOption::VALUE_OPTIONAL, 'Path to the file with private key to import')
            ->addOption('public-key', null, InputOption::VALUE_OPTIONAL, 'Path to the file with public key to import');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $entityManager = $this->getContainer()->get('doctrine.orm.entity_manager');
        $name = $input->getArgument('name');
        $config = $input->getOption('config');
        $private_key = $input->getOption('private-key');
        $public_key = $input->getOption('public-key');
        $generator = new SecureRandom();

        $project = new Project();
        $project->setName($name);
        $project->setReference($input->getOption('reference'));
        $token = bin2hex($generator->nextBytes(20));
        $project->setToken($token);

        if ($input->getOption('bitbucket') == true) {
            $project->setType('bitbucket');
        } else if ($input->getOption('github') == true) {
            $project->setType('github');
        } else if ($input->getOption('remote') == true) {
            $project->setType('remote');
        } else {
            $project->setType('local');
        }

        if ($config && file_exists($config)) {
            $project->setBuildConfig(file_get_contents($config));
        }

        if ($public_key && file_exists($public_key)) {
            $project->setPublicKey(file_get_contents($public_key));
        }

        if ($private_key && file_exists($private_key)) {
            $project->setPrivateKey(file_get_contents($private_key));
        }

        $entityManager->persist($project);
        $entityManager->flush();

        $output->writeln('Project ' . $name . ' has been saved');
    }
}
