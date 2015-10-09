<?php

namespace Mi\PuliBundlePlugins;

use Puli\Repository\Api\ResourceRepository;

/**
 * @author Alexander Miehe <alexander.miehe@movingimage.com>
 */
interface ResourceRepositoryAwareInterface
{
    /**
     * @param ResourceRepository $repository
     */
    public function setRepository(ResourceRepository $repository);
}
