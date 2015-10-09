<?php

namespace Mi\PuliBundlePlugins\Tests\Fixtures;

use Mi\PuliBundlePlugins\PuliBundleWithPlugins;

/**
 * @author Alexander Miehe <alexander.miehe@movingimage.com>
 */
class TestPuliBundleWithPlugins extends PuliBundleWithPlugins
{
    /**
     * {@inheritdoc}
     */
    protected function getAlias()
    {
        return 'test';
    }
}
