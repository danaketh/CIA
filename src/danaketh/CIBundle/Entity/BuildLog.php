<?php

namespace danaketh\CIBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Build entity
 *
 * @ORM\Entity
 * @ORM\Table(name="CIBundle_BuildLog")
 */
class BuildLog
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="build_id", type="integer")
     */
    protected $buildId;

    /**
     * @ORM\Column(type="text")
     */
    protected $command;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $log;

    /**
     * @ORM\Column(name="status", type="boolean")
     */
    protected $status;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $started;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $finished;

    /**
     * @ORM\ManyToOne(targetEntity="Build", inversedBy="log")
     * @ORM\JoinColumn(name="build_id", referencedColumnName="id")
     */
    protected $build;

    public function __construct()
    {
        $this->log = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set buildId
     *
     * @param integer $buildId
     *
     * @return BuildLog
     */
    public function setBuildId($buildId)
    {
        $this->buildId = $buildId;

        return $this;
    }

    /**
     * Get buildId
     *
     * @return integer
     */
    public function getBuildId()
    {
        return $this->buildId;
    }

    /**
     * Set command
     *
     * @param string $command
     *
     * @return BuildLog
     */
    public function setCommand($command)
    {
        $this->command = $command;

        return $this;
    }

    /**
     * Get command
     *
     * @return string
     */
    public function getCommand()
    {
        return $this->command;
    }

    /**
     * Set started
     *
     * @param \DateTime $started
     *
     * @return BuildLog
     */
    public function setStarted($started)
    {
        $this->started = $started;

        return $this;
    }

    /**
     * Get started
     *
     * @return \DateTime
     */
    public function getStarted()
    {
        return $this->started;
    }

    /**
     * Set finished
     *
     * @param \DateTime $finished
     *
     * @return BuildLog
     */
    public function setFinished($finished)
    {
        $this->finished = $finished;

        return $this;
    }

    /**
     * Get finished
     *
     * @return \DateTime
     */
    public function getFinished()
    {
        return $this->finished;
    }

    /**
     * Set build
     *
     * @param \danaketh\CIBundle\Entity\Build $build
     *
     * @return BuildLog
     */
    public function setBuild(\danaketh\CIBundle\Entity\Build $build = null)
    {
        $this->build = $build;

        return $this;
    }

    /**
     * Get build
     *
     * @return \danaketh\CIBundle\Entity\Build
     */
    public function getBuild()
    {
        return $this->build;
    }

    /**
     * Set log
     *
     * @param array $log
     *
     * @return BuildLog
     */
    public function setLog($log)
    {
        $this->log = $log;

        return $this;
    }

    /**
     * Get log
     *
     * @return array
     */
    public function getLog()
    {
        return $this->log;
    }

    /**
     * Set status
     *
     * @param bool $status
     *
     * @return BuildLog
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return bool
     */
    public function getStatus()
    {
        return $this->status;
    }
}
