<?php
$map_image = str_replace(osc_uploads_url(), osc_uploads_path(), pop_get_listing_map_image(osc_item_id()) );

if (!file_exists($map_image)) {
    pop_listing_location_img(osc_item());
}


// meta tag robots
osc_add_hook('header', 'pop_follow_construct');
pop_add_body_class('item');
osc_enqueue_script('jquery-validate');
osc_enqueue_script('jquery-bxslider');
osc_enqueue_style('jquery-bxslider-css', osc_current_web_theme_url('css/jquery.bxslider.css'));
View::newInstance()->_exportVariableToView('user', User::newInstance()->findByPrimaryKey(osc_item_user_id()));
?>

<?php osc_current_web_theme_path('header.php'); ?>
<?php $class = '';
if (osc_count_item_resources() == 0) {
    $class = "no-image";
} ?>
<div class="box photos <?php echo $class; ?>">
    <div class="photos-slider">
        <?php if (osc_count_item_resources() > 0) { ?>
            <ul class="bxslider">
                <?php osc_reset_resources();
                for ($i = 0; osc_has_item_resources(); $i++) { ?>
                    <li><img src="<?php echo osc_resource_original_url(); ?>" title="" alt="<?php echo osc_esc_html(osc_item_title()); ?>" width="640"/></li>
            <?php } ?>
            </ul>
        <?php } else { ?>
            <img src="<?php echo osc_current_web_theme_url('images/no_photo.gif'); ?>" title="" alt="<?php echo osc_esc_html(osc_item_title()); ?>" width="640"/>
        <?php } ?>
    </div>
    <?php osc_reset_resources(); ?>
    <?php if (osc_count_item_resources() > 0) { ?>
    <div class="photos-thumbnail">
        <div id="bx-pager">
            <?php for ($i = 0; osc_has_item_resources(); $i++) { ?>
                <a data-slide-index="<?php echo $i; ?>" href=""><img src="<?php echo osc_resource_thumbnail_url(); ?>" /></a>
            <?php } ?>
        </div>
    </div>
    <?php } ?>
</div>

<div class="box item-detail">
    <div class="listing-detail-wrapper">
        <span class="listing-price"><?php echo osc_format_price(osc_item_price()); ?></span>
        <span class="listing-title"><?php echo osc_item_title(); ?></span>
        <span class="listing-description"><?php echo osc_item_description(); ?></span>

        <?php if( osc_search_category_url() != '') { ?>
        <p><span class="listing-category"><a class="btn-round" href="<?php echo osc_search_category_url(); ?>"><?php echo osc_item_category(); ?></a></span></p>
        <?php } ?>

        <div id="custom_fields">
        <?php if( osc_count_item_meta() >= 1 ) { ?>
            <br />
            <div class="meta_list">
                <?php while ( osc_has_item_meta() ) { ?>
                    <?php if(osc_item_meta_value()!='') { ?>
                        <div class="meta">
                            <strong><?php echo osc_item_meta_name(); ?>:</strong> <?php echo osc_item_meta_value(); ?>
                        </div>
                    <?php } ?>
                <?php } ?>
            </div>
        <?php } ?>
        </div>

        <?php  // Plugins like printPdf,
        /*<p></p>*/
        // --/Plugin  ?>

        <div class="hook-item-detail">
            <?php osc_run_hook('item_detail', osc_item()); ?>
        </div>

        <span><i class="ico-location_pin"></i><?php echo osc_item_city(); ?></span>
    </div>
    <div class="item-location">
        <img src="<?php echo pop_get_listing_map_image(osc_item_id()); ?>"/>
    </div>
    <div class="share-links">
        <span><?php _e('Share it!', 'pop'); ?></span>
        <p>
            <a href="<?php echo osc_esc_html(pop_facebook_share_url()); ?>" class="share-icon-bg facebook-icon"></a>
            <a href="<?php echo osc_esc_html(pop_twitter_share_url()); ?>" class="share-icon-bg twitter-icon"></a>
            <a href="<?php echo osc_esc_html(pop_email_share_url()); ?>" class="share-icon-bg email-icon"></a>
        <p>
    </div>
</div>
<div class="box user-detail">
<?php pop_draw_ad('search-results-300x250'); ?>
</div>

<div class="box user-detail">
    <?php if(osc_item_user_id()!=0) { ?>
    <div class="listing-seller">
        <a href="<?php echo osc_user_public_profile_url(); ?>">
            <div class="listing-seller-avatar" style="background-image: url('<?php echo osc_current_web_theme_url('images/seller-default.png'); ?>')"></div>
            <span class="seller-name"><?php echo osc_item_contact_name(); ?></span>
            <?php if (osc_item_user_id() != '') { ?>
            <span class="seller-products"><?php echo osc_user_items_validated(), ' ' . _n('Listing', 'Listings', osc_user_items_validated(), 'pop') ?></span>
            <?php } ?>
        </a>
    </div>
    <?php } ?>
<?php require 'inc.seller.contact.php'; ?>
</div>

<?php
if (osc_item_user_id() != '') {
    $aItems = Item::newInstance()->findByUserID(osc_item_user_id(), 0, 5);
    View::newInstance()->_exportVariableToView('items', $aItems);
    ?>
    <div class="box user-products">
        <p><?php printf(__('Other products from %s', 'pop'), osc_user_name()); ?></p>
        <?php
        while (osc_has_items()) {
            if (osc_count_item_resources()) {
                osc_get_item_resources();
                ?>
                <a href="<?php echo osc_item_url(); ?>" title="<?php echo osc_esc_html(osc_item_title()); ?>">
                    <img src="<?php echo osc_resource_thumbnail_url(); ?>" alt="<?php echo osc_item_title(); ?>" title="<?php echo osc_item_title(); ?>" />
                </a>
        <?php } else { ?>
                <a href="<?php echo osc_item_url(); ?>" title="<?php echo osc_esc_html(osc_item_title()); ?>">
                    <img src="<?php echo osc_current_web_theme_url('images/no_photo.gif'); ?>" alt="<?php echo osc_item_title(); ?>" title="<?php echo osc_item_title(); ?>" />
                </a>
        <?php } ?>
    <?php } ?>
    </div>
<?php } ?>

<script>
    $('.bxslider').bxSlider({
        pagerCustom: '#bx-pager',
        adaptiveHeight: true,
        mode: 'fade'
    });
</script>
<?php
osc_current_web_theme_path('footer.php');
