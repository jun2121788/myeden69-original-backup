<?php
$lat = -34.397;
$long = 150.644;
if(osc_user_latitude()!='')       $lat = osc_user_latitude();
if(osc_user_longitude()!='')    $long = osc_user_longitude();
?>

<script src="https://maps.googleapis.com/maps/api/js?v=3"></script>
<script type="text/javascript">
    function initialize() {
        var lat = <?php echo $lat;?>;
        var long = <?php echo $long;?>;
        var mapOptions = {
            center: new google.maps.LatLng(lat, long),
            zoom: 11,
            disableDefaultUI: true,
            scrollwheel: false,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        };
        var map = new google.maps.Map(document.getElementById("map_canvas"),
                mapOptions);
    }

    $(document).ready(function (event) {
        initialize();
    });
</script>

<div id="map_canvas" style="width:100%; height:320px;  z-index: -1;"></div>

<div class="wrapper container-user-card">
    <div class="user-card">
        <div class="user-card-left">
            <img class="user-avatar" src="<?php echo osc_current_web_theme_url('images/user_default.gif'); ?>" />
            <?php /* <img class="user-avatar" src="http://www.gravatar.com/avatar/<?php echo md5(strtolower(trim(osc_user_email()))); ?>?s=120&d=<?php echo osc_current_web_theme_url('images/user_default.gif'); ?>" /> */ ?>
        </div>
        <div class="user-card-right">
            <ul id="user_data">
                <li class="name"><?php echo osc_highlight(osc_user_name(),120); ?></li>
                <li class="share-links">
                    <span><?php _e('Share it!', 'pop'); ?></span>
                    <p>
                        <a href="<?php echo pop_facebook_share_url(); ?>" class="share-icon facebook-icon"></a>
                        <a href="<?php echo pop_twitter_share_url(); ?>" class="share-icon twitter-icon"></a>
                        <a href="<?php echo pop_email_share_url(); ?>" class="share-icon email-icon"></a>
                    <p>
                </li>
            </ul>
        </div>
        <div class="clear"></div>
    </div>
</div>
