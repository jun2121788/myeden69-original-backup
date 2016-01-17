<?php
// meta tag robots
osc_add_hook('header', 'pop_follow_construct');

$address = '';
if (osc_user_address() != '') {
    if (osc_user_city_area() != '') {
        $address = osc_user_address() . ", " . osc_user_city_area();
    } else {
        $address = osc_user_address();
    }
} else {
    $address = osc_user_city_area();
}
$location_array = array();
if (trim(osc_user_city() . " " . osc_user_zip()) != '') {
    $location_array[] = trim(osc_user_city() . " " . osc_user_zip());
}
if (osc_user_region() != '') {
    $location_array[] = osc_user_region();
}
if (osc_user_country() != '') {
    $location_array[] = osc_user_country();
}
$location = implode(", ", $location_array);
unset($location_array);

osc_enqueue_script('jquery-validate');

pop_add_body_class('user-public-profile');

osc_add_hook('before-main', 'pop_user_map_header');
function pop_user_map_header() {
    osc_current_web_theme_path('inc.user_header_public_profile.php');
}
osc_current_web_theme_path('header.php');
?>


<?php if (osc_count_items() > 0) {
    osc_current_web_theme_path('loop.php');
} ?>

<?php osc_current_web_theme_path('footer.php'); ?>