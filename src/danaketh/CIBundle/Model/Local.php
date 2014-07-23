<?php

namespace danaketh\CIBundle\Model;

/**
 * Class Local
 *
 * @package danaketh\CIBundle\Model
 */
class Local extends Repository
{
    /**
     * @return string
     */
    public function getCloneUrl()
    {
        return $this->reference;
    }
}
