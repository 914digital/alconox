<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
$operator = isset($options->operator) ? $options->operator : 'greater_than_or_equal';
$calculate_from = isset($options->calculate_from) ? $options->calculate_from : 'from_cart';
echo ($render_saved_condition == true) ? '' : '<div class="cart_line_items_count">';
?>
<div class="wdr_cart_line_item_count_group wdr-condition-type-options">
    <div class="wdr-cart-subtotal wdr-select-filed-hight">
        <select name="conditions[<?php echo (isset($i)) ? $i : '{i}' ?>][options][operator]" class="awdr-left-align">
            <option value="less_than" <?php echo ($operator == "less_than") ? "selected" : ""; ?>><?php _e('Less than ( &lt; )', WDR_TEXT_DOMAIN) ?></option>
            <option value="less_than_or_equal" <?php echo ($operator == "less_than_or_equal") ? "selected" : ""; ?>><?php _e('Less than or equal ( &lt;= )', WDR_TEXT_DOMAIN) ?></option>
            <option value="greater_than_or_equal" <?php echo ($operator == "greater_than_or_equal") ? "selected" : ""; ?>><?php _e('Greater than or equal ( &gt;= )', WDR_TEXT_DOMAIN) ?></option>
            <option value="greater_than" <?php echo ($operator == "greater_than") ? "selected" : ""; ?>><?php _e('Greater than ( &gt; )', WDR_TEXT_DOMAIN) ?></option>
        </select>
        <span class="wdr_desc_text awdr-clear-both"><?php _e('Line item count should be', WDR_TEXT_DOMAIN); ?></span>
    </div>

    <div class="cart-subtotal-value wdr-input-filed-hight">
        <input name="conditions[<?php echo (isset($i)) ? $i : '{i}' ?>][options][value]" type="text" class="float_only_field awdr-left-align"
               value="<?php echo (isset($options->value)) ? $options->value : '' ?>" placeholder="<?php _e('1', WDR_TEXT_DOMAIN);?>">
        <span class="wdr_desc_text awdr-clear-both"><?php _e('Line item count', WDR_TEXT_DOMAIN); ?></span>
    </div>
    <div class="cart-subtotal-methods wdr-select-filed-hight">
        <select name="conditions[<?php echo (isset($i)) ? $i : '{i}' ?>][options][calculate_from]" class="calculate_cart_from awdr-left-align">
            <option value="from_cart" <?php echo ($calculate_from == "from_cart") ? "selected" : ""; ?>><?php _e('Count all items in cart', WDR_TEXT_DOMAIN) ?></option>
            <option value="from_filter" <?php echo ($calculate_from == "from_filter") ? "selected" : ""; ?>><?php _e('Only count items chosen in the filters set for this rule', WDR_TEXT_DOMAIN) ?></option>
        </select>
        <span class="wdr_desc_text awdr-clear-both "><?php _e('How to calculate the line item count', WDR_TEXT_DOMAIN); ?></span>
    </div>
</div>
<?php echo ($render_saved_condition == true) ? '' : '</div>'; ?>
