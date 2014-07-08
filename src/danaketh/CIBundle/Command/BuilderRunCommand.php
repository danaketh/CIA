<?php

namespace danaketh\CIBundle\Command;

use danaketh\CIBundle\Entity\Build;
use danaketh\CIBundle\Entity\BuildData;
use danaketh\CIBundle\Entity\BuildLog;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;
use Symfony\Component\Yaml\Yaml;

/**
 * Class BuilderRunCommand
 *
 * @package danaketh\CIBundle\Command
 */
class BuilderRunCommand extends ContainerAwareCommand
{
    /**
     * @var bool
     */
    protected $buildStatus = Build::SUCCESS;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $entityManager;

    /**
     * @var array
     */
    protected $services;

    /**
     * @var array
     */
    protected $plugins;

    /**
     * @var array
     */
    protected $environments;

    /**
     * @var \Monolog\Logger
     */
    protected $logger;

    /**
     * @var \danaketh\CIBundle\Entity\Build
     */
    protected $build;

    /**
     * @var \danaketh\CIBundle\Entity\Project
     */
    protected $project;

    /**
     * @var string
     */
    protected $buildPath;

    /**
     * @var string
     */
    protected $buildHash;

    /**
     * @var array
     */
    protected $projectConfig;

    /**
     * @var \danaketh\CIBundle\Model\Repository
     */
    protected $repository;

    /**
     * @param integer $status
     */
    protected function setBuildStatus($status)
    {
        $this->buildStatus = $status;
    }

    /**
     * Set the command
     */
    protected function configure()
    {
        $this
            ->setName('ci:builder:run')
            ->setDescription('Run the project builder');
    }

    /**
     * Initialize the build. Find a build to process and load it's project.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->entityManager = $this->getContainer()->get('doctrine.orm.entity_manager');
        $this->services = $this->getContainer()->getParameter('danaketh_ci.services');
        $this->plugins = $this->getContainer()->getParameter('danaketh_ci.plugins');
        $this->environments = $this->getContainer()->getParameter('danaketh_ci.env');
        $this->logger = $this->getContainer()->get('logger');

        $this->build = $this->entityManager->getRepository('danakethCIBundle:Build')->findOneBy(array(
                'status' => 0
            ), array(
                'created' => 'asc'
            ));

        if ($this->build === null) {
            $this->logger->info('Queue empty');
            $output->writeln('No builds queued. You should probably go and write down some more code...');
            exit(0);
        }

        $this->project = $this->build->getProject();
    }

    /**
     * Verifies the project's type and return an error in case the type is unknown.
     *
     * @param OutputInterface $output
     */
    protected function verifyService(OutputInterface $output)
    {
        if (!array_key_exists($this->project->getType(), $this->services)) {
            $this->logger->error('Service ' . $this->project->getType() . ' not found!');
            exit(1);
        }
    }

    /**
     * Verifies the build directory and performs a clean-up in case it already exists.
     *
     * @param OutputInterface $output
     */
    protected function verifyBuildDirectory(OutputInterface $output)
    {
        $this->repository = new $this->services[$this->project->getType()]($this->project->getReference());

        if ($this->project->getPrivateKey() !== null) {
            $this->repository->setPrivateKey($this->project->getPrivateKey());
        }

        $this->buildHash = sha1($this->build->getProjectId().$this->build->getBuild().$this->build->getCreated('Y-m-d H:i:s'));
        $this->buildPath = getcwd().'/build/'.$this->buildHash;

        if (is_dir($this->buildPath)) {
            // drop the build files
            $output->writeln('Cleaning up after possibly broken build...');
            $process = new Process('rm -Rf '.$this->buildPath);
            $process->run();

            if (!$process->isSuccessful()) {
                $this->logger->error($process->getErrorOutput());
                exit(1);
            }
        }

        if (!mkdir($this->buildPath)) {
            $this->logger->error('Unable to create a build directory '.$this->buildPath);
            exit(1);
        }
    }

    /**
     * @param OutputInterface $output
     */
    protected function startBuild(OutputInterface $output)
    {
        $this->build->setStarted(new \DateTime('now'));
        $this->build->setStatus(Build::RUNNING);
        $this->entityManager->persist($this->build);
        $this->entityManager->flush();

        $output->writeln('Building '.$this->project->getName().' #'.$this->build->getBuild());
    }

    /**
     * @param OutputInterface $output
     */
    protected function finishBuild(OutputInterface $output)
    {
        $output->writeln('Removing build');
        $process = new Process('rm -Rf '.$this->buildPath);
        $process->run();

        if (!$process->isSuccessful()) {
            $this->logger->error($process->getErrorOutput());
        }

        if ($this->buildStatus === Build::SUCCESS) {
            $this->build->setStatus(Build::SUCCESS);
            $status = '<info>SUCCESS</info>';
        } else {
            $this->build->setStatus(Build::FAILED);
            $status = '<error>FAILED</error>';
        }

        $this->build->setFinished(new \DateTime('now'));
        $this->entityManager->persist($this->build);
        $this->entityManager->flush();
        $output->writeln('Done with status ' . $status);

        exit(0);
    }

    /**
     * @param OutputInterface $output
     */
    protected function cloneRepository(OutputInterface $output)
    {
        $output->write('Cloning repository');

        $log = new BuildLog();
        $log->setBuild($this->build);
        $log->setCommand('git clone');
        $log->setStarted(new \DateTime('now'));
        $clone = $this->repository->runClone($this->buildPath);
        $log->setStatus($clone['status']);
        $log->setLog($clone['output']);
        $log->setFinished(new \DateTime('now'));
        $this->entityManager->persist($log);
        $this->entityManager->flush();
        $result = $log->getLog();

        if ($result['success'] === false) {
            $this->setBuildStatus(Build::FAILED);
            $output->writeln(' <error>ERROR</error>');
            $this->finishBuild($output);
            return;
        }

        $output->writeln(' <info>OK</info>');
    }

    /**
     * @param OutputInterface $output
     */
    protected function preBuildSetup(OutputInterface $output)
    {
        $output->write('Checking out branch `' . $this->build->getBranch() . '` and commit `' . $this->build->getCommitHash() . '`');

        $log = new BuildLog();
        $log->setBuild($this->build);
        $log->setCommand('git checkout');
        $log->setStarted(new \DateTime('now'));
        $checkout = $this->repository->checkoutCommit($this->build->getCommitHash());
        $log->setStatus($checkout['status']);
        $log->setLog($checkout['output']);
        $log->setFinished(new \DateTime('now'));
        $this->entityManager->persist($log);
        $this->entityManager->flush();

        if ($checkout['status'] === false) {
            $this->setBuildStatus(Build::FAILED);
            $output->writeln(' <error>ERROR</error>');
            $this->finishBuild($output);
            return;
        }

        $output->writeln(' <info>OK</info>');
    }

    /**
     * @param OutputInterface $output
     */
    protected function configureBuild(OutputInterface $output)
    {
        $log = new BuildLog();
        $log->setBuild($this->build);
        $log->setCommand('configure');
        $log->setStarted(new \DateTime('now'));

        if (!file_exists($this->buildPath.'/cia.yml')) {
            $log->setStatus(false);
            $log->setLog('Project build file `cia.yml` not found!');
            $log->setFinished(new \DateTime('now'));
            $this->entityManager->persist($log);
            $this->entityManager->flush();
            $this->logger->error('Project build file `cia.yml` not found!');
            $this->setBuildStatus(Build::FAILED);
            $this->finishBuild($output);
            return;
        }

        $locator = new FileLocator(array(
            $this->buildPath
        ));

        $configFile = $locator->locate('cia.yml', null, false);
        $this->projectConfig = Yaml::parse($configFile[0]);

        $log->setStatus(true);
        $log->setLog('`cia.yml` found and loaded...');
        $log->setFinished(new \DateTime('now'));
        $this->entityManager->persist($log);
        $this->entityManager->flush();
    }

    /**
     * @param OutputInterface $output
     */
    protected function setupBuild(OutputInterface $output)
    {
        $output->writeln('Running setup commands...');

        foreach ($this->projectConfig['setup'] as $cmd => $config) {
            if (isset($this->plugins[$cmd])) {
                $plugin = $this->plugins[$cmd];
                $output->write('Running ' . $plugin['name']);
                $result = $this->runPlugin($plugin, $config);
                if ($result === true) {
                    $output->writeln(' <info>OK</info>');
                }
                else {
                    $this->setBuildStatus(Build::FAILED);
                    $output->writeln(' <error>ERROR</error>');
                }
            }
        }
    }

    /**
     * @param OutputInterface $output
     */
    protected function runPlugins(OutputInterface $output)
    {
        foreach ($this->projectConfig['test'] as $id => $config) {
            if (isset($this->plugins[$id])) {
                $plugin = $this->plugins[$id];
                $output->write('Running ' . $plugin['name']);
                $result = $this->runPlugin($plugin, $config);
                if ($result === true) {
                    $output->writeln(' <info>OK</info>');
                }
                else {
                    $this->setBuildStatus(Build::FAILED);
                    $output->writeln(' <error>ERROR</error>');
                }
            }
        }
    }

    /**
     * Run plugin
     *
     * @param array $plugin
     * @param array $config
     *
     * @return bool
     */
    protected function runPlugin($plugin, $config)
    {
        // Log the run
        $log = new BuildLog();
        $log->setBuild($this->build);
        $log->setCommand($plugin['name']);
        $log->setStarted(new \DateTime('now'));

        if (!is_array($config)) {
            $this->setBuildStatus(Build::FAILED);
            $log->setStatus(false);
            $log->setLog('Configuration has to be an array. <code>' . gettype($config) . '</code> given.');
        } else {
            $class = $plugin['class'];
            $plugin = new $class($this->entityManager, $this->build);
            $plugin->setConfig($config);
            $plugin->setEnvironments($this->environments);
            $plugin->setBuildPath($this->buildPath);
            $plugin->run();
            $log->setStatus($plugin->getStatus());
            $log->setLog($plugin->getLog());
        }

        $log->setFinished(new \DateTime('now'));

        $this->entityManager->persist($log);
        $this->entityManager->flush();

        return $log->getStatus();
    }

    /**
     * Execute the whole building process
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->verifyService($output);

        $this->verifyBuildDirectory($output);

        $this->startBuild($output);

        $this->cloneRepository($output);

        $this->preBuildSetup($output);

        $this->configureBuild($output);

        $this->setupBuild($output);

        $this->runPlugins($output);

        //$this->finishBuild($output);
    }
}
