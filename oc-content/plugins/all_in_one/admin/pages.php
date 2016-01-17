<?php 
$pluginInfo = osc_plugin_get_info('all_in_one/index.php');

// Get all static pages
function osc_has_static_pages_seo() {
  if ( !View::newInstance()->_exists('pages') ) {
    View::newInstance()->_exportVariableToView('pages', Page::newInstance()->listAll(false, 0) );
  }

  $page = View::newInstance()->_next('pages');
  View::newInstance()->_exportVariableToView('page_meta', json_decode($page['s_meta'], true));
  return $page;
}

if(Params::getParam('plugin_action')=='done') {
  message_ok(__('Meta settings saved','all_in_one'));
  osc_reset_static_pages();

  while(osc_has_static_pages_seo()) {
    $detail = ModelSeoPage::newInstance()->getAttrByPageId( osc_static_page_id() );
    if(isset($detail['seo_page_id'])) {
      ModelSeoPage::newInstance()->updateAttr( osc_static_page_id(), Params::getParam('seo_title' . osc_static_page_id()), Params::getParam('seo_desc' . osc_static_page_id()), Params::getParam('seo_keywords' . osc_static_page_id()) );
    } else {
      ModelSeoPage::newInstance()->insertAttr( osc_static_page_id(), Params::getParam('seo_title' . osc_static_page_id()), Params::getParam('seo_desc' . osc_static_page_id()), Params::getParam('seo_keywords' . osc_static_page_id()) );
    } 
  }
} 
?>

<div id="settings_form">
  <?php echo config_menu(); ?>

  <form name="promo_form" id="promo_form" action="<?php echo osc_admin_base_url(true); ?>" method="POST" enctype="multipart/form-data" >
    <input type="hidden" name="page" value="plugins" />
    <input type="hidden" name="action" value="renderplugin" />
    <input type="hidden" name="file" value="<?php echo osc_plugin_folder(__FILE__); ?>pages.php" />
    <input type="hidden" name="plugin_action" value="done" />

    <fieldset class="round3">
      <legend class="yellow round2">Static Pages Meta Settings</legend>
      <span class="cat-note"><a href="<?php echo osc_base_url() . 'oc-admin/index.php?page=pages';?>"><i class="fa fa-pencil"></i><?php _e('Add new or Edit existing Page','all_in_one'); ?></a></span>
      <span class="cat-note"><span class="note"><?php _e('Note', 'all_in_one'); ?>:</span> <?php _e('Edit button will take you to pages management site where you can edit this static page.','all_in_one'); ?></span>
      <span class="cat-note"><span class="note"><?php _e('Note', 'all_in_one'); ?>:</span> <?php _e('View button will open static page in new window and you will see page in front-office.','all_in_one'); ?></span>

      <div class="clear" style="margin:12px 0;"></div>

      <div class="cat-list pages">
        <div class="cat-head round3">
          <div class="cat-elem id"><?php _e('ID', 'all_in_one'); ?></div>
          <div class="cat-elem name"><?php _e('Page Name', 'all_in_one'); ?></div>
          <div class="cat-elem titl"><?php _e('Meta Title', 'all_in_one'); ?></div>
          <div class="cat-elem desc"><?php _e('Meta Description', 'all_in_one'); ?></div>
          <div class="cat-elem keywords"><?php _e('Meta Keywords', 'all_in_one'); ?></div>
          <div class="cat-elem edit"></div>
          <div class="cat-elem view"></div>
        </div>

        <?php osc_reset_static_pages(); ?>
        <?php while(osc_has_static_pages_seo()) { ?>
          <?php $detail = ModelSeoPage::newInstance()->getAttrByPageId( osc_static_page_id() ); ?>

          <div class="cat-row">
            <div class="cat-elem id"><?php echo osc_static_page_id(); ?></div>
            <div class="cat-elem name"><?php echo osc_static_page_title(); ?></div>
            <div class="cat-elem titl"><input type="text" name="seo_title<?php echo osc_static_page_id(); ?>" id="seo_title" value="<?php if($detail['seo_title'] != ''){echo $detail['seo_title']; } ?>" size="20" /></div>
            <div class="cat-elem desc"><input type="text" name="seo_desc<?php echo osc_static_page_id(); ?>" id="seo_desc" value="<?php if($detail['seo_desc'] != ''){echo $detail['seo_desc']; } ?>" size="20" /></div>
            <div class="cat-elem keywords"><input type="text" name="seo_keywords<?php echo osc_static_page_id(); ?>" id="seo_keywords" value="<?php if($detail['seo_keywords'] != ''){echo $detail['seo_keywords']; } ?>" size="20" /></div>
            <div class="cat-elem edit"><a href="<?php echo osc_base_url() . 'oc-admin/index.php?page=pages&action=edit&id=' . osc_static_page_id();?>" target="_blank"><i class="fa fa-wrench"></i><?php _e('Edit', 'all_in_one'); ?></a></div>
            <div class="cat-elem view"><a href="<?php echo osc_static_page_url();?>" target="_blank"><i class="fa fa-external-link-square"></i><?php _e('View', 'all_in_one'); ?></a></div>
          </div>

        <?php } ?>
      </div>
    </fieldset>

    <br /><br />
    <button name="theButton" id="theButton" type="submit" style="float: left;" class="btn btn-submit"><?php _e('Update pages', 'all_in_one');?></button>
  </form>

  <div class="clear"></div>
  <br /><br />

  <div class="warn"><?php _e('If you do not enter Meta details for page, default osclass settings are used.', 'all_in_one'); ?></div>
  <div class="warn"><?php _e('Meta title should have <strong>50-60 characters</strong>, Meta description <strong>150-160 characters</strong> and there should be <strong>4-5 keywords</strong>.', 'all_in_one'); ?></div>
  <div class="clear"></div>
  <br /><br />

  <?php echo $pluginInfo['plugin_name'] . ' | ' . __('Version','all_in_one') . ' ' . $pluginInfo['version'] . ' | ' . __('Author','all_in_one') . ': ' . $pluginInfo['author'] . ' | Cannot be redistributed | &copy; ' . date('Y') . ' <a href="' . $pluginInfo['plugin_uri'] . '" target="_blank">MB Themes</a>'; ?>             
</div>