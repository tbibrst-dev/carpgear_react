<?php

/**
 * @package Octolize\Onboarding
 */
namespace DpdUKVendor\Octolize\Onboarding;

/**
 * Never display strategy.
 */
class OnboardingShouldShowNeverStrategy implements \DpdUKVendor\Octolize\Onboarding\OnboardingShouldShowStrategy
{
    public function should_display() : bool
    {
        return \false;
    }
}
