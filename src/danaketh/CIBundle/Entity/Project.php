<?php

namespace danaketh\CIBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Project entity
 *
 * @ORM\Entity
 * @ORM\Table(name="CIBundle_Project")
 */
class Project
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     */
    protected $name;

    /**
     * @ORM\Column(type="string")
     */
    protected $reference;

    /**
     * @ORM\Column(type="string")
     */
    protected $type;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $last_commit;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $build_config;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $token;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $private_key;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $public_key;

    /**
     * @ORM\OneToMany(targetEntity="Build",
     *  mappedBy="project",
     *  orphanRemoval=true,
     *  fetch="EXTRA_LAZY",
     *  cascade={"persist", "remove", "merge"})
     */
    protected $builds;
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->builds = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set name
     *
     * @param string $name
     * @return Project
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set reference
     *
     * @param string $reference
     * @return Project
     */
    public function setReference($reference)
    {
        $this->reference = $reference;

        return $this;
    }

    /**
     * Get reference
     *
     * @return string
     */
    public function getReference()
    {
        return $this->reference;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return Project
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set last_commit
     *
     * @param string $lastCommit
     * @return Project
     */
    public function setLastCommit($lastCommit)
    {
        $this->last_commit = $lastCommit;

        return $this;
    }

    /**
     * Get last_commit
     *
     * @return string
     */
    public function getLastCommit()
    {
        return $this->last_commit;
    }

    /**
     * Set build_config
     *
     * @param string $buildConfig
     * @return Project
     */
    public function setBuildConfig($buildConfig)
    {
        $this->build_config = $buildConfig;

        return $this;
    }

    /**
     * Get build_config
     *
     * @return string
     */
    public function getBuildConfig()
    {
        return $this->build_config;
    }

    /**
     * Set token
     *
     * @param string $token
     * @return Project
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Get token
     *
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Set private_key
     *
     * @param string $privateKey
     * @return Project
     */
    public function setPrivateKey($privateKey)
    {
        $this->private_key = $privateKey;

        return $this;
    }

    /**
     * Get private_key
     *
     * @return string
     */
    public function getPrivateKey()
    {
        return $this->private_key;
    }

    /**
     * Set public_key
     *
     * @param string $publicKey
     * @return Project
     */
    public function setPublicKey($publicKey)
    {
        $this->public_key = $publicKey;

        return $this;
    }

    /**
     * Get public_key
     *
     * @return string
     */
    public function getPublicKey()
    {
        return $this->public_key;
    }

    /**
     * Add builds
     *
     * @param \danaketh\CIBundle\Entity\Build $builds
     * @return Project
     */
    public function addBuild(\danaketh\CIBundle\Entity\Build $builds)
    {
        $this->builds[] = $builds;

        return $this;
    }

    /**
     * Remove builds
     *
     * @param \danaketh\CIBundle\Entity\Build $builds
     */
    public function removeBuild(\danaketh\CIBundle\Entity\Build $builds)
    {
        $this->builds->removeElement($builds);
    }

    /**
     * Get builds
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBuilds()
    {
        return $this->builds;
    }
}
