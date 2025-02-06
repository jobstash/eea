<?php

// Remove xls file
function my_wpseo_stylesheet_url($_s)
{
  // Need to be empty
}
add_filter('wpseo_stylesheet_url', 'my_wpseo_stylesheet_url', 10, 2);


// Fix for ACF > 6
add_filter(
  'acf/pre_save_block',
  function ($attributes) {
    if (empty($attributes['id'])) {
      $attributes['id'] = 'block_' . uniqid();
    }
    return $attributes;
  }
);

// Workaround for https://github.com/wp-graphql/wp-graphql-acf/issues/167
// add_filter(
//   'acf/load_value',
//   function ($post_ids, $parent_id, $field) {
//     if (!function_exists('is_graphql_http_request')) {
//       return $post_ids;
//     }

//     if (!is_graphql_http_request()) {
//       return $post_ids;
//     }

//     // No need to filter this if the viever has the enough permissions
//     if (current_user_can('edit_posts')) {
//       return $post_ids;
//     }

//     $type = $field['type'] ?? null;

//     if ('post_object' === $type) {
//       $post = get_post($post_ids);
//       if ($post->post_status === 'publish') {
//         return $post_ids;
//       } else {
//         return null;
//       }
//     }

//     // Affects only the 'relationship' field types
//     if ('relationship' !== $type) {
//       return $post_ids;
//     }

//     if (!is_array($post_ids)) {
//       return $post_ids;
//     }

//     $public_ids = [];

//     foreach ($post_ids as $id) {
//       $post = get_post($id);
//       if ($post->post_status === 'publish') {
//         $public_ids[] = $post->ID;
//       }
//     }

//     return $public_ids;
//   },
//   10,
//   3
// );

// Function to change "posts" to "news" in the admin side menu
function change_post_menu_label()
{
  global $menu;
  global $submenu;
  $menu[5][0] = 'Insights';
  $submenu['edit.php'][5][0] = 'Insights';
  $submenu['edit.php'][10][0] = 'Add Insight';
  $submenu['edit.php'][16][0] = 'Tags';
  echo '';
}
// add_action('admin_menu', 'change_post_menu_label');

// Function to change post object labels to "news"
function change_post_object_label()
{
  global $wp_post_types;
  $labels = &$wp_post_types['post']->labels;
  $labels->name = 'Insights';
  $labels->singular_name = 'Insight';
  $labels->add_new = 'Add Insight';
  $labels->add_new_item = 'Add Insight';
  $labels->edit_item = 'Edit Insight';
  $labels->new_item = 'Insight';
  $labels->view_item = 'View Insight';
  $labels->search_items = 'Search Insights';
  $labels->not_found = 'No Insights found';
  $labels->not_found_in_trash = 'No Insights found in Trash';
}
// add_action('init', 'change_post_object_label');


/*
 * ACF options page
 */
if (function_exists('acf_add_options_page')) {
  acf_add_options_page(
    array(
      'page_title' => __('Options'),
      'menu_title' => __('Options'),
      'autoload' => true,
      'show_in_graphql' => true
    )
  );
}


/*
 * Preview link
 */
// add_filter('preview_post_link', 'the_preview_fix', 10, 2);

function the_preview_fix($preview_link, $post)
{
  $revision  = wp_get_post_revision($post);
  $post_type = $revision ? get_post_type($revision->post_parent) : $post->post_type;
  $id = $revision ? $revision->post_parent : $post->ID;
  $post_status = $post->post_status;
  $draft = ($post_status != 'publish' || $revision) ? '&is_draft=true' : '';
  return HEADLESS_URL . "/api/preview?secret=" . WORDPRESS_PREVIEW_SECRET . "$draft&id=$id&post_type=$post_type";
}

/*
 * Preview link inside Gutenberg editor [Hack Fix]
 */
// add_filter('rest_prepare_post', 'hack_fix_preview_link', 10, 2);
// add_filter('rest_prepare_page', 'hack_fix_preview_link', 10, 2);

function hack_fix_preview_link($response, $post)
{
  if ('draft' === $post->post_status) {
    $response->data['link'] = get_preview_post_link($post);
  }

  return $response;
}

/*
 * View page link
 */
function custom_frontend_url($permalink, $post)
{
  $custom_permalink = str_replace(home_url(), HEADLESS_URL,  $permalink);
  return $custom_permalink;
};

// add_filter('page_link', 'custom_frontend_url', 10, 2);
// add_filter('post_link', 'custom_frontend_url', 10, 2);
// // If you use custom post types also add this filter.
// add_filter('post_type_link', 'custom_frontend_url', 10, 2);

/*
 * Let WordPress manage the document title.
 * By adding theme support, we declare that this theme does not use a
 * hard-coded <title> tag in the document head, and expect WordPress to
 * provide it for us.
 */
add_theme_support('title-tag');


// Disable fullscren mode from Gutenberg
if (is_admin()) {
  function jba_disable_editor_fullscreen_by_default()
  {
    $script = "jQuery( window ).load(function() { const isFullscreenMode = wp.data.select( 'core/edit-post' ).isFeatureActive( 'fullscreenMode' ); if ( isFullscreenMode ) { wp.data.dispatch( 'core/edit-post' ).toggleFeature( 'fullscreenMode' ); } });";
    wp_add_inline_script('wp-blocks', $script);
  }
  add_action('enqueue_block_editor_assets', 'jba_disable_editor_fullscreen_by_default');
}

// Gutenberg
add_action('after_setup_theme', function () {
  add_theme_support('align-wide');
  add_theme_support('responsive-embeds');
});


/* Remove Inline CSS and Line Breaks in WordPress Galleries */
add_filter('the_content', 'remove_br_gallery', 11);
function remove_br_gallery($output)
{
  return preg_replace('/(<br[^>]*>\s*){2,}/', '<br />', $output);
}

add_filter('use_default_gallery_style', '__return_false');


// Modifica the_excerpt() mantenendo la formattazione
function improved_trim_excerpt($text)
{
  global $post;
  if ('' == $text) {
    $text = get_the_content('');
    $text = apply_filters('the_content', $text);
    $text = str_replace('\]\]\>', ']]&gt;', $text);
    $text = preg_replace('@<script[^>]*?>.*?</script>@si', '', $text);
    $text = strip_tags($text, '<p>');
    $excerpt_length = 20;
    $words = explode(' ', $text, $excerpt_length + 1);
    if (count($words) > $excerpt_length) {
      array_pop($words);
      array_push($words, '...');
      $text = implode(' ', $words);
    }
  }
  return $text;
}

remove_filter('get_the_excerpt', 'wp_trim_excerpt');
add_filter('get_the_excerpt', 'improved_trim_excerpt');


// API
// add_action('rest_api_init', function () {
//   $namespace = 'wp_starter/v1';
//   register_rest_route($namespace, '/posts/(?P<url>.*?)', array(
//     'methods'  => 'GET',
//     'callback' => 'get_post_for_url',
//     'permission_callback' => function () {
//       return '';
//     }
//   ));
// });

/**
 * This fixes the wordpress rest-api so we can just lookup pages by their full
 * path (not just their name). This allows us to use React Router.
 *
 * @return WP_Error|WP_REST_Response
 */
// function get_post_for_url($data)
// {
//   $postId    = url_to_postid($data['url']);
//   $postType  = get_post_type($postId);
//   $controller = new WP_REST_Posts_Controller($postType);
//   $request    = new WP_REST_Request('GET', "/wp/v2/{$postType}s/{$postId}");
//   $request->set_url_params(array('id' => $postId));
//   return $controller->get_item($request);
// }

/** Enable json upload */
function custom_upload_mimes($mimes)
{
  $mimes['json'] = 'text/plain';
  return $mimes;
}

add_filter('upload_mimes', 'custom_upload_mimes');


// Remove comments
add_action('admin_init', function () {
  // Redirect any user trying to access comments page
  global $pagenow;

  if ($pagenow === 'edit-comments.php') {
    wp_redirect(admin_url());
    exit;
  }

  // Remove comments metabox from dashboard
  remove_meta_box('dashboard_recent_comments', 'dashboard', 'normal');

  // Disable support for comments and trackbacks in post types
  foreach (get_post_types() as $post_type) {
    if (post_type_supports($post_type, 'comments')) {
      remove_post_type_support($post_type, 'comments');
      remove_post_type_support($post_type, 'trackbacks');
    }
  }
});

// Close comments on the front-end
add_filter('comments_open', '__return_false', 20, 2);
add_filter('pings_open', '__return_false', 20, 2);

// Hide existing comments
add_filter('comments_array', '__return_empty_array', 10, 2);

// Remove comments page in menu
add_action('admin_menu', function () {
  remove_menu_page('edit-comments.php');
});

// Remove comments links from admin bar
add_action('init', function () {
  if (is_admin_bar_showing()) {
    remove_action('admin_bar_menu', 'wp_admin_bar_comments_menu', 60);
  }
});



// Rename category
// function revcon_change_cat_label()
// {
//   global $submenu;
//   $submenu['edit.php'][15][0] = 'Industries'; // Rename categories to Industries
// }
// add_action('admin_menu', 'revcon_change_cat_label');

// function revcon_change_cat_object()
// {
//   global $wp_taxonomies;
//   $labels = &$wp_taxonomies['category']->labels;
//   $labels->name = 'Industry';
//   $labels->singular_name = 'Industry';
//   $labels->add_new = 'Add Industry';
//   $labels->add_new_item = 'Add Industry';
//   $labels->edit_item = 'Edit Industry';
//   $labels->new_item = 'Industry';
//   $labels->view_item = 'View Industry';
//   $labels->search_items = 'Search Industries';
//   $labels->not_found = 'No Industries found';
//   $labels->not_found_in_trash = 'No Industries found in Trash';
//   $labels->all_items = 'All Industries';
//   $labels->menu_name = 'Industry';
//   $labels->name_admin_bar = 'Industry';
// }
// add_action('init', 'revcon_change_cat_object');


/*
 * Replacing domain for rest api requests from Gutenberg editor if youre using
 * WP headless and WP_SITEURL & WP_HOME are not the same domain
 * (has nothing to do with yoast)
 */
// add_filter('rest_url', function ($url) {
//   $url = str_replace(home_url(), site_url(), $url);
//   return $url;
// });

/*
 * Replacing domain for stylesheet to xml if youre using WP headless 
 * and WP_SITEURL & WP_HOME are not the same domain
 */
// function filter_wpseo_stylesheet_url($stylesheet)
// {
//   $home = parse_url(get_option('home'));
//   $site = parse_url(get_option('siteurl'));
//   return str_replace($home, $site, $stylesheet);
// };
// add_filter('wpseo_stylesheet_url', 'filter_wpseo_stylesheet_url', 10, 1);

/*
 * Replacing domain for sitemap index if youre using WP headless 
 * and WP_SITEURL & WP_HOME are not the same domain
 */
// function filter_wpseo_sitemap_index_links($links)
// {
//   $home = parse_url(get_option('home'));
//   $site = parse_url(get_option('siteurl'));
//   foreach ($links as $i => $link)
//     $links[$i]['loc'] = str_replace($home, $site, $link['loc']);
//   return $links;
// };
// add_filter('wpseo_sitemap_index_links', 'filter_wpseo_sitemap_index_links', 10, 1);


// Wrap all core block in a div.core-block
function custom_render_block_core(
  string $block_content,
  array $block
): string {
  if (
    !is_admin() &&
    !wp_is_json_request()
  ) {
    $html = '<div class="core-block">' . "\n";
    // $html .= render_block($block);
    $html .= $block_content;

    $html .= '</div>' . "\n";

    return $html;
  }

  return $block_content;
}

add_filter('render_block', 'custom_render_block_core', null, 2);
