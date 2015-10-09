<?php

namespace Mi\PuliBundlePlugins;

use Puli\Discovery\Api\Discovery;

/**
 * @author Alexander Miehe <alexander.miehe@movingimage.com>
 */
interface DiscoveryAwareInterface
{
    /**
     * @param Discovery $discovery
     */
    public function setDiscovery(Discovery $discovery);
}
