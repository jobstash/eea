<?php

/*
 * Enable custom login style uncommenting the line below
 */
add_action("login_head", "my_login_head");

/*
 * Change login page style adding your custom css in $output
 */
function my_login_head()
{
  $logo = get_bloginfo('template_directory') . '/assets/img/logo.png';
  $output = "<style>";
  $output .= "#login > h1 {
      background-image:url($logo) !important;
      background-size: contain;
      background-position: top center;
      background-repeat: no-repeat;
      width: 100%;
      height: 50px;
      margin-bottom: 5px;
    }
    #login > h1 a {display:none; !important;}";
  $output .= "</style>";
  echo $output;
}


/*
 * Change title for login screen uncommenting the line below
 */
add_filter('login_headertext', function () {
  return get_bloginfo('site');
});

/*
 * Change url for login screen uncommenting the line below
 */
// add_filter('login_headerurl', function () {
//   return home_url('site');
// });
