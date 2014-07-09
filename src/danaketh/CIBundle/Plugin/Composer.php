<?php

namespace danaketh\CIBundle\Plugin;

use Symfony\Component\Process\Process;

/**
 * Class Composer
 *
 * @package danaketh\CIBundle\Plugin
 */
class Composer extends Plugin
{
    /**
     * @var array(
     */
    protected $command = array(
        'composer'
    );

    /**
     * @return array
     */
    public function run()
    {
        $this->setCommandAction();
        $this->setCommandNoAnsi();
        $this->setCommandNoInteraction();
        $this->setCommandPreferSource();
        $this->setCommandWorkingDir();

        $process = new Process($this->getCommand());
        $process->setTimeout($this->getTimeout());
        $process->run();

        if (!$process->isSuccessful()) {
            $this->setStatus(false);
            $this->setLog($process->getErrorOutput());
        } else {
            $this->setLog($process->getOutput());
        }
    }

    /**
     * Set action
     *
     * @return void
     */
    protected function setCommandAction()
    {
        if (isset($this->config['action'])) {
            $this->command[] = $this->config['action'];
        } else {
            $this->command[] = 'install';
        }
    }

    /**
     * @return void
     */
    protected function setCommandNoAnsi()
    {
       $this->command[] = '--no-ansi';
    }

    /**
     * @return void
     */
    protected function setCommandNoInteraction()
    {
        $this->command[] = '--no-interaction';
    }

    /**
     * @return void
     */
    protected function setCommandPreferSource()
    {
        $this->command[] = '--prefer-source';
    }

    /**
     * @return void
     */
    protected function setCommandWorkingDir()
    {
        $this->command[] = '--working-dir=' . $this->buildPath;
    }
}
