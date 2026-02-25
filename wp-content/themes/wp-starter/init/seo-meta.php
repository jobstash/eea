<?php
/**
 * EEA SEO Meta Tags
 * 
 * Outputs meta descriptions, Open Graph, and Twitter Card tags.
 * No plugin dependencies - just clean meta tags.
 * 
 * @package EEA Theme
 */

if (!defined('ABSPATH')) exit;

/**
 * Load SEO meta data from JSON file
 */
function eea_get_seo_data() {
    static $seo_data = null;
    
    if ($seo_data === null) {
        $json_file = get_template_directory() . '/init/eea-seo-meta.json';
        if (file_exists($json_file)) {
            $seo_data = json_decode(file_get_contents($json_file), true);
        } else {
            $seo_data = array();
        }
    }
    
    return $seo_data;
}

/**
 * Get meta for current page/post
 */
function eea_get_current_meta() {
    $seo_data = eea_get_seo_data();
    $meta = array(
        'title' => '',
        'description' => '',
        'og_image' => ''
    );
    
    if (is_front_page() || is_home()) {
        // Homepage
        if (isset($seo_data['homepage'])) {
            $meta = $seo_data['homepage'];
        }
    } elseif (is_singular('post')) {
        // Single post
        $slug = get_post_field('post_name', get_the_ID());
        if (isset($seo_data['posts'][$slug])) {
            $meta = $seo_data['posts'][$slug];
        } else {
            // Fallback: generate from post content
            $meta['title'] = get_the_title();
            $meta['description'] = eea_generate_description(get_the_excerpt());
            $meta['og_image'] = get_the_post_thumbnail_url(get_the_ID(), 'large');
        }
    } elseif (is_page()) {
        // Page
        $slug = get_post_field('post_name', get_the_ID());
        if (isset($seo_data['pages'][$slug])) {
            $meta = $seo_data['pages'][$slug];
        } else {
            // Fallback
            $meta['title'] = get_the_title();
            $meta['description'] = eea_generate_description(get_the_excerpt());
        }
    } elseif (is_archive()) {
        // Archive pages
        $meta['title'] = get_the_archive_title();
        $meta['description'] = get_the_archive_description() ?: 'Browse articles from the Enterprise Ethereum Alliance.';
    }
    
    return $meta;
}

/**
 * Generate description from text
 */
function eea_generate_description($text, $max_len = 155) {
    $text = wp_strip_all_tags($text);
    $text = preg_replace('/\s+/', ' ', $text);
    $text = trim($text);
    
    if (strlen($text) > $max_len) {
        $text = substr($text, 0, $max_len);
        $text = preg_replace('/\s+\S*$/', '', $text) . '...';
    }
    
    return $text;
}

/**
 * Output SEO meta tags in <head>
 */
function eea_output_seo_meta() {
    $meta = eea_get_current_meta();
    $site_name = get_bloginfo('name');
    $current_url = home_url(add_query_arg(array(), $GLOBALS['wp']->request));
    
    // Default OG image if none set
    $default_og_image = get_template_directory_uri() . '/assets/images/eea-og-default.png';
    $og_image = !empty($meta['og_image']) ? $meta['og_image'] : $default_og_image;
    
    // Meta description
    if (!empty($meta['description'])) {
        echo '<meta name="description" content="' . esc_attr($meta['description']) . '">' . "\n";
    }
    
    // Open Graph
    echo '<meta property="og:type" content="' . (is_singular('post') ? 'article' : 'website') . '">' . "\n";
    echo '<meta property="og:url" content="' . esc_url($current_url) . '">' . "\n";
    echo '<meta property="og:site_name" content="' . esc_attr($site_name) . '">' . "\n";
    
    if (!empty($meta['title'])) {
        echo '<meta property="og:title" content="' . esc_attr($meta['title']) . '">' . "\n";
    }
    
    if (!empty($meta['description'])) {
        echo '<meta property="og:description" content="' . esc_attr($meta['description']) . '">' . "\n";
    }
    
    if (!empty($og_image)) {
        echo '<meta property="og:image" content="' . esc_url($og_image) . '">' . "\n";
    }
    
    // Twitter Card
    echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
    
    if (!empty($meta['title'])) {
        echo '<meta name="twitter:title" content="' . esc_attr($meta['title']) . '">' . "\n";
    }
    
    if (!empty($meta['description'])) {
        echo '<meta name="twitter:description" content="' . esc_attr($meta['description']) . '">' . "\n";
    }
    
    if (!empty($og_image)) {
        echo '<meta name="twitter:image" content="' . esc_url($og_image) . '">' . "\n";
    }
}

// Don't output duplicate meta/OG if an SEO plugin (e.g. Rank Math) is active
function eea_should_output_seo_meta() {
	if (class_exists('RankMath')) {
		return false;
	}
	if (!function_exists('is_plugin_active')) {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
	}
	return !is_plugin_active('rank-math/rank-math.php');
}
if (eea_should_output_seo_meta()) {
	add_action('wp_head', 'eea_output_seo_meta', 1);
}
