<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

use Wdr\App\Helpers\Template;
$template_helper = new Template();
?>
<br>
<div id="wpbody-content" class="awdr-container" style="background-color: #ffffff;">
    <?php if($is_pro) { ?>
        <div class="awdr_recipe_container">
            <div class="awdr_recipe_left">
                <div class="awdr_recipe_main_card" data-recipe-group="awdr_product_adjustment_card_group">
                    <div class="awdr_product_adjustment_card_container" >
                        <h4><b> <?php _e('Simple Discount', WDR_TEXT_DOMAIN);?></b></h4>
                        <p><?php _e('Product adjustment ', WDR_TEXT_DOMAIN);?></p>
                    </div>
                </div>
                <div class="awdr_recipe_main_card" data-recipe-group="awdr_set_adjustment_card_group">
                    <div class="awdr_set_adjustment_card_container">
                        <h4><b><?php _e('Bundle(set) Discount', WDR_TEXT_DOMAIN);?></b></h4>
                        <p><?php _e('Bundle(set) Adjustment', WDR_TEXT_DOMAIN);?></p>
                    </div>
                </div>
                <div class="awdr_recipe_main_card" data-recipe-group="awdr_bogo_adjustment_card_group">
                    <div class="awdr_bogo_adjustment_card_container">
                        <h4><b><?php _e('BOGO Discount', WDR_TEXT_DOMAIN);?></b></h4>
                        <p><?php _e('BOGO Adjustment', WDR_TEXT_DOMAIN);?></p>
                    </div>
                </div>
            </div>
            <div class="awdr_recipe_right">
                <div class="awdr_recipe_main_content">
                    <?php
                        $recipe_nonce = \Wdr\App\Helpers\Helper::create_nonce('common_recipe_nonce');
                        $params['recipe_nonce'] = $recipe_nonce;
                        $template_helper->setPath(WDR_PLUGIN_PATH . 'App/Views/Admin/Tabs/RecipeTypes/SimpleDiscountRecipe.php' )->setData($params)->display();
                        $template_helper->setPath(WDR_PLUGIN_PATH . 'App/Views/Admin/Tabs/RecipeTypes/CartDiscountRecipe.php' )->setData($params)->display();
                        $template_helper->setPath(WDR_PLUGIN_PATH . 'App/Views/Admin/Tabs/RecipeTypes/BulkDiscountRecipe.php' )->setData($params)->display();
                        $template_helper->setPath(WDR_PLUGIN_PATH . 'App/Views/Admin/Tabs/RecipeTypes/BundleDiscountRecipe.php' )->setData($params)->display();
                        $template_helper->setPath(WDR_PLUGIN_PATH . 'App/Views/Admin/Tabs/RecipeTypes/BogoDiscountRecipe.php' )->setData($params)->display();
                    ?>
                </div>
            </div>
        </div>
        <?php
    }else{ ?>
       <p> <?php  _e("Unlock this feature by <a href='https://www.flycart.org/products/wordpress/woocommerce-discount-rules?utm_source=woo-discount-rules-v2&utm_campaign=doc&utm_medium=text-click&utm_content=unlock_pro' target='_blank'>Upgrading to Pro</a>", WDR_TEXT_DOMAIN); ?></p>
    <?php } ?>
</div>

