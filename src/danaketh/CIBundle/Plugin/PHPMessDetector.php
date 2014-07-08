<?php

namespace danaketh\CIBundle\Plugin;

use Symfony\Component\Process\Process;

/**
 * Class PHPMessDetector
 *
 * @package danaketh\CIBundle\Plugin
 */
class PHPMessDetector extends Plugin
{
    protected $reportFile;

    /**
     * @var array(
     */
    protected $command = array(
        'bin/phpmd'
    );

    /**
     * @return array
     */
    public function run()
    {
        $this->setCommandDirectories();
        $this->setCommandReportFormat();
        $this->setCommandRuleSet();
        $this->setCommandSuffixes();
        $this->setCommandReportFile();

        $process = new Process($this->getCommand());
        $process->run();

        if (!$process->isSuccessful()) {
            $output = $process->getErrorOutput();
        } else {
            $output = $process->getOutput();
        }

        $this->setLog($output);

        $report = $this->parseReport();

        if ($report['num'] > 0) {
            $this->setStatus(false);
        }

        $this->store('phpmd-errors', $report['num']);
        $this->store('phpmd-data', $report['errors']);
    }

    /**
     * Set working directories
     *
     * @return void
     */
    protected function setCommandDirectories()
    {
        if (!is_array($this->config['directories'])) {
            $this->command[1] = $this->buildPath.'/'.$this->config['directories'];
        } else {
            $dirs = array();
            foreach ($this->config['directories'] as $dir) {
                $dirs[] = $this->buildPath.'/'.$dir;
            }
            $this->command[1] = implode(',', $dirs);
        }
    }

    /**
     * Set report format
     *
     * @return void
     */
    protected function setCommandReportFormat()
    {
        $this->command[2] = 'xml';
    }

    /**
     * Set ruleset
     *
     * @return void
     */
    protected function setCommandRuleSet()
    {
        if (isset($this->config['rule_set'])) {
            $this->command[3] = $this->config['rule_set'];
        } else {
            $this->command[3] = 'cleancode,codesize,controversial,design,naming,unusedcode';
        }
    }

    /**
     * Set suffixes
     *
     * @return void
     */
    protected function setCommandSuffixes()
    {
        if (isset($this->config['suffixes'])) {
            $this->command[] = '--suffixes '.$this->config['suffixes'];
        } else {
            $this->command[] = '--suffixes php';
        }
    }

    /**
     * Set report file
     *
     * @return void
     */
    protected function setCommandReportFile()
    {
        $this->command[] = '--reportfile ' . $this->getTempFilePath() . 'phpmd.report.xml';
        $this->reportFile = $this->getTempFilePath() . 'phpmd.report.xml';
    }

    /**
     * Parse the report file
     *
     * @return array
     */
    protected function parseReport()
    {
        $return = array(
            'num' => 0,
            'errors' => array()
        );
        $report = simplexml_load_file($this->reportFile);
        foreach ($report->file as $file) {
            $violations = array();

            foreach($file->violation as $v) {
                $violations[] = array(
                    'beginsAt' => (integer) $v['beginline'],
                    'endsAt' => (integer) $v['endline'],
                    'rule' => (string) $v['rule'],
                    'ruleset' => (string) $v['ruleset'],
                    'infoUrl' => (string) $v['externalInfoUrl'],
                    'priority' => (integer) $v['priority'],
                    'message' => (string) $v
                );

                if ((integer) $v['priority'] < 3) {
                    $return['num']++;
                }
            }

            $return['errors'][] = array(
                'file' => str_replace($this->buildPath, null, $file['name']),
                'violations' => $violations,
            );
        }

        return $return;
    }
}
