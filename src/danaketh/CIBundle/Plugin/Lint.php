<?php

namespace danaketh\CIBundle\Plugin;

use Symfony\Component\Process\Process;

/**
 * Class Lint
 *
 * @package danaketh\CIBundle\Plugin
 */
class Lint extends Plugin
{
    /**
     * @var array(
     */
    protected $command = array(
        'bin/parallel-lint'
    );

    /**
     * @return array
     */
    public function run()
    {
        $this->setCommandExcludes();
        $this->setCommandDirectory();

        $process = new Process($this->getCommand());
        $process->run();

        $output = $process->getOutput();
        $this->setLog($output);
        $errors = array();

        if (!preg_match('~no syntax error found~', $output)) {
            $this->setStatus(false);
            $errors = $this->parseOutput($output);
        }

        $this->store('lint-errors', count($errors));
        $this->store('lint-data', $errors);
    }

    /**
     * Set directory on which the command will run
     *
     * @return void
     */
    protected function setCommandDirectory()
    {
        $this->command[] = $this->config['directory'] == '.' ? $this->buildPath : $this->buildPath . $this->config['directory'];
    }

    /**
     * Set excluded directories
     *
     * @return void
     */
    protected function setCommandExcludes()
    {
        if (!isset($this->config['excludes'])) {
            return;
        }

        foreach ($this->config['excludes'] as $dir) {
            $this->command[] = '--exclude ' . $this->buildPath . $dir;
        }
    }

    /**
     * @param string $output
     *
     * @return array
     */
    protected function parseOutput($output)
    {
        $matches = array();
        $return = array();
        if (preg_match_all('/Parse error\:/', $output, $matches)) {
            $errors = explode('------------------------------------------------------------', $output);
            unset($errors[0]);
            foreach ($errors as $error) {
                preg_match("/^Parse error: (.*):([0-9]{1,5})(.*)\n(.*?)\n$/sm", $error, $err);
                $return[] = array(
                    'file' => str_replace($this->buildPath, '', $err[1]),
                    'line' => $err[2],
                    'source' => explode("\n", $err[3]),
                    'error' => $err[4]
                );
            }
        }

        return $return;
    }
}
