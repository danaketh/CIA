<?php

namespace danaketh\CIBundle\Plugin;

use Symfony\Component\Process\Process;

/**
 * Class PHPCodeSniffer
 *
 * @package danaketh\CIBundle\Plugin
 */
class PHPCodeSniffer extends Plugin
{
    const ERROR = 'error';

    const WARNING = 'warning';

    /**
     * @var array(
     */
    protected $command = array(
        'bin/phpcs',
    );

    /**
     * @var string
     */
    protected $reportFile;

    /**
     * @return array
     */
    public function run()
    {
        $this->setCommandReportFile();
        $this->setCommandExtensions();
        $this->setCommandDirectories();

        $process = new Process($this->getCommand());
        $process->setTimeout($this->getTimeout());
        $process->run();

        if (!$process->isSuccessful()) {
            $this->setLog($process->getErrorOutput());
        } else {
            $this->setLog($process->getOutput());
        }

        $report = $this->parseReport();

        $this->store('phpcs-errors', $report['errors']);
        $this->store('phpcs-warnings', $report['warnings']);
        $this->store('phpcs-data', $report['data']);
    }

    /**
     * Set the report file
     */
    protected function setCommandReportFile()
    {
        $this->command[] = '--report-xml='.$this->buildPath.'/phpcs.report.xml';
        $this->reportFile = $this->buildPath.'/phpcs.report.xml';
    }

    /**
     * Set the extensions to analyze
     */
    protected function setCommandExtensions()
    {
        $this->command[] = '--extensions=php';
    }

    /**
     * Set working directories
     *
     * @return void
     */
    protected function setCommandDirectories()
    {
        if (!is_array($this->config['directories'])) {
            $this->command[] = $this->buildPath.'/'.$this->config['directories'];
        } else {
            $dirs = array();
            foreach ($this->config['directories'] as $dir) {
                $dirs[] = $this->buildPath.'/'.$dir;
            }
            $this->command[] = implode(' ', $dirs);
        }
    }

    /**
     * Parse the report file
     *
     * @return array
     */
    protected function parseReport()
    {
        $return = array(
            'errors' => 0,
            'warnings' => 0,
            'data' => array()
        );
        $report = simplexml_load_file($this->reportFile);

        foreach ($report->file as $file) {
            $fileName = str_replace($this->buildPath, null, (string) $file['name']);
            $data = array();
            $return['errors'] += (integer) $file['errors'];
            $return['warnings'] += (integer) $file['warnings'];

            foreach ($file->error as $e) {
                $data[] = array(
                    'type' => self::ERROR,
                    'line' => (integer) $e['line'],
                    'column' => (integer) $e['column'],
                    'severity' => (integer) $e['severity'],
                    'source' => (string) $e['source'],
                    'message' => (string) $e,
                );
            }

            foreach ($file->warning as $w) {
                $data[] = array(
                    'type' => self::WARNING,
                    'line' => (integer) $e['line'],
                    'column' => (integer) $e['column'],
                    'severity' => (integer) $e['severity'],
                    'source' => (string) $e['source'],
                    'message' => (string) $e,
                );
            }

            $return['data'][$fileName] = $data;
        }

        return $return;
    }
}
