<?php

namespace FPrintingVendor\Octolize\Tracker\OptInNotice;

/**
 * Should display always.
 */
class ShouldDisplayAlways implements \FPrintingVendor\Octolize\Tracker\OptInNotice\ShouldDisplay
{
    /**
     * @inheritDoc
     */
    public function should_display()
    {
        return \true;
    }
}
