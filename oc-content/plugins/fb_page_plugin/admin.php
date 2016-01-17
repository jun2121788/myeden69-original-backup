<style type="text/css">
	#content-page { background:#e5e5e5; }
	.col6 { width:650px; float:left; margin:0 10px; }
	.col5 { width:500px; float:left;  margin:0 10px; }
	.form-container { border:2px solid #ddd; background:#fff; }
	.form-container h2 { margin:0; padding:10px 15px; background:#47639e; color:#fff;}
	.form-container fieldset { padding:15px; }
	.form-container .quote { padding:15px; border:1px solid #eee; background:#f9f9f9; margin:15px; }
	.control-group { margin-bottom:15px; }
	.control-label { margin-bottom:5px; display:block; }
	.form-actions { margin-top:15px; margin-bottom:0;}
	.form-horizontal .form-actions { padding-left:15px;}
	.form-container input[type=text] { padding:10px; width:95%; }
	.form-container input[type=text].swidth { width:30%; }
	
	.help { color:#999; font-size:12px; }
	.clearfix { clear:both; }
</style>
<div class="plugin-page">
	<div class="col5">
		<?php $pluginInfo = osc_plugin_get_info('fb_page_plugin/index.php');  ?>
        <div class="form-container">
        <h2 class="render-title"><?php _e('Facebook Page Plugin Settings', 'fb_page_plugin'); ?></h2>
        <form action="<?php echo osc_admin_render_plugin_url('fb_page_plugin/admin.php'); ?>" class="form-horizontal" method="post">
          <input type="hidden" name="fbpageoption" value="fbpagesettings" />
          <fieldset>
          <div class="control-group">
            <label class="control-label"><?php _e('Facebook Page URL', 'fb_page_plugin'); ?></label>
            <div class="controls">
                <input type="text" name="fb_page_url" value="<?php echo osc_esc_html(dd_fb_page_url()); ?>">
            </div>
            <span class="help">Get your Facebook page - <a href="https://www.facebook.com/pages/create/" target="_blank">Click here</a></span>
          </div>
          <div class="control-group">
            <label class="control-label"><?php _e('Width', 'fb_page_plugin'); ?></label>
            <div class="controls">
                <input type="text" class="swidth" name="fb_page_width" value="<?php echo osc_esc_html(dd_fb_page_width()); ?>">
            </div>
          </div>
          <div class="control-group">
            <label class="control-label"><?php _e('Height', 'fb_page_plugin'); ?></label>
            <div class="">
                <input type="text" class="swidth" name="fb_page_height" value="<?php echo osc_esc_html(dd_fb_page_height()); ?>">
            </div>
          </div>
          <div class="control-group">
            
            <div class="controls checkbox">
                <input type="checkbox" <?php echo (dd_fb_show_faces() ? 'checked="true"' : ''); ?> name="fb_show_faces" id="fb_show_faces" value="true" />
                <label><?php _e('Show Friends Faces', 'fb_page_plugin'); ?></label>
            </div>
          </div>
          
          
          <div class="control-group">
            <div class="controls checkbox">
                <input type="checkbox" <?php echo (dd_hide_page_cover() ? 'checked="false"' : ''); ?> name="hide_page_cover" id="hide_page_cover" value="false" />
                <label><?php _e('Show Cover Photo', 'fb_page_plugin'); ?></label>
            </div>
          </div>
          <div class="control-group">
            <div class="controls checkbox">
                <input type="checkbox" <?php echo (dd_show_page_posts() ? 'checked="true"' : ''); ?> name="show_page_posts" id="show_page_posts" value="true" />
                <label><?php _e('Show Page Posts', 'fb_page_plugin'); ?></label>
            </div>
          </div>
          <div class="control-group">
            <div class="controls checkbox">
                <input type="checkbox" <?php echo (dd_use_small_header() ? 'checked="true"' : ''); ?> name="use_small_header" id="use_small_header" value="true" />
                <label><?php _e('Use Small Header', 'fb_page_plugin'); ?></label>
            </div>
          </div>
          <div class="control-group">
            <div class="controls checkbox">
                <input type="checkbox" <?php echo (dd_adapt_container_width() ? 'checked="true"' : ''); ?> name="adapt_container_width" id="adapt_container_width" value="true" />
                <label><?php _e('Adapt to container width', 'fb_page_plugin'); ?></label>
            </div>
          </div>
          </fieldset>
          <div class="quote">
            Place following code into theme file wherever you need to show.<br />
            <pre>&lt;?php fb_page_plugin(); ?&gt;</pre>
          </div>
          <div class="form-actions">
            <input type="submit" value="<?php _e('Save changes', 'fb_page_plugin'); ?>" class="btn btn-submit">
          </div>
          
        </form>
        
        </div>
	</div><!-- /Col5 -->
    
    <div class="col6">
    	<div id="fb_page" class="promobox">
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