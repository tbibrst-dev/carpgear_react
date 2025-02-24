<?php

namespace FPrintingVendor\Octolize\Tracker\OptInNotice;

/**
 * Should display never.
 */
class ShouldDisplayNever implements \FPrintingVendor\Octolize\Tracker\OptInNotice\ShouldDisplay
{
    /**
     * @inheritDoc
     */
    public function should_display()
    {
        return \false;
    }
}
