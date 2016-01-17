<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/<?php echo osc_current_user_locale(); ?>/sdk.js#xfbml=1&version=v2.4";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>

<div class="fb-page" 
	data-href="<?php echo $fb_page_url; ?>" 
    data-width="<?php echo $fb_page_width; ?>" 
    data-height="<?php echo $fb_page_height; ?>" 
    data-small-header="<?php if(dd_use_small_header()=="true"){echo "true";}else{echo "false";}?>" 
    data-adapt-container-width="<?php if(dd_adapt_container_width()=="true"){echo "true";}else{echo "false";}?>" 
    data-hide-cover="<?php if(dd_hide_page_cover()=="false"){echo "false";}else{echo "true";}?>" 
    data-show-facepile="<?php if(dd_fb_show_faces()=="true"){echo "true";}else{echo "false";}?>" 
    data-show-posts="<?php if(dd_show_page_posts()=="true"){echo "true";}else{echo "false";}?>">
    <div class="fb-xfbml-parse-ignore"><blockquote cite="<?php echo $fb_page_url; ?>"><a href="<?php echo $fb_page_url; ?>">Facebook</a></blockquote></div></div>
