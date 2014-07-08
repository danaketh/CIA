<?php

namespace danaketh\CIBundle\Entity;

use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;

/**
 * Build entity
 *
 * @ORM\Entity
 * @ORM\Table(name="CIBundle_Build")
 */
class Build
{
    const PENDING = 0;
    const RUNNING = 1;
    const SUCCESS = 2;
    const FAILED = -1;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="project_id", type="integer")
     */
    protected $projectId;

    /**
     * @ORM\Column(type="integer")
     */
    protected $build;

    /**
     * @ORM\Column(type="integer")
     */
    protected $status;

    /**
     * @ORM\Column(type="string")
     */
    protected $branch;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $created;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $started;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $finished;

    /**
     * @ORM\Column(name="commit_hash", type="string")
     */
    protected $commitHash;

    /**
     * @ORM\Column(name="commit_author", type="string", nullable=true)
     */
    protected $commitAuthor;

    /**
     * @ORM\Column(name="commit_email", type="string", nullable=true)
     */
    protected $commitEmail;

    /**
     * @ORM\Column(name="commit_message", type="text", nullable=true)
     */
    protected $commitMessage;

    /**
     * @ORM\ManyToOne(targetEntity="Project", inversedBy="builds")
     * @ORM\JoinColumn(name="project_id", referencedColumnName="id")
     */
    protected $project;

    /**
     * @ORM\OneToMany(targetEntity="BuildLog",
     *  mappedBy="build",
     *  orphanRemoval=true,
     *  fetch="EXTRA_LAZY",
     *  cascade={"persist", "remove", "merge"})
     */
    protected $log;

    /**
     * @ORM\OneToMany(targetEntity="BuildMeta",
     *  mappedBy="build",
     *  orphanRemoval=true,
     *  fetch="EXTRA_LAZY",
     *  cascade={"persist", "remove", "merge"})
     */
    protected $meta;
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->log = new \Doctrine\Common\Collections\ArrayCollection();
        $this->meta = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set projectId
     *
     * @param integer $projectId
     * @return Build
     */
    public function setProjectId($projectId)
    {
        $this->projectId = $projectId;

        return $this;
    }

    /**
     * Get projectId
     *
     * @return integer
     */
    public function getProjectId()
    {
        return $this->projectId;
    }

    /**
     * Set build
     *
     * @param integer $build
     * @return Build
     */
    public function setBuild($build)
    {
        $this->build = $build;

        return $this;
    }

    /**
     * Get build
     *
     * @return integer
     */
    public function getBuild()
    {
        return $this->build;
    }

    /**
     * Set status
     *
     * @param integer $status
     * @return Build
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return integer
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set branch
     *
     * @param string $branch
     * @return Build
     */
    public function setBranch($branch)
    {
        $this->branch = $branch;

        return $this;
    }

    /**
     * Get branch
     *
     * @return string
     */
    public function getBranch()
    {
        return $this->branch;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return Build
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @param string $format
     *
     * @return \DateTime
     */
    public function getCreated($format = null)
    {
        if ($format !== null) {
            return $this->created->format($format);
        }

        return $this->created;
    }

    /**
     * Set started
     *
     * @param string $started
     * @return Build
     */
    public function setStarted($started)
    {
        $this->started = $started;

        return $this;
    }

    /**
     * Get started
     *
     * @param string $format
     *
     * @return string
     */
    public function getStarted($format = null)
    {
        if ($format !== null && $this->started !== null) {
            return $this->started->format($format);
        }

        return $this->started;
    }

    /**
     * Set finished
     *
     * @param string $finished
     * @return Build
     */
    public function setFinished($finished)
    {
        $this->finished = $finished;

        return $this;
    }

    /**
     * Get finished
     *
     * @param string $format
     *
     * @return string
     */
    public function getFinished($format = null)
    {
        if ($format !== null && $this->finished !== null) {
            return $this->finished->format($format);
        }

        return $this->finished;
    }

    /**
     * Set commitHash
     *
     * @param string $commitHash
     * @return Build
     */
    public function setCommitHash($commitHash)
    {
        $this->commitHash = $commitHash;

        return $this;
    }

    /**
     * Get commitHash
     *
     * @return string
     */
    public function getCommitHash()
    {
        return $this->commitHash;
    }

    /**
     * Set commitAuthor
     *
     * @param string $commitAuthor
     * @return Build
     */
    public function setCommitAuthor($commitAuthor)
    {
        $this->commitAuthor = $commitAuthor;

        return $this;
    }

    /**
     * Get commitAuthor
     *
     * @return string
     */
    public function getCommitAuthor()
    {
        return $this->commitAuthor;
    }

    /**
     * Set commitEmail
     *
     * @param string $commitEmail
     * @return Build
     */
    public function setCommitEmail($commitEmail)
    {
        $this->commitEmail = $commitEmail;

        return $this;
    }

    /**
     * Get commitEmail
     *
     * @return string
     */
    public function getCommitEmail()
    {
        return $this->commitEmail;
    }

    /**
     * Set commitMessage
     *
     * @param string $commitMessage
     * @return Build
     */
    public function setCommitMessage($commitMessage)
    {
        $this->commitMessage = $commitMessage;

        return $this;
    }

    /**
     * Get commitMessage
     *
     * @return string
     */
    public function getCommitMessage()
    {
        return $this->commitMessage;
    }

    /**
     * Set project
     *
     * @param \danaketh\CIBundle\Entity\Project $project
     * @return Build
     */
    public function setProject(\danaketh\CIBundle\Entity\Project $project = null)
    {
        $this->project = $project;

        return $this;
    }

    /**
     * Get project
     *
     * @return \danaketh\CIBundle\Entity\Project
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
     * Add log
     *
     * @param \danaketh\CIBundle\Entity\BuildLog $log
     * @return Build
     */
    public function addLog(\danaketh\CIBundle\Entity\BuildLog $log)
    {
        $this->log[] = $log;

        return $this;
    }

    /**
     * Remove log
     *
     * @param \danaketh\CIBundle\Entity\BuildLog $log
     */
    public function removeLog(\danaketh\CIBundle\Entity\BuildLog $log)
    {
        $this->log->removeElement($log);
    }

    /**
     * Get log
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getLog()
    {
        return $this->log;
    }

    /**
     * Get meta
     *
     * @param string $plugin
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMeta($plugin)
    {
        $criteria = Criteria::create()
            ->where(Criteria::expr()->contains("metaKey", $plugin.'%'));

        $rows = $this->meta->matching($criteria);

        $return = array();

        foreach ($rows as $row) {
            $return[str_replace($plugin.'-', null, $row->getMetaKey())] = $row->getMetaValue();
        }

        return $return;
    }

    /**
     * Add data
     *
     * @param \danaketh\CIBundle\Entity\Build $data
     * @return Build
     */
    public function addDatum(\danaketh\CIBundle\Entity\Build $data)
    {
        $this->data[] = $data;

        return $this;
    }

    /**
     * Remove data
     *
     * @param \danaketh\CIBundle\Entity\Build $data
     */
    public function removeDatum(\danaketh\CIBundle\Entity\Build $data)
    {
        $this->data->removeElement($data);
    }

    /**
     * Add meta
     *
     * @param \danaketh\CIBundle\Entity\BuildMeta $meta
     * @return Build
     */
    public function addMeta(\danaketh\CIBundle\Entity\BuildMeta $meta)
    {
        $this->meta[] = $meta;

        return $this;
    }

    /**
     * Remove meta
     *
     * @param \danaketh\CIBundle\Entity\BuildMeta $meta
     */
    public function removeMeta(\danaketh\CIBundle\Entity\BuildMeta $meta)
    {
        $this->meta->removeElement($meta);
    }
}
