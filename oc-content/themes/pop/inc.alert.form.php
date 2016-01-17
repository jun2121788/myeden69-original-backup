<script type="text/javascript">
$(document).ready(function(){
    $(".sub_button").click(function(){
        $.post('<?php echo osc_base_url(true); ?>', {email:$("#alert_email").val(), userid:$("#alert_userId").val(), alert:$("#alert").val(), page:"ajax", action:"alerts"},
            function(data){
                if(data==1) { alert('<?php echo osc_esc_js(__('You have sucessfully subscribed to the alert', 'pop')); ?>'); }
                else if(data==-1) { alert('<?php echo osc_esc_js(__('Invalid email address', 'pop')); ?>'); }
                else { alert('<?php echo osc_esc_js(__('There was a problem with the alert', 'pop')); ?>');
                };
        });
        return false;
    });
});
</script>

<div class="alert_form">
    <h3>
        <strong><?php _e('Subscribe to this search', 'pop'); ?></strong>
    </h3>
    <div class="alert_form_container">
        <form action="<?php echo osc_base_url(true); ?>" method="post" name="sub_alert" id="sub_alert" class="nocsrf">
                <?php AlertForm::page_hidden(); ?>
                <?php AlertForm::alert_hidden(); ?>

                <?php if(osc_is_web_user_logged_in()) { ?>
                    <?php AlertForm::user_id_hidden(); ?>
                    <?php AlertForm::email_hidden(); ?>
                <?php } else { ?>
                    <?php AlertForm::user_id_hidden(); ?>
                    <input id="alert_email" type="text" name="alert_email" value="" placeholder="<?php echo __('Enter your e-mail', 'pop'); ?>">
                <?php } ?>
                <a href="#" class="sub_button btn-round" ><?php _e('Subscribe now', 'pop'); ?>!</a>
        </form>
    </div>
</div>