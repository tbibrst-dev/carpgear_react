<?php

namespace ADP\BaseVersion\Includes\Compatibility;

defined('ABSPATH') or exit;

/**
 * Plugin Name: Yoast SEO
 * Author: Team Yoast 
 *
 * @see https://yoast.com/#utm_term=team-yoast&utm_content=plugin-info
 */
class YoastSEOCmp
{

    public function applyCompatibility()
    {
        add_action("adp_schema_data_ready", function($data, $processedProduct, $decimals){

            add_filter( 'wpseo_schema_product', function($wpseo_data) use ($data) {
                if ( isset( $wpseo_data['offers'][0]['priceSpecification']['price']) && isset($data['price']) ) {

                    $wpseo_data['offers'][0]['priceSpecification']['price'] = $data['price'];
                }
    
                return $wpseo_data;
            });

            add_filter('wpseo_schema_offer', function($offer) use ($processedProduct, $decimals) {

                $childPrices = YoastSEOCmp::getChildPrices($processedProduct, $decimals);
                if(isset($childPrices)) {
                    foreach($childPrices as $child) {
                        if(isset($child['priceOriginal']) && $child['price'] && $child['priceOriginal'] === $offer['priceSpecification']['price']) {
                            $offer['priceSpecification']['price'] = $child['price'];
                        }
                    }
                }

                return $offer;
            });
        }, 10, 3);
    }

    /**
     * @param $processedProduct
     * @param $decimals
     * 
     * @return array
     */
    private static function getChildPrices($processedProduct, $decimals) {
        $childPrices = array();
        foreach ($processedProduct->getChildren() as $child) {
            $price = $child->getPrice();
            $priceOriginal = $child->getOriginalPrice();
            $childPrices[] = ['price' => wc_format_decimal($price, $decimals), 'priceOriginal' => wc_format_decimal($priceOriginal, $decimals)];
        }
        return $childPrices;
    }

    public function isActive()
    {
        return defined('WPSEO_WOO_VERSION') && defined('WPSEO_BASENAME');
    }
}
