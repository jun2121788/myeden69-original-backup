<?php
include 'frm.class/PopUserForm.php';
// meta tag robots
osc_add_hook('header', 'pop_nofollow_construct');

osc_enqueue_script('jquery-validate');
pop_add_body_class('item item-post');
$action = 'item_add_post';
$edit = false;
if (Params::getParam('action') == 'item_edit') {
    $action = 'item_edit_post';
    $edit = true;
}
?>
<?php osc_current_web_theme_path('header.php'); ?>
<?php PopUserForm::location_javascript(); ?>
<div class="form-container box">
    <div class="header">
        <h1><?php _e('Publish a listing', 'pop'); ?></h1>
    </div>
    <div class="form-content">
        <ul id="error_list"></ul>
        <form name="item" action="<?php echo osc_base_url(true); ?>" method="post" enctype="multipart/form-data" id="item-post">
            <fieldset>
                <input type="hidden" name="action" value="<?php echo $action; ?>" />
                <input type="hidden" name="page" value="item" />
<?php if ($edit) { ?>
                    <input type="hidden" name="id" value="<?php echo osc_item_id(); ?>" />
                    <input type="hidden" name="secret" value="<?php echo osc_item_secret(); ?>" />
<?php } ?>
                <div class="control-group">
                    <label class="control-label" for="select_1"><?php _e('Category', 'pop'); ?></label>
                    <div class="controls">
<?php ItemForm::category_select(null, null, __('Select a category', 'pop')); ?>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="title[<?php echo osc_current_user_locale(); ?>]"><?php _e('Title', 'pop'); ?></label>
                    <div class="controls">
<?php ItemForm::title_input('title', osc_current_user_locale(), osc_esc_html(pop_item_title())); ?>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="description[<?php echo osc_current_user_locale(); ?>]"><?php _e('Description', 'pop'); ?></label>
                    <div class="controls">
<?php ItemForm::description_textarea('description', osc_current_user_locale(), osc_esc_html(pop_item_description())); ?>
                    </div>
                </div>
<?php if (osc_price_enabled_at_items()) { ?>
                    <div class="control-group control-group-price">
                        <label class="control-label" for="price"><?php _e('Price', 'pop'); ?></label>
                        <div class="controls">
    <?php ItemForm::price_input_text(); ?>
                            <?php ItemForm::currency_select(); ?>
                        </div>
                    </div>
<?php } ?>
                <?php
                if (osc_images_enabled_at_items()) {
                    ItemForm::ajax_photos();
                }
                ?>
                <div class=" location">
                    <div class="control-group">
                        <h2><?php _e('Listing Location', 'pop'); ?></h2>
                    </div>

                    <?php if(count(osc_get_countries()) > 1) { ?>
                        <div class="control-group">
                            <label class="control-label" for=""><?php _e('Country', 'pop'); ?></label>
                            <div class="controls">
                                <?php ItemForm::country_select(osc_get_countries(), osc_user()); //array('fk_c_country_code' => osc_user_field('fk_c_country_code'))); ?>
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label" for="regionId"><?php _e('Region', 'pop'); ?></label>
                            <div class="controls">
                                <?php ItemForm::region_select(osc_get_regions(osc_user_field('fk_c_country_code')), osc_user()); ?>
                            </div>
                        </div>
                    <?php
                    } else {
                        $aCountries = osc_get_countries();
                        $aRegions = osc_get_regions($aCountries[0]['pk_c_code']);
                        ?>
                    <input type="hidden" id="countryId" name="countryId" value="<?php echo osc_esc_html($aCountries[0]['pk_c_code']); ?>"/>
                    <div class="control-group">
                        <label class="control-label" for="regionId"><?php _e('Region', 'pop'); ?></label>
                        <div class="controls">
                            <?php ItemForm::region_select($aRegions); ?>
                        </div>
                    </div>
                    <?php } ?>
                    <div class="control-group">
                        <label class="control-label" for="city"><?php _e('City', 'pop'); ?></label>
                        <div class="controls">
                            <?php if($action == 'item_add_post') { ?>
                            <?php ItemForm::city_select(osc_get_cities(osc_user_region_id()), osc_user()); ?>
                            <?php } else { ?>
                            <?php ItemForm::city_select(null, osc_item()); ?>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label" for="cityArea"><?php _e('City Area', 'pop'); ?></label>
                        <div class="controls">
                            <?php ItemForm::city_area_text(osc_user()); ?>
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label" for="address"><?php _e('Address', 'pop'); ?></label>
                        <div class="controls">
                            <?php ItemForm::address_text(osc_user()); ?>
                        </div>
                    </div>
                </div>
                <!-- seller info -->
                <?php if (!osc_is_web_user_logged_in()) { ?>
                    <div class="seller_info">

                        <div class="control-group">
                            <h2><?php _e("Seller's information", 'pop'); ?></h2>
                        </div>
                        <div class="control-group">
                            <label class="control-label" for="contactName"><?php _e('Name', 'pop'); ?></label>
                            <div class="controls">
                                <?php ItemForm::contact_name_text(); ?>
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label" for="contactEmail"><?php _e('E-mail', 'pop'); ?></label>
                            <div class="controls">
                                <?php ItemForm::contact_email_text(); ?>
                            </div>
                        </div>
                        <div class="control-group">
                            <div class="controls checkbox">
                                <?php ItemForm::show_email_checkbox(); ?> <label for="showEmail"><?php _e('Show e-mail on the listing page', 'pop'); ?></label>
                            </div>
                        </div>
                    </div>
                    <?php
                }
                if ($edit) {
                    ItemForm::plugin_edit_item();
                } else {
                    ItemForm::plugin_post_item();
                }
                ?>
                <div class="control-group">
                    <?php if (osc_recaptcha_items_enabled()) { ?>
                        <div class="controls">
                            <?php if (osc_recaptcha_public_key()) { ?>
                                <script type="text/javascript">
                                    var RecaptchaOptions = {
                                        theme: 'custom',
                                        custom_theme_widget: 'recaptcha_widget'
                                    };
                                </script>
                                <style type="text/css"> div#recaptcha_widget, div#recaptcha_image > img { width:280px; } </style>
                                <div id="recaptcha_widget">
                                    <div id="recaptcha_image"><img /></div>
                                    <span class="recaptcha_only_if_image"><?php _e('Enter the words above', 'pop'); ?>:</span>
                                    <input type="text" id="recaptcha_response_field" name="recaptcha_response_field" />
                                </div>
                            <?php } ?>
                            <?php osc_show_recaptcha(); ?>
                        </div>
                    <?php } ?>
                    <div class="controls">
                        <button type="submit" class="btn-round"><?php if ($edit) {
                        _e("Update", 'pop');
                    } else {
                        _e("Publish", 'pop');
                    } ?></button>
                    </div>
                </div>
            </fieldset>
        </form>
    </div>
</div>
<script type="text/javascript">
    $('#price').bind('hide-price', function () {
        $('.control-group-price').hide();
    });

    $('#price').bind('show-price', function () {
        $('.control-group-price').show();
    });

<?php if (osc_locale_thousands_sep() != '' || osc_locale_dec_point() != '') { ?>
        $().ready(function () {
            $("#price").blur(function (event) {
                var price = $("#price").prop("value");
    <?php if (osc_locale_thousands_sep() != '') { ?>
                    while (price.indexOf('<?php echo osc_esc_js(osc_locale_thousands_sep()); ?>') != -1) {
                        price = price.replace('<?php echo osc_esc_js(osc_locale_thousands_sep()); ?>', '');
                    }
    <?php }; ?>
    <?php if (osc_locale_dec_point() != '') { ?>
                    var tmp = price.split('<?php echo osc_esc_js(osc_locale_dec_point()) ?>');
                    if (tmp.length > 2) {
                        price = tmp[0] + '<?php echo osc_esc_js(osc_locale_dec_point()) ?>' + tmp[1];
                    }
    <?php }; ?>
                $("#price").prop("value", price);
            });
        });
<?php }; ?>
</script>
<?php osc_current_web_theme_path('footer.php'); ?>