<?php

use Timber\Site;
use Timber\Timber;

Timber::init();

// Sets the directories (inside your theme) to find .twig files.
Timber::$dirname = ['template-parts', 'views'];


/**
 * Class StarterSite
 */
class StarterSite extends Site
{
  public function __construct()
  {
    add_action('after_setup_theme', array($this, 'theme_supports'));
    add_action('init', array($this, 'register_post_types'));
    add_action('init', array($this, 'register_taxonomies'));

    add_filter('timber/context', array($this, 'add_to_context'));
    add_filter('timber/twig', array($this, 'add_to_twig'));
    add_filter('timber/twig/environment/options', [$this, 'update_twig_environment_options']);

    parent::__construct();
  }

  /**
   * This is where you can register custom post types.
   */
  public function register_post_types() {}

  /**
   * This is where you can register custom taxonomies.
   */
  public function register_taxonomies() {}

  /**
   * This is where you add some context
   *
   * @param string $context context['this'] Being the Twig's {{ this }}.
   */
  public function add_to_context($context)
  {
    $context['menu']  = Timber::get_menu('main_menu');
    $context['footer_menu_1']  = Timber::get_menu('footer_menu_1');
    $context['footer_menu_2']  = Timber::get_menu('footer_menu_2');
    $context['footer_menu_3']  = Timber::get_menu('footer_menu_3');
    $context['footer_menu_4']  = Timber::get_menu('footer_menu_4');
    $context['footer_menu_5']  = Timber::get_menu('footer_menu_5');
    $context['footer_menu_bottom']  = Timber::get_menu('footer_menu_bottom');
    $context['lang_switcher']  = Timber::get_menu('lang_switcher');
    $context['latest_posts'] = Timber::get_posts([
      'post_type' => 'post',
      'posts_per_page' => 6
    ]);
    $context['site']  = $this;
    $context['header_button_link'] = get_field('header_button_link', 'option');
    $context['in_url'] = get_field('in_url', 'option');
    $context['copyright'] = get_field('copyright', 'option');
    $context['privacy_policy_page'] = get_field('privacy_policy_page', 'option');

    // Get the current post
    $current_post = Timber::get_post();
    $context['post'] = $current_post;
    // Get parent if it's a page
    if ($current_post && $current_post->parent()) {
      $context['parent'] = $current_post->parent();
    }

    // Fetch related posts by category
    if ($current_post && !empty($current_post->terms('category'))) {
      $category_ids = array_map(function ($term) {
        return $term->term_id;
      }, $current_post->terms('category'));

      $context['related_posts'] = Timber::get_posts([
        'post_type' => 'post',
        'posts_per_page' => 3,
        'post__not_in' => [$current_post->ID],
        'category__in' => $category_ids,
      ]);
    }


    // Check if WPML is active
    if (function_exists('apply_filters')) {
      $languages = apply_filters('wpml_active_languages', null, ['skip_missing' => 0, 'orderby' => 'custom']);

      if (!empty($languages)) {
        $context['languages'] = $languages;
      }

      $current_language = apply_filters('wpml_current_language', null);
      if ($current_language) {
        $context['current_language'] = $current_language;

        // Filter out the current language from the list of languages
        $context['non_active_languages'] = array_filter($languages, function ($language) use ($current_language) {
          return $language['code'] !== $current_language;
        });

        if (count($languages) > 1) {
          $context['show_lang_switcher'] = true;
        } else {
          $context['show_lang_switcher'] = false;
        }
      } else {
        $context['show_lang_switcher'] = false;
      }
    } else {
      $context['show_lang_switcher'] = false;
    }

    $image_id = get_field('image');
    if ($image_id) {
      $image = new \Timber\Image($image_id);

      // Manually generate srcset
      $sizes = wp_get_attachment_image_srcset($image_id);

      $context['image'] = [
        'timber_image' => $image,
        'srcset' => $sizes ?: '',
        'src' => wp_get_attachment_image_src($image_id, 'full')[0],
        'alt' => get_post_meta($image_id, '_wp_attachment_image_alt', true),
      ];
    } else {
      $context['image'] = false;
    }

    // Get the fields
    $fields = get_fields();
    
    // Convert entries to Timber Posts if they exist
    if (!empty($fields['entries'])) {
        $fields['entries'] = array_map(function($post) {
            return new \Timber\Post($post);
        }, $fields['entries']);
    }
    
    $context['fields'] = $fields;

    return $context;
  }

  public function theme_supports()
  {
    // Add default posts and comments RSS feed links to head.
    add_theme_support('automatic-feed-links');

    /*
		 * Let WordPress manage the document title.
		 * By adding theme support, we declare that this theme does not use a
		 * hard-coded <title> tag in the document head, and expect WordPress to
		 * provide it for us.
		 */
    add_theme_support('title-tag');

    /*
		 * Enable support for Post Thumbnails on posts and pages.
		 *
		 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		 */
    add_theme_support('post-thumbnails');

    /*
		 * Switch default core markup for search form, comment form, and comments
		 * to output valid HTML5.
		 */
    add_theme_support(
      'html5',
      array(
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
      )
    );

    /*
		 * Enable support for Post Formats.
		 *
		 * See: https://codex.wordpress.org/Post_Formats
		 */
    add_theme_support(
      'post-formats',
      array(
        'aside',
        'image',
        'video',
        'quote',
        'link',
        'gallery',
        'audio',
      )
    );

    add_theme_support('menus');
  }

  /**
   * his would return 'foo bar!'.
   *
   * @param string $text being 'foo', then returned 'foo bar!'.
   */
  public function myfoo($text)
  {
    $text .= ' bar!';
    return $text;
  }

  /**
   * This is where you can add your own functions to twig.
   *
   * @param Twig\Environment $twig get extension.
   */
  public function add_to_twig($twig)
  {
    /**
     * Add has_excerpt as a Twig function
     */
    $twig->addFunction(new \Twig\TwigFunction('has_excerpt', function ($post_id) {
      return has_excerpt($post_id);
    }));

    $twig->addFilter(new Twig\TwigFilter('myfoo', [$this, 'myfoo']));

    return $twig;
  }

  /**
   * Updates Twig environment options.
   *
   * @link https://twig.symfony.com/doc/2.x/api.html#environment-options
   *
   * \@param array $options An array of environment options.
   *
   * @return array
   */
  function update_twig_environment_options($options)
  {
    // $options['autoescape'] = true;

    return $options;
  }
}

new StarterSite();
