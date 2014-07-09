<?php

namespace danaketh\CIBundle\Plugin;

use danaketh\CIBundle\Entity\Build;
use danaketh\CIBundle\Entity\BuildMeta;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Validator\Constraints\Time;

/**
 * Class Plugin
 *
 * @package danaketh\CIBundle\Plugin
 */
abstract class Plugin
{
    /**
     * @var array
     */
    protected $command;

    /**
     * @var array
     */
    protected $environments;

    /**
     * @var string
     */
    protected $buildPath;

    /**
     * @var array
     */
    protected $config = array();

    /**
     * @var \danaketh\CIBundle\Entity\BuildMeta
     */
    protected $meta;

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var Build
     */
    protected $build;

    /**
     * @var bool
     */
    protected $status = true;

    /**
     * @var string
     */
    protected $log;

    /**
     * @param EntityManager $em
     * @param Build         $build
     */
    public function __construct(EntityManager $em, Build $build)
    {
        $this->build = $build;
        $this->entityManager = $em;
    }

    /**
     * Run the plugin
     *
     * @throws \ErrorException
     */
    public function run()
    {
        throw new \ErrorException('Plugin::run() method has to be implemented by each plugin.');
    }

    /**
     * Build command
     *
     * @return string
     */
    protected function getCommand()
    {
        return implode(' ', $this->command);
    }

    /**
     * @param array $config
     */
    public function setConfig(array $config)
    {
        $this->config = $config;
    }

    /**
     * @param array $env
     */
    public function setEnvironments(array $env)
    {
        $this->environments = $env;
    }

    /**
     * @param string $path
     */
    public function setBuildPath($path)
    {
        $this->buildPath = $path;
    }

    /**
     * @return string
     */
    protected function getTempFilePath()
    {
        return $this->buildPath . '_';
    }

    /**
     * @return bool
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param bool $status
     */
    protected function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return string
     */
    public function getLog()
    {
        return $this->log;
    }

    /**
     * @param string $log
     */
    protected function setLog($log)
    {
        $this->log = $log;
    }

    /**
     * @return integer
     */
    protected function getTimeout()
    {
        preg_match('~^([0-9].*?)([s|m|h].*?)$~', $this->config['timeout'], $timeout);

        if (isset($timeout[2])) {
            switch($timeout[2]) {
                case 's':
                    return $timeout[1];
                case 'm':
                    return $timeout[1] * 60;
                case 'h':
                    return $timeout[1] * 3600;
                default:
                    return 60;
            }
        }

        return 60;
    }

    /**
     * @param string $key
     * @param mixed  $value
     */
    protected function store($key, $value)
    {
        $this->meta = new BuildMeta();
        $this->meta->setBuild($this->build);
        $this->meta->setMetaKey($key);
        $this->meta->setMetaValue($value);
        $this->entityManager->persist($this->meta);
        $this->entityManager->flush();
    }
}