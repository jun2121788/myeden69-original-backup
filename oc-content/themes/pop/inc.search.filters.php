<?php
$current_category_id = '';
$current_category_parent = '';
$_current_category_id = osc_search_category_id();
if( count($_current_category_id) > 0 ) {
    $current_category_id = $_current_category_id[0];

    $_current_category = Category::newInstance()->findByPrimaryKey( $current_category_id );
    $current_category_parent = $_current_category['fk_i_parent_id'];
}
$limit_regions = $limit_cities = 10;
$filter_region_id = '%%%%';

osc_get_non_empty_categories();
osc_goto_first_category();
$aCategories = array();
while (osc_has_categories()) {
    $aCategories[] = array(
        'id' => osc_category_id(),
        'slug' => osc_category_slug(),
        'name' => osc_category_name(),
        'url' => osc_update_search_url(array('sCategory' => osc_category_slug())),
        'total_items' => osc_category_total_items()
    );
}
?>
<div class="title-section"><span><i class="ico-filter_category"></i><?php _e('Categories', 'pop'); ?></span></div>
<div class="collections">
    <?php foreach ($aCategories as $category) {
        $active = "";
        if($category['slug']==Params::getParam('sCategory') ||
           $category['id']==Params::getParam('sCategory') ||
           $category['id'] == $current_category_parent) {
            $active = "active";
        } ?>
    <div>
        <a href="<?php echo $category['url']; ?>" class="<?php echo $active . ' ' . osc_esc_html($category['slug']); ?>">
            <?php echo $category['name']; ?>
        </a>
        <?php if($active!='') { ?>
        <a class="<?php echo $active; ?>"  href="<?php echo osc_update_search_url(array('sCategory' => null)); ?>"><i class="fa fa-times-circle"></i>&nbsp;</a>
        <?php } ?>
    </div>
    <?php } ?>
</div>

<?php if(osc_count_list_regions()>0) { ?>
<div class="title-section"><span><i class="ico-filter_region"></i><?php _e('Region', 'pop'); ?></span></div>
<div class="collections">
    <?php $i = 0;
    while (osc_has_list_regions()) {
        $active = '';
        if(osc_list_region_name()==Params::getParam('sRegion') ||
           osc_list_region_id()==Params::getParam('sRegion') ){
            $filter_region_id = osc_list_region_id();
            $active = 'active';
           }
        ?>
    <div>
    <a class="<?php echo $active; ?>" href="<?php echo osc_update_search_url(array('sRegion' => osc_list_region_name())); ?>"><?php echo osc_list_region_name(); ?></a>
    <?php if($active!='') { ?>
        <a class="<?php echo $active; ?>"  href="<?php echo osc_update_search_url(array('sRegion' => null)); ?>"><i class="fa fa-times-circle"></i>&nbsp;</a>
    <?php } ?>
    </div>
        <?php $i++;
        if ($i == $limit_regions) {
            ?>

            <?php break;
        }
        ?>
<?php } ?>
</div>
<?php } ?>

<?php View::newInstance()->_exportVariableToView('list_cities', CityStats::newInstance()->listCities($filter_region_id) ); ?>
<?php if(osc_count_list_cities()>0) { ?>
<div class="title-section"><span><i class="ico-filter_city"></i><?php _e('City', 'pop'); ?></span></div>
<div class="collections">
    <?php $i = 0;
    while (osc_has_list_cities()) {
        $active = '';
        if(osc_list_city_name()==Params::getParam('sCity') ||
           osc_list_city_id()==Params::getParam('sCity') ){
            $active = 'active';
           }
        ?>
    <div>
    <a class='<?php echo $active; ?>' href="<?php echo osc_update_search_url(array('sCity' => osc_list_city_name())); ?>"><?php echo osc_list_city_name(); ?></a>
    <?php if($active!='') { ?>
        <a class="<?php echo $active; ?>"  href="<?php echo osc_update_search_url(array('sCity' => null)); ?>"><i class="fa fa-times-circle"></i>&nbsp;</a>
    <?php } ?>
    </div>
        <?php $i++;
        if ($i == $limit_cities) {
            ?>

        <?php break;
    }
    ?>
        <?php } ?>
</div>
<?php } ?>
<form action="<?php echo osc_base_url(true); ?>" method="get" class="nocsrf">
    <input type="hidden" name="page" value="search"/>
    <?php
    foreach(Params::getParamsAsArray() as $key => $value) {
        if($key!='page' && $key!='action') {?>
    <input type="hidden" name="<?php echo osc_esc_html($key); ?>" value="<?php echo osc_esc_html($value); ?>"/>
    <?php
        }
    } ?>
    <div class="plugin-hooks">
        <?php
        if (osc_search_category_id()) {
            osc_run_hook('search_form', osc_search_category_id());
        } else {
            osc_run_hook('search_form');
        }
        ?>
    </div>
    <p class="center"><button type="submit" class="btn-round btn-gray"><?php _e('Apply', 'pop') ; ?></button></p>
</form>