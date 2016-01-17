<?php

$pluginInfo = osc_plugin_get_info('all_in_one/index.php');
$dao_preference = new Preference();

$rewriteEnabled = osc_get_preference('rewriteEnabled', 'osclass');

$htaccessEnabled = '';
if(Params::getParam('htaccessEnabled') != '') {
  $htaccessEnabled = Params::getParam('htaccessEnabled');
} else {
  $htaccessEnabled = (osc_get_preference('allSeo_htaccess_enabled', 'plugin-all_in_one') != '') ? osc_get_preference('allSeo_htaccess_enabled', 'plugin-all_in_one') : '' ;
}

$htaccess = '';
$dao_preference = new Preference();
if(Params::getParam('htaccess') != '') {
  $htaccess = Params::getParam('htaccess', false, false);
} else {
  $htaccess = (osc_get_preference('allSeo_htaccess', 'plugin-all_in_one') != '') ? osc_get_preference('allSeo_htaccess', 'plugin-all_in_one') : '' ;
}

// Define default content of .htaccess file
$def = 
'<IfModule mod_rewrite.c>
RewriteEngine On
RewriteBase /
RewriteRule ^index\.php$ - [L]
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule . /index.php [L]
</IfModule>';

$def_text = '&lt;IfModule mod_rewrite.c&gt;<br>&nbsp;&nbsp;RewriteEngine On<br>&nbsp;&nbsp;RewriteBase /<br>&nbsp;&nbsp;RewriteRule ^index\.php$ - [L]<br>&nbsp;&nbsp;RewriteCond %{REQUEST_FILENAME} !-f<br>&nbsp;&nbsp;RewriteCond %{REQUEST_FILENAME} !-d<br>&nbsp;&nbsp;RewriteRule . /index.php [L]<br>&lt;/IfModule&gt;';

if(Params::getParam('plugin_action')=='done') {
  if($htaccessEnabled == 0) { 
    if(file_exists(osc_base_path() .  ".htaccess_backup")) {
      $content = file_get_contents(osc_base_path() .  ".htaccess_backup");
    } else {
      $content = $def; 
    }

    $htaccess = $content;
  } else {
    if(file_exists(osc_base_path() .  ".htaccess_backup") <> 1) {
      $fp_backup = fopen(osc_base_path() .  ".htaccess_backup", "wb"); 
      fwrite($fp_backup, file_get_contents(osc_base_path() .  ".htaccess"));
      fclose($fp_backup);
      message_ok(__('Backup file .htaccess_backup file was successfully created', 'all_in_one'));
    }

    $content = $htaccess;  
  }

  $fp = fopen(osc_base_path() .  "/.htaccess", "wb"); 
  fwrite($fp, $content);
  fclose($fp);
  
  osc_reset_preferences();
  message_ok(__('.htaccess file was successfully updated','all_in_one'));

  if(!is_writable(osc_base_path() .  "/.htaccess")) {
    message_error(__('It is impossible to write to .htaccess file, please change CHMOD settings on this file.','all_in_one'));
  }
  
  $dao_preference->update(array("s_value" => $htaccessEnabled), array("s_section" => "plugin-all_in_one", "s_name" => "allSeo_htaccess_enabled")) ;
  $dao_preference->update(array("s_value" => $htaccess), array("s_section" => "plugin-all_in_one", "s_name" => "allSeo_htaccess")) ; 
}

unset($dao_preference) ;
?>

<div id="settings_form">
  <?php echo config_menu(); ?>

  <?php if($rewriteEnabled <> 1) { ?>
    <?php message_error( __('You did not activated Friendly URLs. Activate it first or you will not be able to save settings.', 'all_in_one') . '</br></br><strong><a href="' . osc_base_url() . 'oc-admin/index.php?page=settings&action=permalinks" target="_blank">' . __('Activate Permalinks (Friendly URLs) >>', 'all_in_one') . '</a></strong>'); ?>
  <?php } ?>

  <form name="promo_form" id="promo_form" action="<?php echo osc_admin_base_url(true); ?>" method="POST" enctype="multipart/form-data" >
    <input type="hidden" name="page" value="plugins" />
    <input type="hidden" name="action" value="renderplugin" />
    <input type="hidden" name="file" value="<?php echo osc_plugin_folder(__FILE__); ?>htaccess.php" />
    <input type="hidden" name="plugin_action" value="done" />

    <fieldset class="round3">
      <legend class="green round2"><?php _e('.htaccess file settings', 'all_in_one'); ?></legend>

      <label for="htaccessEnabled" class="text-label htaccess"><?php _e('Choose .htaccess you want to use', 'all_in_one'); ?>  <sup class="sup-go go1">(1)</sup></label>
      <select name="htaccessEnabled" id="htaccessEnabled"> 
        <option <?php if($htaccessEnabled == 1){echo 'selected="selected"';}?>value='1'><?php _e('Custom','all_in_one'); ?></option>
        <option <?php if($htaccessEnabled == 0){echo 'selected="selected"';}?>value='0'><?php _e('Original','all_in_one'); ?></option>
      </select>

      <div class="clear" style="margin:6px 0;"></div>
      
      <label for="htaccess" class="text-label htaccess"><?php _e('.htaccess file content', 'all_in_one'); ?></label>
      <textarea <?php if ($htaccessEnabled == 0) { echo "disabled"; } ?> rows="20" type="text" id="htaccess" name="htaccess"><?php echo $htaccess; ?></textarea>
    </fieldset>				       

    <br /><br />  
                       
    <button <?php if($rewriteEnabled <> 1) { echo 'disabled'; } ?> name="theButton" id="theButton" type="submit" style="float: left;" class="btn btn-submit"><?php _e('Update .htaccess file', 'all_in_one');?></button>
  </form>
  
  <div class="clear"></div>
  <br /><br />

  <div class="warn"><sup class="sup-go1">(1)</sup> <?php _e('You can use .htaccess generated by this form (custom) or default .htaccess (original). When original choosen will be choosen, copy from backup file will be used to restore original file', 'all_in_one'); ?></div>
  <div class="warn"><?php _e('When you create .htaccess file using this form, original one will be back up in same folder with name <strong>.htaccess_backup</strong>. This file will be used just in case it does not exist. If it exists, it will not be overwritten.', 'all_in_one'); ?></div>
  <div class="warn">
    <?php _e('Do not miss following pages that helps you to understand and correctly generate .htaccess file:', 'all_in_one'); ?><br />
    <i class="fa fa-angle-right"></i>&nbsp;<a href="http://www.htaccessredirect.net/" target="_blank"><?php _e('.htaccess generator', 'all_in_one'); ?></a><br />
    <i class="fa fa-angle-right"></i>&nbsp;<a href="http://httpd.apache.org/docs/2.2/howto/htaccess.html" target="_blank"><?php _e('.htaccess documentation', 'all_in_one'); ?></a>
  </div>
  <div class="warn"><?php _e('Default .htaccess file generated by osclass has content:', 'all_in_one'); ?><br /><br /><div class="code-text"><?php echo $def_text; ?></div></div>
  <div class="warn"><?php _e('When you get <strong>500 Internal Server Error</strong> on your site, there was some problem in saving .htaccess file or your code is incorrect. In this case just replace content of file with default content listed above.', 'all_in_one'); ?></div>
</div>

<div class="clear"></div>
<br /><br />

<?php echo $pluginInfo['plugin_name'] . ' | ' . __('Version','all_in_one') . ' ' . $pluginInfo['version'] . ' | ' . __('Author','all_in_one') . ': ' . $pluginInfo['author'] . ' | Cannot be redistributed | &copy; ' . date('Y') . ' <a href="' . $pluginInfo['plugin_uri'] . '" target="_blank">MB Themes</a>'; ?>             

<script>
  if($('#htaccessEnabled').val() == 0) { $('#htaccess').css('opacity', '0.7'); $('#htaccess').prop('disabled', true); $('#htaccess').css('color', '#666'); $('#htaccess').css('background-color', '#fefefe'); } else { $('#htaccess').prop('disabled', false); $('#htaccess').css('color', '#000'); $('#htaccess').css('background-color', '#fff'); $('#htaccess').css('opacity', '1');}
</script>