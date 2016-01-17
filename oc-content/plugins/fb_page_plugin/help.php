<style type="text/css">
	#content-page { background:#e5e5e5; }
	.col6 { width:600px; float:left; margin:0 10px; }
	.col5 { width:500px; float:left;  margin:0 10px; }
	.form-container { border:2px solid #ddd; background:#fff; }
	.form-container h2 { margin:0; padding:10px 15px; background:#47639e; color:#fff;}
	.form-container fieldset { padding:15px; }
	.control-group { margin-bottom:15px; }
	.control-label { margin-bottom:5px; display:block; }
	.form-actions { margin-top:15px; margin-bottom:0;}
	.form-horizontal .form-actions { padding-left:15px;}
	.form-container input[type=text] { padding:10px; width:95%; }
	.form-container input[type=text].swidth { width:30%; }
	
	.clearfix { clear:both; }
</style>
<div class="plugin-page">
	<div class="col5">
		<?php $pluginInfo = osc_plugin_get_info('fb_page_plugin/index.php');  ?>
        <div class="form-container">
        <h2 class="render-title"><?php _e('Facebook Page Plugin Help', 'fb_page_plugin'); ?></h2>
        	
          
        </div>
	</div><!-- /Col5 -->
    
    <div class="col6">
    	<div class="promobox">
		<?php echo dd_fb_sidebar();?> 
        <?php echo dd_fb_related();?>
        </div>
    </div><!-- /COL6 -->
    <div class="clearfix"></div>
</div>

<div class="footer">
    <p class="form-row">
    &copy; <?php echo date('Y'); ?> Facebook Page Plugin - <a href="<?php echo osc_admin_render_plugin_url('fb_page_plugin/help.php'); ?>"><?php _e('Help', 'fb_page_plugin'); ?></a> - <a href="http://forums.osclass.org/plugins/facebook-page-plugin/" target="_blank"><?php _e('Support', 'fb_page_plugin'); ?></a> - <a href="http://www.drizzlethemes.com/">DrizzleThemes</a>.
    </p>
</div>
