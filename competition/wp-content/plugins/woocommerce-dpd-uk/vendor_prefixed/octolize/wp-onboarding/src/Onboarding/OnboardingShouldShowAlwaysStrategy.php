<?php

/**
 * @package Octolize\Onboarding
 */
namespace DpdUKVendor\Octolize\Onboarding;

/**
 * Always display strategy.
 */
class OnboardingShouldShowAlwaysStrategy implements \DpdUKVendor\Octolize\Onboarding\OnboardingShouldShowStrategy
{
    public function should_display() : bool
    {
        return \true;
    }
}
