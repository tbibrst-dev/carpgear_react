<?php

namespace ADP\BaseVersion\Includes\Shortcodes;

use ADP\BaseVersion\Includes\Core\Rule\SingleItemRule;

defined('ABSPATH') or exit;

class OnSaleProducts extends Products
{
    const NAME = 'adp_products_on_sale';
    const STORAGE_WITH_RULES_KEY = 'wdp_rules_products_onsale';

    /**
     * @param Rule $rule
     *
     * @return bool
     */
    protected static function filterRule($rule)
    {
        return
            $rule instanceof SingleItemRule &&
            $rule->getProductAdjustmentHandler() &&
            ! $rule->getProductRangeAdjustmentHandler() &&
            ! $rule->getRoleDiscounts() &&
            count($rule->getGifts()) === 0 &&
            count($rule->getItemGiftsCollection()->asArray()) === 0 &&
            adp_functions()->isRuleMatchedCart($rule) &&
            count($rule->getLimits()) === 0;
    }

}
