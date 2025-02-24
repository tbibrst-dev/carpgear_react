<?php

namespace DpdUKVendor\Octolize\Tracker\OptInNotice;

/**
 * Should display always.
 */
class ShouldDisplayAlways implements \DpdUKVendor\Octolize\Tracker\OptInNotice\ShouldDisplay
{
    /**
     * @inheritDoc
     */
    public function should_display()
    {
        return \true;
    }
}
