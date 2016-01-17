<?php 
  $pluginInfo = osc_plugin_get_info('all_in_one/index.php');
  include 'functions.php';

  if(Params::getParam('plugin_action')=='done') {
    message_ok(__('Meta settings for categories saved','all_in_one'));
  }
?>

<div id="settings_form">
  <?php echo config_menu(); ?>

  <form name="promo_form" id="promo_form" action="<?php echo osc_admin_base_url(true); ?>" method="POST" enctype="multipart/form-data" >
    <input type="hidden" name="page" value="plugins" />
    <input type="hidden" name="action" value="renderplugin" />
    <input type="hidden" name="file" value="<?php echo osc_plugin_folder(__FILE__); ?>categories.php" />
    <input type="hidden" name="plugin_action" value="done" />

    <fieldset class="round3">
      <legend class="azure round2"><?php _e('Category Meta Settings','all_in_one'); ?></legend>
      <span class="cat-note"><a href="<?php echo osc_base_url() . 'oc-admin/index.php?page=categories';?>"><i class="fa fa-pencil"></i><?php _e('Edit Categories','all_in_one'); ?></a></span>
      <span class="cat-note"><span class="note"><?php _e('Note', 'all_in_one'); ?>:</span> <?php _e('To edit meta details for categories, click on "unlock" link on right side','all_in_one'); ?></span>
      <span class="cat-note"><span class="note"><?php _e('Note', 'all_in_one'); ?>:</span> <?php _e('Do not edit more than 50 categories at once','all_in_one'); ?></span>

      <div class="clear" style="margin:12px 0;"></div>

      <div class="cat-list">
        <div class="cat-head round3">
          <div class="cat-elem id"><?php _e('ID', 'all_in_one'); ?></div>
          <div class="cat-elem name"><?php _e('Category Name', 'all_in_one'); ?></div>
          <div class="cat-elem titl"><?php _e('Meta Title', 'all_in_one'); ?></div>
          <div class="cat-elem desc"><?php _e('Meta Description', 'all_in_one'); ?></div>
          <div class="cat-elem keywords"><?php _e('Meta Keywords', 'all_in_one'); ?></div>
          <div class="cat-elem lock"></div>
        </div>

        <?php
          osc_goto_first_category();
          seo_categories_list();
        ?>
      </div>
    </fieldset>

    <br /><br />
    <button name="theButton" id="theButton" type="submit" style="float: left;" class="btn btn-submit"><?php _e('Update Categories', 'all_in_one');?></button>
  </form>

  <div class="clear"></div>
  <br /><br />

  <div class="warn"><?php _e('If you do not enter Meta details for category, default osclass settings are used.', 'all_in_one'); ?></div>
  <div class="warn"><?php _e('Do not enter more than 5 keywords per category, it would be useless', 'all_in_one'); ?></div>
  <div class="warn"><?php _e('Meta description should be <strong>150 - 160 characters</strong> long, otherwise not whole description will be shown.', 'all_in_one'); ?></div>
  <div class="warn"><?php _e('Meta title should be <strong>50-60 characters</strong> long, otherwise not whole title will be shown.', 'all_in_one'); ?></div>
  <div class="warn"><?php _e('Meta description & title should contain some keyword for your category, so if you have category Cars, it would be good to have there word Car, Auto etc. This will secure that when user search this word, your site will be shown with your Meta Title & Description you entered here. Otherwise, Google - or other search engines - will generate own.', 'all_in_one'); ?></div>
  <div class="warn"><?php _e('When you are on listing page and you show category in title, category name <span class="underline">will not</span> be replaced by category meta title placed here', 'all_in_one'); ?></div>
  <div class="clear"></div>
  <br /><br />

  <script>
    $(document).ready(function() {
      $('.cat-row.level1').first().addClass('first-child');
    });
  </script>

  <?php echo $pluginInfo['plugin_name'] . ' | ' . __('Version','all_in_one') . ' ' . $pluginInfo['version'] . ' | ' . __('Author','all_in_one') . ': ' . $pluginInfo['author'] . ' | Cannot be redistributed | &copy; ' . date('Y') . ' <a href="' . $pluginInfo['plugin_uri'] . '" target="_blank">MB Themes</a>'; ?>
</div>