<?php

namespace DpdUKVendor\Octolize\Tracker\OptInNotice;

/**
 * Should display never.
 */
class ShouldDisplayNever implements \DpdUKVendor\Octolize\Tracker\OptInNotice\ShouldDisplay
{
    /**
     * @inheritDoc
     */
    public function should_display()
    {
        return \false;
    }
}
