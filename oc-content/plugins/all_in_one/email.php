<?php
//Add keywords to email legend
EmailVariables::newInstance()->add('{LINK_FROM}', 'URL of website used in backlink placed on external website (URL in backlink).');
EmailVariables::newInstance()->add('{LINK_TO}', 'URL where is placed backlink');

// Create email when backlink not found on external page
function email_link_problem($url_from, $url_to, $contact) {
  $page = new Page() ;
  $page = $page->findByInternalName('seo_link_problem');
  if(empty($page)) { exit(); }

  $locale = osc_current_user_locale() ;
  $content = array();
  if(isset($page['locale'][$locale]['s_title'])) {
    $content = $page['locale'][$locale];
  } else {
    $content = current($page['locale']);
  }

  $words   = array();
  $words[] = array('{LINK_FROM}', '{LINK_TO}');
  $words[] = array($url_from, $url_to);

  $title = osc_mailBeauty($content['s_title'], $words) ;
  $body  = osc_mailBeauty($content['s_text'], $words) ;

  $email_build = array(
    'subject'  => $title, 
    'to' => $contact, 
    'to_name'  => 'Partner',
    'body' => $body,
    'alt_body' => $body
  );

  osc_sendMail($email_build);
}
?>