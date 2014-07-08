<?php

namespace danaketh\CIBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Build meta data entity
 *
 * @ORM\Entity
 * @ORM\Table(name="CIBundle_BuildMeta")
 */
class BuildMeta
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
     * @ORM\Column(name="meta_key", type="string")
     */
    protected $metaKey;

    /**
     * @ORM\Column(name="meta_value", type="array")
     */
    protected $metaValue;

    /**
     * @ORM\ManyToOne(targetEntity="Build", inversedBy="data")
     * @ORM\JoinColumn(name="build_id", referencedColumnName="id")
     */
    protected $build;

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
     * @return $this
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
     * Set metaKey
     *
     * @param string $metaKey
     *
     * @return $this
     */
    public function setMetaKey($metaKey)
    {
        $this->metaKey = $metaKey;

        return $this;
    }

    /**
     * Get metaKey
     *
     * @return string
     */
    public function getMetaKey()
    {
        return $this->metaKey;
    }

    /**
     * Set build
     *
     * @param Build $build
     *
     * @return $this
     */
    public function setBuild(Build $build = null)
    {
        $this->build = $build;

        return $this;
    }

    /**
     * Get build
     *
     * @return $this
     */
    public function getBuild()
    {
        return $this->build;
    }

    /**
     * Set metaValue
     *
     * @param array $metaValue
     *
     * @return $this
     */
    public function setMetaValue($metaValue)
    {
        $this->metaValue = $metaValue;

        return $this;
    }

    /**
     * Get dataValue
     *
     * @return array
     */
    public function getMetaValue()
    {
        return $this->metaValue;
    }
}
