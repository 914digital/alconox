<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
?>
<div class="awdr_set_adjustment_card_group" style="display: none;">
    <div class="awdr_simple_discount awdr_common_border_class">
        <b><?php _e('Set Discount - sample', WDR_TEXT_DOMAIN);?></b>
        <button class="button awdr_recipe_content" type="button" data-recipe-nonce="<?php echo $recipe_nonce;?>" data-select-recipe="bundle_recipe_1"><?php _e('Create', WDR_TEXT_DOMAIN);?></button>
        <a style="display: none;" class="button awdr_recipe_rule_edit" href="" target="_blank" ><?php _e('View & edit Rule', WDR_TEXT_DOMAIN);?></a>

    </div>
</div>