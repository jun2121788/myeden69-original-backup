<?php

$category = osc_get_category('id',osc_item_category_id());
$category_url = osc_search_url(array('sCategory' => $category['s_slug']))
?>
<div class="item <?php echo $class;
if (osc_item_is_premium()) {
    echo ' premium';
} ?>">
    <?php if (osc_images_enabled_at_items()) { ?>
        <?php if (osc_count_item_resources()) { ?>
            <a class="" href="<?php echo osc_item_url(); ?>" title="<?php echo osc_esc_html(osc_item_title()); ?>"><img src="<?php echo osc_resource_original_url(); ?>" title="" alt="<?php echo osc_esc_html(osc_item_title()); ?>" width="220" ></a>
        <?php } else { ?>
            <a class="" href="<?php echo osc_item_url(); ?>" title="<?php echo osc_esc_html(osc_item_title()); ?>"><img src="<?php echo osc_current_web_theme_url('images/no_photo.gif'); ?>" title="" alt="<?php echo osc_esc_html(osc_item_title()); ?>" width="220"></a>
        <?php } ?>
<?php } ?>

    <div class="listing-attributes">
        <span class="listing-price"><?php echo osc_format_price(osc_item_price()); ?></span>
        <span class="listing-title"><a href="<?php echo osc_item_url();?>"><?php echo osc_item_title(); ?></a></span>
        <span class="listing-category"><a href="<?php echo $category_url; ?>"><?php echo osc_item_category(); ?></a></span>
    </div>

    <?php if(Params::getParam('page')=='user') { ?>
    <?php if ($admin) { ?>
            <div class="center">
                <span class="admin-options">
                    <a href="<?php echo osc_item_edit_url(); ?>" rel="nofollow"><?php _e('Edit item', 'pop'); ?></a>
                    <span>|</span>
                    <a class="delete" onclick="javascript:return confirm('<?php echo osc_esc_js(__('This action can not be undone. Are you sure you want to continue?', 'pop')); ?>')" href="<?php echo osc_item_delete_url(); ?>" ><?php _e('Delete', 'pop'); ?></a>
                    <?php if (osc_item_is_inactive()) { ?>
                        <span>|</span>
                        <a href="<?php echo osc_item_activate_url(); ?>" ><?php _e('Activate', 'pop'); ?></a>
                    <?php } ?>
                </span>
            </div>
    <?php } ?>
    <?php } else if(osc_item_user_id()!='') {
        View::newInstance()->_exportVariableToView('user', User::newInstance()->findByPrimaryKey(osc_item_user_id()));
        ?>
    <a href="<?php echo osc_user_public_profile_url(); ?>">
        <div class="listing-seller">
            <div class="listing-seller-avatar" style="background-image: url('<?php echo osc_current_web_theme_url('images/seller-default.png');?>')"></div>
            <span class="seller-name"><?php echo osc_user_name(); ?></span>
            <span class="seller-products"><?php echo osc_user_items_validated(), ' ' ._n('Listing', 'Listings', osc_user_items_validated(), 'pop'); ?></span>
        </div>
    </a>
    <?php } ?>

</div>
