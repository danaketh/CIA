<?php

namespace danaketh\CIBundle\Plugin;

use Symfony\Component\Process\Process;

/**
 * Class PHPUnit
 *
 * @package danaketh\CIBundle\Plugin
 */
class PHPUnit extends Plugin
{
    const ERROR = 'error';

    const WARNING = 'warning';

    /**
     * @var array(
     */
    protected $command = array(
        'bin/phpunit',
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
        $this->setCommandCoverage();
        $this->setCommandLog();
        $this->setCommandReportUselessTests();
        $this->setCommandStrictCoverage();
        $this->setCommandVerbose();
        $this->setCommandConfigurationFile();

        $process = new Process($this->getCommand());
        $process->run();

        if (!$process->isSuccessful()) {
            $this->setLog($process->getErrorOutput());
        } else {
            $this->setLog($process->getOutput());
        }

        $report = $this->parseReport();
        $coverage = $this->parseCoverage();

        $this->store('phpunit-tests', $report['tests']);
        $this->store('phpunit-assertions', $report['assertions']);
        $this->store('phpunit-errors', $report['errors']);
        $this->store('phpunit-failures', $report['failures']);
        $this->store('phpunit-time', $report['time']);
        $this->store('phpunit-data', $report['data']);
        $this->store('phpunit-coverage', $coverage);
    }

    /**
     * Set coverage
     */
    protected function setCommandCoverage()
    {
        $this->command[] = '--coverage-xml ' . $this->getTempFilePath() . 'coverage';
    }

    /**
     * Set log
     */
    protected function setCommandLog()
    {
        $this->command[] = '--log-junit ' . $this->getTempFilePath() . 'phpunit.log.xml';
    }

    /**
     * Report useless tests
     */
    protected function setCommandReportUselessTests()
    {
        $this->command[] = '--report-useless-tests';
    }

    /**
     * Report unintentionally covered code
     */
    protected function setCommandStrictCoverage()
    {
        $this->command[] = '--strict-coverage';
    }

    /**
     * Run each test in separate PHP process
     */
    protected function setCommandProcessIsolation()
    {
        $this->command[] = '--process-isolation';
    }

    /**
     * Verbose output
     */
    protected function setCommandVerbose()
    {
        $this->command[] = '--verbose';
    }

    /**
     * Set configuration file
     */
    protected function setCommandConfigurationFile()
    {
        if (isset($this->config['config'])) {
            $this->command[] = '--configuration '.$this->buildPath.$this->config['config'];
        } else {
            $this->command[] = '--configuration '.$this->buildPath.'/phpunit.xml.dist';
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
            'tests' => 0,
            'assertions' => 0,
            'errors' => 0,
            'failures' => 0,
            'time' => 0,
            'data' => array()
        );

        $report = simplexml_load_file($this->getTempFilePath() . 'phpunit.log.xml');

        $main = $report->testsuite;

        $return['tests'] = (integer) $main['tests'];
        $return['assertions'] = (integer) $main['assertions'];
        $return['errors'] = (integer) $main['errors'];
        $return['failures'] = (integer) $main['failures'];
        $return['time'] = (float) $main['time'];

        foreach ($main->testsuite as $testsuite) {
            $node = $this->prepareSuite($testsuite);

            foreach($testsuite->testsuite as $ts) {
                $subNode = $this->prepareSuite($ts);
                $subNode['data'] = $this->getTestCases($ts);
                $node['data'][] = $subNode;
            }

            $return['data'][] = $node;
        }

        return $return;
    }

    /**
     * @param \SimpleXmlElement $testsuite
     *
     * @return array
     */
    protected function prepareSuite($testsuite)
    {
        return array(
            'name' => (string) $testsuite['name'],
            'tests' => (integer) $testsuite['tests'],
            'assertions' => (integer) $testsuite['assertions'],
            'errors' => (integer) $testsuite['errors'],
            'failures' => (integer) $testsuite['failures'],
            'time' => (float) $testsuite['time'],
            'file' => isset($testsuite['file']) ? str_replace($this->buildPath, null, (string) $testsuite['file']) : null,
            'namespace' => isset($testsuite['namespace']) ? (string) $testsuite['namespace'] : null,
            'fullPackage' => isset($testsuite['fullPackage']) ? (string) $testsuite['fullPackage'] : null,
            'package' => isset($testsuite['package']) ? (string) $testsuite['package'] : null,
            'data' => array()
        );
    }

    /**
     * @param \SimpleXMLElement $testSuite
     *
     * @return array
     */
    protected function getTestCases($testSuite)
    {
        $arr = array();

        foreach ($testSuite->testcase as $tc) {
            $c = array(
                'name' => (string) $tc['name'],
                'class' => (string) $tc['class'],
                'file' => str_replace($this->buildPath, null, (string) $tc['file']),
                'line' => (integer) $tc['line'],
                'assertions' => (string) $tc['assertions'],
                'time' => (string) $tc['time'],
                'errors' => array()
            );

            foreach ($tc->error as $e) {
                $c['errors'][] = array(
                    'type' => (string) $e['type'],
                    'error' => str_replace($this->buildPath, null, (string) $e)
                );
            }

            $arr[] = $c;
        }

        return $arr;
    }

    /**
     * Parse coverage
     *
     * @return array
     */
    protected function parseCoverage()
    {
        return array();
    }
}
