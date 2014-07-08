<?php

namespace danaketh\CIBundle\Model;

use Symfony\Component\Process\Process;

abstract class Repository
{
    /**
     * @var string
     */
    protected $branch = 'master';

    /**
     * @var string
     */
    protected $commit;

    /**
     * @var string
     */
    protected $private_key;

    /**
     * @var string
     */
    protected $reference;

    /**
     * @var string
     */
    protected $repositoryPath;

    /**
     * @param string $reference
     */
    public function __construct($reference)
    {
        $this->reference = $reference;
    }

    /**
     * @param string $branch
     */
    public function setBranch($branch)
    {
        $this->branch = $branch;
    }

    /**
     * @param string $key
     */
    public function setPrivateKey($key)
    {
        $this->private_key = trim($key);
    }

    /**
     * @return string
     */
    public function getCloneUrl()
    {
        return $this->reference;
    }

    /**
     * @param string $target
     *
     * @return Process
     */
    public function runClone($target = './')
    {
        $this->repositoryPath = $target;
        $cmd = 'git clone -b ' . $this->branch . ' ' . $this->getCloneUrl() . ' "' . $target . '"';
        $keyFile = null;
        $sshWrapper = null;
        $return = array(
            'status' => false,
            'output' => ''
        );

        if ($this->private_key !== null) {
            $keyFile = $this->writeSSHKey($target);
            $sshWrapper = $this->writeSSHWrapper($target, $keyFile);
            $cmd = 'export GIT_SSH="' . $sshWrapper . '" && ' . $cmd;
        }

        $process = new Process($cmd);
        $process->run();

        if ($this->private_key !== null) {
            if (file_exists($keyFile)) {
                unlink($keyFile);
            }
            if (file_exists($sshWrapper)) {
                unlink($sshWrapper);
            }
        }

        if (!$process->isSuccessful()) {
            $return['output'] = $process->getErrorOutput();
        } else {
            $return['status'] = true;
            $return['output'] = $process->getOutput();
        }

        return $return;
    }

    /**
     * @param string $commit
     *
     * @return array
     */
    public function checkoutCommit($commit)
    {
        $return = array(
            'status' => false,
            'output' => ''
        );

        if ($this->repositoryPath === null) {
            $return['output'] = 'Missing path to the repository';
            return $return;
        }

        $cwd = getcwd();
        chdir($this->repositoryPath);
        $cmd = 'git checkout ' . $commit;
        $process = new Process($cmd);
        $process->run();

        if (!$process->isSuccessful()) {
            $return['output'] = $process->getErrorOutput();
            return $return;
        }

        chdir($cwd);

        $return['status'] = true;
        $return['output'] = $process->getOutput();
        return $return;
    }

    /**
     * @param string $branch
     *
     * @return array
     */
    public function checkoutBranch($branch)
    {
        $return = array(
            'status' => false,
            'output' => ''
        );

        if ($this->repositoryPath === null) {
            $return['output'] = 'Missing path to the repository';
            return $return;
        }

        $cwd = getcwd();
        chdir($this->repositoryPath);
        $cmd = 'git branch ' . $branch;

        $process = new Process($cmd);
        $process->run();

        if (!$process->isSuccessful()) {
            $return['output'] = $process->getErrorOutput();
            return $return;
        }

        $return['status'] = true;
        return $return;
    }

    /**
     * Write the SSH key to the file.
     *
     * @param $target
     *
     * @return string
     */
    protected function writeSSHKey($target)
    {
        $path = dirname($target . '/ssh');
        $file = $path . '.key';

        file_put_contents($file, $this->private_key);
        chmod($file, 0600);

        return $file;
    }

    /**
     * @param $target
     * @param $keyFilePath
     *
     * @return string
     */
    protected function writeSSHWrapper($target, $keyFilePath)
    {
        $path = dirname($target . '/ssh');
        $file = $path . '.sh';

        $cmd = <<<OUT
#!/bin/sh
ssh -o CheckHostIP=no -o IdentitiesOnly=yes -o StrictHostKeyChecking=no -o PasswordAuthentication=no -o IdentityFile={$keyFilePath} $*

OUT;
        file_put_contents($file, $cmd);
        chmod($file, 0777);

        return $file;
    }
}
