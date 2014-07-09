<?php

namespace danaketh\CIBundle\Command;

use danaketh\CIBundle\Entity\Project;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Routing\Exception\InvalidParameterException;

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

        if ($project === null) {
            throw new \RuntimeException('Project not found!');
        }

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

        $publicKeyPath = $input->getOption('public-key');
        if ($publicKeyPath) {
            if (!file_exists($publicKeyPath)) {
                throw new InvalidParameterException('File '.$publicKeyPath.' not found');
            }

            $project->setPublicKey(file_get_contents($publicKeyPath));
        }

        $privateKeyPath = $input->getOption('private-key');
        if ($privateKeyPath) {
            if (!file_exists($privateKeyPath)) {
                throw new InvalidParameterException('File '.$privateKeyPath.' not found');
            }

            $project->setPrivateKey(file_get_contents($privateKeyPath));
        }

        $entityManager->persist($project);
        $entityManager->flush();

        $output->writeln('Changes to ' . $project->getName() . ' has been saved');
    }
}
