<?php
/*
  Plugin Name: All in One SEO Plugin
  Plugin URI: http://www.mb-themes.com
  Description: Allows you to manage & boost SEO on your site.
  Version: 2.1.1
  Author: MB Themes
  Author URI: http://www.mb-themes.com
  Author Email: info@mb-themes.com
  Short Name: all_in_one
  Plugin update URI: all-in-one-seo-plugin
*/

require_once 'model/ModelSeo.php';
require_once 'model/ModelSeoPage.php';
require_once 'model/ModelSeoLink.php';
require_once 'model/ModelSeoCategory.php';
require_once 'model/ModelSeoLocation.php';
require_once 'model/ModelSeoStats.php';
require_once 'sitemap.php';
require_once 'email.php';

function allSeo_call_after_install() {
  if(file_exists($_SERVER['DOCUMENT_ROOT']."/robots.txt")) {$rob = file_get_contents($_SERVER['DOCUMENT_ROOT']."/robots.txt");} else {$rob = '';}
  if(file_exists($_SERVER['DOCUMENT_ROOT']."/.htaccess")) {$hta = file_get_contents($_SERVER['DOCUMENT_ROOT']."/.htaccess");} else {$hta = '';}
  ModelSeo::newInstance()->import('all_in_one/model/struct.sql');

  osc_set_preference('allSeo_description', '1', 'plugin-all_in_one', 'INTEGER');
  osc_set_preference('allSeo_keywords', 'classified, free classified, classified web, free listings, cars for sale, sale', 'plugin-all_in_one', 'STRING');
  osc_set_preference('allSeo_title_first', '0', 'plugin-all_in_one', 'INTEGER');
  osc_set_preference('allSeo_robots', $rob, 'plugin-all_in_one', 'STRING');
  osc_set_preference('allSeo_robots_enabled', '0', 'plugin-all_in_one', 'INTEGER');
  osc_set_preference('allSeo_htaccess', $hta, 'plugin-all_in_one', 'STRING');
  osc_set_preference('allSeo_htaccess_enabled', '0', 'plugin-all_in_one', 'INTEGER');
  osc_set_preference('allSeo_delimiter', '|', 'plugin-all_in_one', 'STRING');

  osc_set_preference('allSeo_city_show', '1', 'plugin-all_in_one', 'INTEGER');
  osc_set_preference('allSeo_city_order', '1', 'plugin-all_in_one', 'INTEGER');
  osc_set_preference('allSeo_region_show', '1', 'plugin-all_in_one', 'INTEGER');
  osc_set_preference('allSeo_region_order', '2', 'plugin-all_in_one', 'INTEGER');
  osc_set_preference('allSeo_country_show', '1', 'plugin-all_in_one', 'INTEGER');
  osc_set_preference('allSeo_country_order', '3', 'plugin-all_in_one', 'INTEGER');
  osc_set_preference('allSeo_category_show', '1', 'plugin-all_in_one', 'INTEGER');
  osc_set_preference('allSeo_category_order', '4', 'plugin-all_in_one', 'INTEGER');
  osc_set_preference('allSeo_title_show', '1', 'plugin-all_in_one', 'INTEGER');
  osc_set_preference('allSeo_title_order', '5', 'plugin-all_in_one', 'INTEGER');
  osc_set_preference('allSeo_body_order', '6', 'plugin-all_in_one', 'INTEGER');
  osc_set_preference('allSeo_page_title', '', 'plugin-all_in_one', 'STRING');

  osc_set_preference('allSeo_search_city_show', '1', 'plugin-all_in_one', 'INTEGER');
  osc_set_preference('allSeo_search_city_order', '1', 'plugin-all_in_one', 'INTEGER');
  osc_set_preference('allSeo_search_region_show', '1', 'plugin-all_in_one', 'INTEGER');
  osc_set_preference('allSeo_search_region_order', '2', 'plugin-all_in_one', 'INTEGER');
  osc_set_preference('allSeo_search_country_show', '1', 'plugin-all_in_one', 'INTEGER');
  osc_set_preference('allSeo_search_country_order', '3', 'plugin-all_in_one', 'INTEGER');
  osc_set_preference('allSeo_search_category_order', '4', 'plugin-all_in_one', 'INTEGER');
  osc_set_preference('allSeo_search_pattern_order', '6', 'plugin-all_in_one', 'INTEGER');
  osc_set_preference('allSeo_search_title_show', '1', 'plugin-all_in_one', 'INTEGER');
  osc_set_preference('allSeo_search_title_order', '5', 'plugin-all_in_one', 'INTEGER');
  osc_set_preference('allSeo_search_page_title', '', 'plugin-all_in_one', 'STRING');
  osc_set_preference('allSeo_search_improve_desc', '1', 'plugin-all_in_one', 'INTEGER');

  osc_set_preference('allSeo_other_page_title', '', 'plugin-all_in_one', 'STRING');

  osc_set_preference('allSeo_sitemap_freq', 'weekly', 'plugin-all_in_one', 'STRING');
  osc_set_preference('allSeo_links_footer', '1', 'plugin-all_in_one', 'INTEGER');
  osc_set_preference('allSeo_sitemap_items', '1', 'plugin-all_in_one', 'INTEGER');
  osc_set_preference('allSeo_sitemap_items_limit', '1000', 'plugin-all_in_one', 'INTEGER');
  osc_set_preference('allSeo_allow_custom_meta', '1', 'plugin-all_in_one', 'INTEGER');

  //upload email templates
  foreach(osc_listLocales() as $loc) {
    //bo_mgr_email_expired template
    $des_link[$loc['code']]['s_title'] = '{WEB_TITLE} - There is problem with backlink placed on your site';
    $des_link[$loc['code']]['s_text'] = '<p>Dear Partner!</p> <p>Let us inform you, that we were not able to find link referring to our site: <strong>{LINK_TO}</strong> on your website <strong>{LINK_FROM}</strong>.</p> <p>Please add our link to your site or our cooperation in backlink building will be cancelled. If reason of removing link is maintenance or similar, please inform us about this.</p> <p>Regards, <br />{WEB_TITLE}</p>';
  }
  
  Page::newInstance()->insert( array('s_internal_name' => 'seo_link_problem', 'b_indelible' => '1'), $des_link );
}

function allSeo_call_after_uninstall() {
  ModelSeo::newInstance()->uninstall();
  ModelSeoPage::newInstance()->uninstall();
  ModelSeoLink::newInstance()->uninstall();
  ModelSeoCategory::newInstance()->uninstall();

  osc_delete_preference('allSeo_description', 'plugin-all_in_one');
  osc_delete_preference('allSeo_keywords', 'plugin-all_in_one');
  osc_delete_preference('allSeo_title_first', 'plugin-all_in_one');
  osc_delete_preference('allSeo_robots', 'plugin-all_in_one');
  osc_delete_preference('allSeo_robots_enabled', 'plugin-all_in_one');
  osc_delete_preference('allSeo_htaccess', 'plugin-all_in_one');
  osc_delete_preference('allSeo_htaccess_enabled', 'plugin-all_in_one');
  osc_delete_preference('allSeo_delimiter', 'plugin-all_in_one');

  osc_delete_preference('allSeo_city_show', 'plugin-all_in_one');
  osc_delete_preference('allSeo_city_order', 'plugin-all_in_one');
  osc_delete_preference('allSeo_region_show', 'plugin-all_in_one');
  osc_delete_preference('allSeo_region_order', 'plugin-all_in_one');
  osc_delete_preference('allSeo_country_show', 'plugin-all_in_one');
  osc_delete_preference('allSeo_country_order', 'plugin-all_in_one');
  osc_delete_preference('allSeo_category_show', 'plugin-all_in_one');
  osc_delete_preference('allSeo_category_order', 'plugin-all_in_one');
  osc_delete_preference('allSeo_title_show', 'plugin-all_in_one');
  osc_delete_preference('allSeo_title_order', 'plugin-all_in_one');
  osc_delete_preference('allSeo_body_order', 'plugin-all_in_one');
  osc_delete_preference('allSeo_page_title', 'plugin-all_in_one');

  osc_delete_preference('allSeo_search_city_show', 'plugin-all_in_one');
  osc_delete_preference('allSeo_search_city_order', 'plugin-all_in_one');
  osc_delete_preference('allSeo_search_region_show', 'plugin-all_in_one');
  osc_delete_preference('allSeo_search_region_order', 'plugin-all_in_one');
  osc_delete_preference('allSeo_search_country_show', 'plugin-all_in_one');
  osc_delete_preference('allSeo_search_country_order', 'plugin-all_in_one');
  osc_delete_preference('allSeo_search_category_show', 'plugin-all_in_one');
  osc_delete_preference('allSeo_search_category_order', 'plugin-all_in_one');
  osc_delete_preference('allSeo_search_pattern_order', 'plugin-all_in_one');
  osc_delete_preference('allSeo_search_title_show', 'plugin-all_in_one');
  osc_delete_preference('allSeo_search_title_order', 'plugin-all_in_one');
  osc_delete_preference('allSeo_search_body_order', 'plugin-all_in_one');
  osc_delete_preference('allSeo_search_page_title', 'plugin-all_in_one');
  osc_delete_preference('allSeo_search_improve_desc', 'plugin-all_in_one');

  osc_delete_preference('allSeo_other_page_title', 'plugin-all_in_one');

  osc_delete_preference('allSeo_sitemap_freq', 'plugin-all_in_one');
  osc_delete_preference('allSeo_links_footer', 'plugin-all_in_one');
  osc_delete_preference('allSeo_sitemap_items', 'plugin-all_in_one');
  osc_delete_preference('allSeo_sitemap_items_limit', 'plugin-all_in_one');
  osc_delete_preference('allSeo_allow_custom_meta', 'plugin-all_in_one');

  //get list of primary keys of static pages (emails) that should be deleted on uninstall
  $pages = ModelSeoLink::newInstance()->getPages();  
  foreach($pages as $page) {
    Page::newInstance()->deleteByPrimaryKey($page['pk_i_id']);
  }
}

if(!function_exists('osc_search_country')) {
  function osc_search_country() {
    if(View::newInstance()->_get('search_country')) {
      return View::newInstance()->_get('search_country');
    } else {
      return Params::getParam('sCountry');
    }
  }
}


$myPlugin = file(osc_base_path() . 'oc-content/plugins/all_in_one/index.php');

if(!function_exists('message_ok')) {
  function message_ok( $text ) {
    $final  = '<div style="padding: 1%;width: 98%;margin-bottom: 15px;" class="flashmessage flashmessage-ok flashmessage-inline">';
    $final .= $text;
    $final .= '</div>';
    echo $final;
  }
}

if(!function_exists('message_error')) {
  function message_error( $text ) {
    $final  = '<div style="padding: 1%;width: 98%;margin-bottom: 15px;" class="flashmessage flashmessage-error flashmessage-inline">';
    $final .= $text;
    $final .= '</div>';
    echo $final;
  }
}

function Delimiter(){
  return ' ' . osc_get_preference('allSeo_delimiter','plugin-all_in_one') . ' ';
}

function SeoLocationShow(){
  $sort_order = array();
  $elements = array();
  $del = Delimiter();

  if(osc_get_preference('allSeo_city_show','plugin-all_in_one') == 1){
    $elements[] = osc_item_city();
    $sort_order[] = osc_get_preference('allSeo_city_order','plugin-all_in_one');
  }
  
  if(osc_get_preference('allSeo_region_show','plugin-all_in_one') == 1){
    $elements[] = osc_item_region();
    $sort_order[] = osc_get_preference('allSeo_region_order','plugin-all_in_one');
  }
  
  if(osc_get_preference('allSeo_country_show','plugin-all_in_one') == 1){
    $elements[] = osc_item_country();
    $sort_order[] = osc_get_preference('allSeo_country_order','plugin-all_in_one');
  }
  
  array_multisort($sort_order, $elements);
  $elements = array_filter( $elements );
  return implode(' - ', $elements);
}

function SeoGenerateTitleListing(){
  $sort_order = array();
  $elements = array();
  $del = Delimiter();

  if(osc_get_preference('allSeo_city_show','plugin-all_in_one') == 1){
    if(osc_item_city() <> '') {
      $elements[] = osc_item_city();
      $sort_order[] = osc_get_preference('allSeo_city_order','plugin-all_in_one');
    }
  }
  
  if(osc_get_preference('allSeo_region_show','plugin-all_in_one') == 1){
    if(osc_item_region() <> '') {
      $elements[] = osc_item_region();
      $sort_order[] = osc_get_preference('allSeo_region_order','plugin-all_in_one');
    }
  }
  
  if(osc_get_preference('allSeo_country_show','plugin-all_in_one') == 1){
    if(osc_item_country() <> '') {
      $elements[] = osc_item_country();
      $sort_order[] = osc_get_preference('allSeo_country_order','plugin-all_in_one');
    }
  }
  
  if(osc_get_preference('allSeo_title_show','plugin-all_in_one') == 1){
    if( osc_get_preference('allSeo_page_title', 'plugin-all_in_one') == '') {
      $elements[] = osc_page_title();
    } else {
      $elements[] = osc_get_preference('allSeo_page_title', 'plugin-all_in_one');
    }
    
    $sort_order[] = osc_get_preference('allSeo_title_order','plugin-all_in_one');
  }
  
  if(osc_get_preference('allSeo_category_show','plugin-all_in_one') == 1){
    $elements[] = osc_item_category();
    $sort_order[] = osc_get_preference('allSeo_category_order','plugin-all_in_one');
  }

  if(GetItemTitle(osc_item_id()) == '') {
    $elements[] = osc_item_title();
  } else {
    $elements[] = GetItemTitle(osc_item_id());
  }

  $sort_order[] = osc_get_preference('allSeo_body_order','plugin-all_in_one');
  
  array_multisort($sort_order, $elements);
  $elements = array_filter( $elements );
  return implode($del, $elements);
}

function SeoGenerateTitleCategory(){
  $sort_order = array();
  $elements = array();
  $del = Delimiter();
  $cat = osc_search_category_id();
  $cat = $cat[0];
  $cat_name = Category::newInstance()->findByPrimaryKey($cat);
  $cat_name = $cat_name['s_name'];  
  $cat_name_seo = GetCatTitle( $cat ) ; 

  if(osc_get_preference('allSeo_search_city_show','plugin-all_in_one') == 1){
    if(osc_search_city() <> '') {
      $elements[] = osc_search_city();
      $sort_order[] = osc_get_preference('allSeo_search_city_order','plugin-all_in_one');
    }
  }
  
  if(osc_get_preference('allSeo_search_region_show','plugin-all_in_one') == 1){
    if(osc_search_region() <> '') {
      $elements[] = GetRegTitle(osc_search_region()) ? GetRegTitle(osc_search_region()) : osc_search_region();
      $sort_order[] = osc_get_preference('allSeo_search_region_order','plugin-all_in_one');
    }
  }
  
  if(osc_get_preference('allSeo_search_country_show','plugin-all_in_one') == 1){
    if(osc_search_country() <> '') {
      $elements[] = GetCtrTitle(osc_search_country()) ? GetCtrTitle(osc_search_country()) : osc_search_country();
      $sort_order[] = osc_get_preference('allSeo_search_country_order','plugin-all_in_one');
    }
  }
  
  if(osc_get_preference('allSeo_search_title_show','plugin-all_in_one') == 1){
    if( osc_get_preference('allSeo_search_page_title', 'plugin-all_in_one') == '') {
      $elements[] = osc_page_title();
    } else {
      $elements[] = osc_get_preference('allSeo_search_page_title', 'plugin-all_in_one');
    }

    $sort_order[] = osc_get_preference('allSeo_search_title_order','plugin-all_in_one');
  }
  
  if($cat_name_seo <> '' or $cat_name <> '') {
    if($cat_name_seo <> '') {
      $elements[] = $cat_name_seo;
    } else {
      $elements[] = $cat_name;
    }

    $sort_order[] = osc_get_preference('allSeo_search_category_order','plugin-all_in_one');
  }
  
  if(osc_search_pattern() <> '') {
    $elements[] = osc_search_pattern();
    $sort_order[] = osc_get_preference('allSeo_search_pattern_order','plugin-all_in_one');
  }
    
  array_multisort($sort_order, $elements);
  $elements = array_filter( $elements );
  return implode($del, $elements);
}

function CurrentMetaOrderListing() {
  $sort_order = array();
  $elements = array();
  $del = Delimiter();
  if(osc_get_preference('allSeo_city_show','plugin-all_in_one') == 1){
    $elements[] = __('City', 'all_in_one');
    $sort_order[] = osc_get_preference('allSeo_city_order','plugin-all_in_one');
  }
  
  if(osc_get_preference('allSeo_region_show','plugin-all_in_one') == 1){
    $elements[] = __('Region', 'all_in_one');
    $sort_order[] = osc_get_preference('allSeo_region_order','plugin-all_in_one');
  }
  
  if(osc_get_preference('allSeo_country_show','plugin-all_in_one') == 1){
    $elements[] = __('Country', 'all_in_one');
    $sort_order[] = osc_get_preference('allSeo_country_order','plugin-all_in_one');
  }

  if(osc_get_preference('allSeo_title_show','plugin-all_in_one') == 1){
    $elements[] = __('Site Title', 'all_in_one');
    $sort_order[] = osc_get_preference('allSeo_title_order','plugin-all_in_one');
  }

  if(osc_get_preference('allSeo_category_show','plugin-all_in_one') == 1){
    $elements[] = __('Category', 'all_in_one');
    $sort_order[] = osc_get_preference('allSeo_category_order','plugin-all_in_one');
  }

  $elements[] = __('Item Title', 'all_in_one');;
  $sort_order[] = osc_get_preference('allSeo_body_order','plugin-all_in_one');
  
  array_multisort($sort_order, $elements);
  $elements = array_filter( $elements );

  return '<div class="meta-elem round2 first">' . __('Tags order', 'all_in_one') . '</div><div class="meta-elem round2">'. implode('</div><div class="meta-delim">' . $del . '</div><div class="meta-elem round2">', $elements) . '</div>';
}

function CurrentMetaOrderCategory() {
  $sort_order = array();
  $elements = array();
  $del = Delimiter();
  
  if(osc_get_preference('allSeo_search_city_show','plugin-all_in_one') == 1){
    $elements[] = __('City', 'all_in_one');
    $sort_order[] = osc_get_preference('allSeo_search_city_order','plugin-all_in_one');
  }
  
  if(osc_get_preference('allSeo_search_region_show','plugin-all_in_one') == 1){
    $elements[] = __('Region', 'all_in_one');
    $sort_order[] = osc_get_preference('allSeo_search_region_order','plugin-all_in_one');
  }

  if(osc_get_preference('allSeo_search_country_show','plugin-all_in_one') == 1){
    $elements[] = __('Country', 'all_in_one');
    $sort_order[] = osc_get_preference('allSeo_search_country_order','plugin-all_in_one');
  }

  if(osc_get_preference('allSeo_search_title_show','plugin-all_in_one') == 1){
    $elements[] = __('Site Title', 'all_in_one');
    $sort_order[] = osc_get_preference('allSeo_search_title_order','plugin-all_in_one');
  }

  $elements[] = __('Category', 'all_in_one');
  $sort_order[] = osc_get_preference('allSeo_search_category_order','plugin-all_in_one');

  $elements[] = __('Search Pattern', 'all_in_one');
  $sort_order[] = osc_get_preference('allSeo_search_pattern_order','plugin-all_in_one');
 
  array_multisort($sort_order, $elements);
  $elements = array_filter( $elements );

  return '<div class="meta-elem round2 first">' . __('Tags order', 'all_in_one') . '</div><div class="meta-elem round2">'. implode('</div><div class="meta-delim">' . $del . '</div><div class="meta-elem round2">', $elements) . '</div>';
}


/* ----------------- Details for Item Meta -------------------- */
function GetItemKeywords(){
  $detail = ModelSeo::newInstance()->getAttrByItemId(osc_item_id()); 
  return isset($detail['seo_keywords']) ? $detail['seo_keywords'] : false;
}

function GetItemDesc(){
  $detail = ModelSeo::newInstance()->getAttrByItemId(osc_item_id()); 
  return isset($detail['seo_desc']) ? $detail['seo_desc'] : false;
}

function GetItemTitle(){
  $detail = ModelSeo::newInstance()->getAttrByItemId(osc_item_id()); 
  return isset($detail['seo_title']) ? $detail['seo_title'] : false;
}

/* ----------------- Details for Country Meta -------------------- */
function GetCtrTitle( $country_name ){
  if($country_name <> '') {
    if(strlen($country_name) == 2) {
      $ctr_full = Country::newInstance()->findByPrimaryKey($country_name);
    } else {
      $ctr_full = Country::newInstance()->findByName($country_name);
    }

    $detail = ModelSeoLocation::newInstance()->getAttrByCountryCode( $ctr_full['pk_c_code'] ); 
  }

  return isset($detail['seo_title']) ? $detail['seo_title'] : false;
}

function GetCtrDesc( $country_name ){
  if($country_name <> '') {
    if(strlen($country_name) == 2) {
      $ctr_full = Country::newInstance()->findByPrimaryKey($country_name);
    } else {
      $ctr_full = Country::newInstance()->findByName($country_name);
    }

    $detail = ModelSeoLocation::newInstance()->getAttrByCountryCode( $ctr_full['pk_c_code'] ); 
  }
 
  $final = $detail['seo_desc'] <> '' ? $detail['seo_desc'] : $ctr_full['s_name'];
  return $final ? $final : false;
}

function GetCtrKeywords( $country_name ){
  if($country_name <> '') {
    if(strlen($country_name) == 2) {
      $ctr_full = Country::newInstance()->findByPrimaryKey($country_name);
    } else {
      $ctr_full = Country::newInstance()->findByName($country_name);
    }

    $detail = ModelSeoLocation::newInstance()->getAttrByCountryCode( $ctr_full['pk_c_code'] ); 
  }
  return isset($detail['seo_keywords']) ? $detail['seo_keywords'] : false;
}


/* ----------------- Details for Region Meta -------------------- */
function GetRegTitle( $region_name ){
  if($region_name <> '') {
    $reg_full = Region::newInstance()->findByName($region_name);
    $detail = ModelSeoLocation::newInstance()->getAttrByRegionId( $reg_full['pk_i_id']); 
  }

  return isset($detail['seo_title']) ? $detail['seo_title'] : false;
}

function GetRegDesc( $region_name ){
  if($region_name <> '') {
    $reg_full = Region::newInstance()->findByName($region_name);
    $detail = ModelSeoLocation::newInstance()->getAttrByRegionId( $reg_full['pk_i_id']); 
  }

  $final = $detail['seo_desc'] <> '' ? $detail['seo_desc'] : $reg_full['s_name'];
  return $final ? $final : false;
}

function GetRegKeywords( $region_name ){
  if($region_name <> '') {
    $reg_full = Region::newInstance()->findByName($region_name);
    $detail = ModelSeoLocation::newInstance()->getAttrByRegionId( $reg_full['pk_i_id']); 
  }

  return isset($detail['seo_keywords']) ? $detail['seo_keywords'] : false;
}


/* ----------------- Details for Category Meta -------------------- */
function GetCatTitle( $cat_id ){
  $detail = ModelSeoCategory::newInstance()->getAttrByCategoryId( $cat_id ); 
  return isset($detail['seo_title']) ? $detail['seo_title'] : false;
}

function GetCatDesc( $cat_id ){
  $detail = ModelSeoCategory::newInstance()->getAttrByCategoryId( $cat_id ); 
  $cat = Category::newInstance()->findByPrimaryKey( $cat_id );
  $final = $detail['seo_desc'] <> '' ? $detail['seo_desc'] : $cat['s_description'];
  return $final ? $final : false;
}

function GetCatKeywords( $cat_id ){
  $detail = ModelSeoCategory::newInstance()->getAttrByCategoryId( $cat_id ); 
  return isset($detail['seo_keywords']) ? $detail['seo_keywords'] : false;
}


/* ----------------- Details for Static Page Meta -------------------- */
function GetPageKeywords(){
  $detail = ModelSeoPage::newInstance()->getAttrByPageId(osc_static_page_id()); 
  return isset($detail['seo_keywords']) ? $detail['seo_keywords'] : false;
}

function GetPageDesc(){
  $detail = ModelSeoPage::newInstance()->getAttrByPageId(osc_static_page_id()); 
  return isset($detail['seo_desc']) ? $detail['seo_desc'] : false;
}

function GetPageTitle(){
  $detail = ModelSeoPage::newInstance()->getAttrByPageId(osc_static_page_id()); 
  return isset($detail['seo_title']) ? $detail['seo_title'] : false;
}


// META TITLE GENERATION
function allSeo_title_filter($text) {
  $location = Rewrite::newInstance()->get_location();
  $section  = Rewrite::newInstance()->get_section();

  switch ($location) {
    // Listing page and page related to listings
    case ('item'):
      switch ($section) {
        case 'item_add':    $text = __('Publish a listing', 'all_in_one'); break;
        case 'item_edit':   $text = __('Edit your listing', 'all_in_one'); break;
        case 'send_friend': $text = __('Send to a friend', 'all_in_one') . Delimiter() . osc_item_title(); break;
        case 'contact':     $text = __('Contact seller', 'all_in_one') . Delimiter() . osc_item_title(); break;
        default:            $text = SeoGenerateTitleListing(); break;
      }
    break;
    
    // Static page
    case('page'):
      if( GetPageTitle() == '' ) {
        $text = osc_static_page_title();
      } else {
        $text = GetPageTitle();
      }
    break;
    
    // Error page
    case('error'):
      $text = __('Page not found', 'all_in_one');
    break;
    
    // Search & Category page
    case('search'):
      $region   = osc_search_region();
      $city     = osc_search_city();
      $pattern  = osc_search_pattern();
      $category = osc_search_category_id();
      $s_page   = '';
      $i_page   = Params::getParam('iPage');

      if($i_page != '' && $i_page > 1) {
        $s_page = Delimiter() . __('page', 'all_in_one') . ' ' . $i_page ;
      }

      $result = SeoGenerateTitleCategory();
      if($result == '') { $result = __('Search result', 'all_in_one'); }

      $text = $result . $s_page;
    break;
    
    // Login page
    case('login'):
      switch ($section) {
        case('recover'): $text = __('Recover your password', 'all_in_one');
        default:         $text = __('Login into your account', 'all_in_one');
      }
    break;
    
    // Registration page
    case('register'):
      $text = __('Create a new account', 'all_in_one');
    break;
    
    // User page and pages related to user
    case('user'):
      switch ($section) {
        case('dashboard'):       $text = __('Dashboard', 'all_in_one'); break;
        case('items'):           $text = __('Manage my listings', 'all_in_one'); break;
        case('alerts'):          $text = __('Manage my alerts', 'all_in_one'); break;
        case('profile'):         $text = __('Update my profile', 'all_in_one'); break;
        case('pub_profile'):     $text = __('Public profile of', 'all_in_one') . ' ' . ucfirst(osc_user_name()); break;
        case('change_email'):    $text = __('Change my email', 'all_in_one'); break;
        case('change_password'): $text = __('Change my password', 'all_in_one'); break;
        case('forgot'):          $text = __('Recover my password', 'all_in_one'); break;
      }
    break;
    
    // Contact page
    case('contact'):
      $text = __('Contact','all_in_one');
    break;
    
    default:
      $text = osc_page_title();
    break;
  }

  // Now add page title to first or last position for other pages
  if( !osc_is_home_page() and !osc_is_ad_page() and !osc_is_search_page() ) {
    $title = osc_get_preference('allSeo_other_page_title','plugin-all_in_one') <> '' ? osc_get_preference('allSeo_other_page_title','plugin-all_in_one') : osc_page_title();

    if(osc_get_preference('allSeo_title_first','plugin-all_in_one') == 1) {
      $text = $title . Delimiter() . $text;
    } else {
      $text .= Delimiter() . $title;
    }
  }

  return $text;
}

// META DESCRIPTION GENERATION
function allSeo_description_filter($text) {
  // CLEAN EXISTING DESCRIPTION
  $text = '';

  // HOME PAGE
  if( osc_is_home_page() ) {
    $text = osc_page_description();
  }

  // STATIC PAGE
  if( osc_is_static_page() ) {
    if ( GetPageDesc() == '' ) {
      $text = osc_highlight(osc_static_page_text(), 140, '', '');
    } else {
      $text = GetPageDesc();
    }
  }

  // SEARCH & CATEGORY PAGE
  if( osc_is_search_page() ) {
    $cat = osc_search_category_id();
    $cat_id = $cat[0];
    $cat_field = Category::newInstance()->findByPrimaryKey($cat_id); 
    $country = GetCtrDesc(osc_search_country()) ? GetCtrDesc(osc_search_country()) : osc_search_country();
    $region = GetRegDesc(osc_search_region()) ? GetRegDesc(osc_search_region()) : osc_search_region();
    $city = osc_search_city();

    if( GetCatDesc( $cat_id ) <> '' ) {
      $desc = GetCatDesc( $cat_id );
    } else {
      $desc = $cat_field['s_name'];
    }

    $text = $desc;

    // ADD LOCATION DESCRIPTION
    if($country <> '') {
      $text .= ($text <> '' ? ' - ' : '') .  $country; 
    }

    if($region <> '') {
      $text .= ($text <> '' ? ' - ' : '') . $region; 
    }

    if($city <> '') {
      $text .= ($text <> '' ? ' - ' : '') . $city; 
    }

    // Improve search/category title adding part of listings in this category/search
    if(osc_get_preference('allSeo_search_improve_desc', 'plugin-all_in_one') == 1) {
      osc_reset_custom_items();
      osc_query_item(array("category" => $cat_id, "country_name" => $country, "region_name" => $region, "city_name" => $city));
    
      while( osc_has_custom_items() ) {
        $item = Item::newInstance()->findByPrimaryKey(osc_item_id());
        $con = strip_tags($item['s_description']);

        if(osc_item_city() <> '') {
          $text .= ' - ' . osc_item_city();
        }
      
        if($con <> '') {
          $text .= ', ' . osc_highlight($con, 100);
        }
      }
    }

    $text = osc_highlight($text, 500);
    osc_reset_items();
  }

  // Listing page
  if( osc_is_ad_page() ) {
    if ( GetItemDesc() == '' ) {
      if( GetCatDesc( osc_item_category_id() ) <> '' ) {
        $desc = GetCatDesc( osc_item_category_id() );
      } else {
        $desc = osc_item_category();
      }

      $item = Item::newInstance()->findByPrimaryKey(osc_item_id());

      $text = $desc .  ' - ' . osc_highlight($item['s_description'], 120) . ', ' . SeoLocationShow();
    } else {
      $text = GetItemDesc();
    }
  }
  return $text;
}

// META KEYWORDS GENERATION
function allSeo_keywords_filter($text) {
  // Listing page
  if( osc_is_ad_page() and GetItemKeywords() <> '' ) {
    $text = GetItemKeywords();
  }

  // Static page
  if( osc_is_static_page() and GetPageKeywords() <> '') {
    $text = GetPageKeywords();
  }

  // Search & Category page
  if( osc_is_search_page() ) {
    $category = osc_search_category_id();
    if(isset($category[0]) && GetCatKeywords($category[0]) <> '' ) {
      $text = GetCatKeywords( $category[0] );
    }

    $text .= GetCtrKeywords(osc_search_country()) ? ', ' . GetCtrKeywords(osc_search_country()) : '';
    $text .= GetRegKeywords(osc_search_region()) ? ', ' . GetRegKeywords(osc_search_region()) : '';
  }
  
  if(osc_get_preference('allSeo_keywords','plugin-all_in_one') <> '') {
    if( $text <> '' ) { 
      $text .= ', ';
    }
    $text .= osc_get_preference('allSeo_keywords','plugin-all_in_one');
  }

  return $text;
}

function allSeoTitle($title){
  $file = explode('/', Params::getParam('file'));
  if($file[0] == 'all_in_one'){
    $title = '<i class="fa fa-line-chart"></i>&nbsp;All in One SEO Plugin';         
  }
  return $title;
}

osc_add_filter('custom_plugin_title','allSeoTitle');

// This is needed in order to be able to activate the plugin
osc_register_plugin(osc_plugin_path(__FILE__), 'allSeo_call_after_install') ;

// This is a hack to show a Uninstall link at plugins table (you could also use some other hook to show a custom option panel)
osc_add_hook(osc_plugin_path(__FILE__) . '_uninstall', 'allSeo_call_after_uninstall') ;

// This will add own meta
osc_add_filter('meta_title_filter', 'allSeo_title_filter');
osc_add_filter('meta_description_filter', 'allSeo_description_filter');

// This will add own keywords
osc_add_filter('meta_keywords_filter', 'allSeo_keywords_filter');

// Display help
function allSeo_conf() {
  osc_admin_render_plugin( osc_plugin_path( dirname(__FILE__) ) . '/admin/listings.php' ) ;
}

// This is a hack to show a Configure link at plugins table (you could also use some other hook to show a custom option panel)
osc_add_hook( osc_plugin_path( __FILE__ ) . '_configure', 'allSeo_conf' ) ;  

// Add font awesome style to header
function seo_font_awesome() {
  echo '<link href="http://maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet" type="text/css" />';
}

osc_add_hook('admin_header', 'seo_font_awesome');

function config_menu() {
  echo '<link href="' . osc_base_url() . 'oc-content/plugins/all_in_one/css/style.css" rel="stylesheet" type="text/css" />';
  //echo '<link href="http://maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet" type="text/css" />';
  echo '<script src="' . osc_base_url() . 'oc-content/plugins/all_in_one/js/js_admin.js"></script>';

  $text  = '';
  $text  = '<fieldset class="round5" style="padding: 0px 10px;">';
  $text .= '<ul class="conf-menu">';
  $text .= '<li class="gray round3">' . __('All in One SEO Plugin', 'all_in_one') . '</li>';
  $text .= '<li class="blue round3"><a href="' . osc_base_url() . 'oc-admin/index.php?page=plugins&action=renderplugin&file=all_in_one/admin/listings.php">' . __('listings', 'all_in_one') . '</a></li>';
  $text .= '<li class="azure round3"><a href="' . osc_base_url() . 'oc-admin/index.php?page=plugins&action=renderplugin&file=all_in_one/admin/categories.php">' . __('categories', 'all_in_one') . '</a></li>';
  $text .= '<li class="red round3"><a href="' . osc_base_url() . 'oc-admin/index.php?page=plugins&action=renderplugin&file=all_in_one/admin/locations.php">' . __('locations', 'all_in_one') . '</a></li>';
  $text .= '<li class="yellow round3"><a href="' . osc_base_url() . 'oc-admin/index.php?page=plugins&action=renderplugin&file=all_in_one/admin/pages.php">' . __('static pages', 'all_in_one') . '</a></li>';
  $text .= '<li class="orange round3"><a href="' . osc_base_url() . 'oc-admin/index.php?page=plugins&action=renderplugin&file=all_in_one/admin/links.php">' . __('back links', 'all_in_one') . '</a></li>';
  $text .= '<li class="purple round3"><a href="' . osc_base_url() . 'oc-admin/index.php?page=plugins&action=renderplugin&file=all_in_one/admin/generate.php">' . __('sitemap', 'all_in_one') . '</a></li>';
  $text .= '<li class="green round3"><a href="' . osc_base_url() . 'oc-admin/index.php?page=plugins&action=renderplugin&file=all_in_one/admin/htaccess.php">' . __('htaccess', 'all_in_one') . '</a></li>';
  $text .= '<li class="red round3"><a href="' . osc_base_url() . 'oc-admin/index.php?page=plugins&action=renderplugin&file=all_in_one/admin/robots.php">' . __('robots.txt', 'all_in_one') . '</a></li>';
  $text .= '<li class="dark-red round3"><a href="' . osc_base_url() . 'oc-admin/index.php?page=plugins&action=renderplugin&file=all_in_one/admin/stats.php">' . __('stats', 'all_in_one') . '</a></li>';
  $text .= '</ul>';
  $text .= '</fieldset>';
  $text .= '<br /><br />';
  $text .= '<div style="clear:both"></div>';
  return $text;
}


/* ---------------------------  Start for item meta --------------------------------------------- */
function seo_form($catId = '') {
  // We received the categoryID
  $allow_custom_meta           = '';
  $dao_preference = new Preference();
  $allow_custom_meta = osc_get_preference('allSeo_allow_custom_meta', 'plugin-all_in_one');
  if($catId!="" and ($allow_custom_meta == 1 or osc_is_admin_user_logged_in())) {
    include_once 'item_edit.php';
  } else if (($allow_custom_meta == 2 and osc_is_web_user_logged_in()) or osc_is_admin_user_logged_in()) {
    include_once 'item_edit.php';
  }
}

function seo_form_post($item) {
  // We received the categoryID and the Item ID
  if($item['fk_i_category_id']!=null) {
    // Insert the data in our plugin's table
    ModelSeo::newInstance()->insertAttr( $item['pk_i_id'], Params::getParam('seo_title'), Params::getParam('seo_desc'), Params::getParam('seo_keywords') );
  }
}

// Self-explanatory
function seo_item_edit($catId = null, $item_id = null) {
  $allow_custom_meta = osc_get_preference('allSeo_allow_custom_meta', 'plugin-all_in_one');
  if($allow_custom_meta == 1 or osc_is_admin_user_logged_in()) {
    include_once 'item_edit.php';
  } else if (($allow_custom_meta == 2 and osc_is_web_user_logged_in()) or osc_is_admin_user_logged_in()) {
    include_once 'item_edit.php';
  }
}

function seo_item_edit_post($item) {
  if($item['fk_i_category_id']!=null) {
    // We will check if Meta for this item exists or not
    $detail = ModelSeo::newInstance()->getAttrByItemId( $item['pk_i_id'] );
    if(isset($detail['seo_item_id'])) {
      ModelSeo::newInstance()->updateAttr( $item['pk_i_id'], Params::getParam('seo_title'), Params::getParam('seo_desc'), Params::getParam('seo_keywords') );
    } else {
      ModelSeo::newInstance()->insertAttr( $item['pk_i_id'], Params::getParam('seo_title'), Params::getParam('seo_desc'), Params::getParam('seo_keywords') );
    } 
  }
}

function seo_delete_item($item_id) {
  ModelSeo::newInstance()->deleteItem($item_id) ;
}

function seo_pre_item_post() {
  Session::newInstance()->_setForm('pp_seo_title' , Params::getParam('seo_title'));
  Session::newInstance()->_setForm('pp_seo_desc' , Params::getParam('seo_desc'));
  Session::newInstance()->_setForm('pp_seo_keywords' , Params::getParam('seo_keywords'));
  
  // keep values on session
  Session::newInstance()->_keepForm('pp_seo_title');
  Session::newInstance()->_keepForm('pp_seo_desc');
  Session::newInstance()->_keepForm('pp_seo_keywords');
}


// When publishing an item we show an extra form with more attributes
osc_add_hook('item_form', 'seo_form');

// To add that new information to our custom table
osc_add_hook('posted_item', 'seo_form_post');

// Edit an item special attributes
osc_add_hook('item_edit', 'seo_item_edit');

// Edit an item special attributes POST
osc_add_hook('edited_item', 'seo_item_edit_post');

//Delete item
osc_add_hook('delete_item', 'seo_delete_item');

// previous to insert item
osc_add_hook('pre_item_post', 'seo_pre_item_post') ;
/* -------------------------------- End item mete -------------------------------- */


// Add menu link to Plugin list
function seo_admin_menu() {
echo '<h3><a href="#" style="font-weight:bold">All in One SEO</a></h3>
<ul> 
  <li><a style="color:blue;font-weight:bold" href="' . osc_admin_render_plugin_url(osc_plugin_path(dirname(__FILE__)) . '/admin/listings.php') . '"><i class="fa fa-angle-right"></i> ' . __('listings', 'all_in_one') . '</a></li>
  <li><a style="color:DarkTurquoise;font-weight:bold" href="' . osc_admin_render_plugin_url(osc_plugin_path(dirname(__FILE__)) . '/admin/categories.php') . '"><i class="fa fa-angle-right"></i> ' . __('categories', 'all_in_one') . '</a></li>
  <li><a style="color:Dred;font-weight:bold" href="' . osc_admin_render_plugin_url(osc_plugin_path(dirname(__FILE__)) . '/admin/locations.php') . '"><i class="fa fa-angle-right"></i> ' . __('locations', 'all_in_one') . '</a></li>
  <li><a style="color:goldenrod;font-weight:bold" href="' . osc_admin_render_plugin_url(osc_plugin_path(dirname(__FILE__)) . '/admin/pages.php') . '"><i class="fa fa-angle-right"></i> ' . __('static pages', 'all_in_one') . '</a></li>
  <li><a style="color:#D86B00;font-weight:bold" href="' . osc_admin_render_plugin_url(osc_plugin_path(dirname(__FILE__)) . '/admin/links.php') . '"><i class="fa fa-angle-right"></i> ' . __('back links', 'all_in_one') . '</a></li>
  <li><a style="color:purple;font-weight:bold" href="' . osc_admin_render_plugin_url(osc_plugin_path(dirname(__FILE__)) . '/admin/generate.php') . '"><i class="fa fa-angle-right"></i> ' . __('sitemap', 'all_in_one') . '</a></li>
  <li><a style="color:green;font-weight:bold" href="' . osc_admin_render_plugin_url(osc_plugin_path(dirname(__FILE__)) . '/admin/htaccess.php') . '"><i class="fa fa-angle-right"></i> ' . __('htaccess', 'all_in_one') . '</a></li>
  <li><a style="color:red;font-weight:bold" href="' . osc_admin_render_plugin_url(osc_plugin_path(dirname(__FILE__)) . '/admin/robots.php') . '"><i class="fa fa-angle-right"></i> ' . __('robots.txt', 'all_in_one') . '</a></li>
  <li><a style="color:DarkRed;font-weight:bold" href="' . osc_admin_render_plugin_url(osc_plugin_path(dirname(__FILE__)) . '/admin/stats.php') . '"><i class="fa fa-angle-right"></i> ' . __('stats', 'all_in_one') . '</a></li>
</ul>';
}

//Adding sub menu to plugins menu in dashboard
osc_add_hook('admin_menu','seo_admin_menu', '1');


/* -------------------------------- Links management there -------------------------------- */
// Get all links
function osc_has_links_seo() {
  $links = ModelSeoLink::newInstance()->getAllLinks();
  return isset($links) ? $links : '';
}

function osc_has_links_rec_seo() {
  $links = ModelSeoLink::newInstance()->getAllRecLinks();
  return isset($links) ? $links : '';
}


function generate_link( $title, $href, $rel ) {
  if( $rel == 1 ) { $follow = ' rel="nofollow" '; } else { $follow = ' '; }
  $text = '<a href="' . $href . '" title = "' . $title . '" target="_blank"' . $follow . '>' . $title . '</a>';
  return isset($text) ? $text : '';
}

function SeoFooterLinks() {
  $text = '';
  foreach( osc_has_links_seo() as $links ) {
    if( $links['seo_footer'] == 1 ) {
      if( $text <> '' ) { $text .= ' | '; }
      $text .= generate_link( $links['seo_title'], $links['seo_href'], $links['seo_rel'] );
    }
  }
  
  echo '<div id="footer-links" style="float:left;width:100%;clear:both;">' . $text . '</div>';
}

if(strpos($myPlugin[2],'All') == false && !osc_is_admin_user_logged_in()) {header('Location:'.osc_base_url());}

// Add links to footer if enabled
if(osc_get_preference('allSeo_links_footer','plugin-all_in_one') == 1) {
  osc_add_hook('footer', 'SeoFooterLinks');
}
?>