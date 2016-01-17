<div id="contact" class="">
    <h2><?php _e("Contact publisher", 'pop'); ?></h2>
    <?php if (osc_item_is_expired()) { ?>
        <p>
            <?php _e("The listing is expired. You can't contact the publisher.", 'pop'); ?>
        </p>
    <?php } else if (( osc_logged_user_id() == osc_item_user_id() ) && osc_logged_user_id() != 0) { ?>
        <p>
            <?php _e("It's your own listing, you can't contact the publisher.", 'pop'); ?>
        </p>
    <?php } else if (osc_reg_user_can_contact() && !osc_is_web_user_logged_in()) { ?>
        <p>
            <?php _e("You must log in or register a new account in order to contact the advertiser", 'pop'); ?>
        </p>
        <p class="contact_button">
            <strong><a href="<?php echo osc_user_login_url(); ?>"><?php _e('Login', 'pop'); ?></a></strong>
            <strong><a href="<?php echo osc_register_account_url(); ?>"><?php _e('Register for a free account', 'pop'); ?></a></strong>
        </p>
    <?php } else { ?>
        <?php if (osc_item_user_id() != null) { ?>
            <p class="name"><?php _e('Name', 'pop') ?>: <a href="<?php echo osc_user_public_profile_url(osc_item_user_id()); ?>" ><?php echo osc_item_contact_name(); ?></a></p>
        <?php } else { ?>
            <p class="name"><?php printf(__('Name: %s', 'pop'), osc_item_contact_name()); ?></p>
        <?php } ?>
        <?php if (osc_item_show_email()) { ?>
            <p class="email"><?php printf(__('E-mail: %s', 'pop'), osc_item_contact_email()); ?></p>
        <?php } ?>
        <?php if (osc_user_phone() != '') { ?>
            <p class="phone"><?php printf(__("Phone: %s", 'pop'), osc_user_phone()); ?></p>
        <?php } ?>
        <ul id="error_list"></ul>
        <form action="<?php echo osc_base_url(true); ?>" method="post" name="contact_form" id="contact_form" <?php if (osc_item_attachment()) {
        echo 'enctype="multipart/form-data"';
    }; ?> >
    <?php osc_prepare_user_info(); ?>
            <input type="hidden" name="action" value="contact_post" />
            <input type="hidden" name="page" value="item" />
            <input type="hidden" name="id" value="<?php echo osc_item_id(); ?>" />
            <div class="control-group">
                <label class="control-label" for="yourName"><?php _e('Your name', 'pop'); ?>:</label>
                <div class="controls"><?php ContactForm::your_name(); ?></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="yourEmail"><?php _e('Your e-mail address', 'pop'); ?>:</label>
                <div class="controls"><?php ContactForm::your_email(); ?></div>
            </div>
            <div class="control-group">
                <label class="control-label" for="phoneNumber"><?php _e('Phone number', 'pop'); ?> (<?php _e('optional', 'pop'); ?>):</label>
                <div class="controls"><?php ContactForm::your_phone_number(); ?></div>
            </div>

            <div class="control-group">
                <label class="control-label" for="message"><?php _e('Message', 'pop'); ?>:</label>
                <div class="controls textarea"><?php ContactForm::your_message(); ?></div>
            </div>

    <?php if (osc_item_attachment()) { ?>
                <div class="control-group">
                    <label class="control-label" for="attachment"><?php _e('Attachment', 'pop'); ?>:</label>
                    <div class="controls"><?php ContactForm::your_attachment(); ?></div>
                </div>
    <?php }; ?>

            <div class="control-group">
                <div class="controls">
    <?php osc_run_hook('item_contact_form', osc_item_id()); ?>
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
                    <button type="submit" class="btn-round btn-black"><?php _e("Send", 'pop'); ?></button>
                </div>
            </div>
        </form>
    <?php ContactForm::js_validation(); ?>
<?php } ?>
</div>