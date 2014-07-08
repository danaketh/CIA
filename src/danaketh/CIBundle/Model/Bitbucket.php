<?php

namespace danaketh\CIBundle\Model;

/**
 * Class Bitbucket
 *
 * @package danaketh\CIBundle\Model
 */
class Bitbucket extends Repository
{
    /**
     * @return string
     */
    public function getCloneUrl()
    {
        if ($this->private_key !== null) {
            return 'git@bitbucket.org:' . $this->reference . '.git';
        }

        return 'https://bitbucket.org/' . $this->reference . '.git';
    }
}
