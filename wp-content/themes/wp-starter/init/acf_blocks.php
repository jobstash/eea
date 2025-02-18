<?php

// Acf Blocks
$acf_blocks = array(
  array(
    'name'   => 'hero-home',
    'title'  => __('Hero Home'),
    'description'    => __('A block for Home page Hero.'),
    'keywords'      => array('hero', 'home')
  ),
  array(
    'name'   => 'divider',
    'title'  => __('Divider'),
    'description'    => __('Divider'),
    'keywords'      => array('Divider')
  ),
  array(
    'name'   => 'members-team',
    'title'  => __('Members/Team'),
    'description'    => __('Members/Team'),
    'keywords'      => array('Members/Team')
  ),
  // array(
  //   'name'   => 'platform-hero',
  //   'title'  => __('Hero Platform'),
  //   'description'    => __('A block for the Platform Hero.'),
  //   'keywords'      => array('hero', 'heading')
  // ),
  // array(
  //   'name'   => 'banner',
  //   'title'  => __('Banners'),
  //   'description'    => __('A Banner to display informations in tiles'),
  //   'keywords'      => array('Banner')
  // ),
  // array(
  //   'name'   => 'cta-banner',
  //   'title'  => __('CTA Banner'),
  //   'description'    => __('CTA Banner'),
  //   'keywords'      => array('CTA Banner')
  // ),
  // array(
  //   'name'   => 'simple-banner',
  //   'title'  => __('Simple Banner'),
  //   'description'    => __('Simple Banner'),
  //   'keywords'      => array('Simple Banner')
  // ),
  // array(
  //   'name'   => 'slider-investors',
  //   'title'  => __('Slider Investors'),
  //   'description'    => __('A Carousel to display Investors'),
  //   'keywords'      => array('Slider', 'Investors')
  // ),
  // array(
  //   'name'   => 'partners-carousel',
  //   'title'  => __('Slider Partner'),
  //   'description'    => __('A Carousel to display partners'),
  //   'keywords'      => array('Carousel', 'Partners')
  // ),
  // array(
  //   'name'   => 'horizontal-accordion',
  //   'title'  => __('Horizontal Accordion'),
  //   'description'    => __('An horizontal accordions made with tiles'),
  //   'keywords'      => array('accordion', 'horizontal')
  // ),
  // array(
  //   'name'   => 'faq',
  //   'title'  => __('FAQ'),
  //   'description'    => __('FAQ Accordion'),
  //   'keywords'      => array('Accordion', 'FAQ', 'QA')
  // ),
  // array(
  //   'name'   => 'products-overview',
  //   'title'  => __('Products Overview'),
  //   'description'    => __('A block for the 3 Products.'),
  //   'keywords'      => array('products', 'overview', 'teaser')
  // ),
  // array(
  //   'name'   => 'advisory-board',
  //   'title'  => __('Advisory Board'),
  //   'description'    => __('Advisory Board'),
  //   'keywords'      => array('Advisory Board')
  // ),
  // array(
  //   'name'   => 'apps',
  //   'title'  => __('Apps Module'),
  //   'description'    => __('Apps'),
  //   'keywords'      => array('Apps')
  // ),
  // array(
  //   'name'   => 'text-image',
  //   'title'  => __('Text and Media'),
  //   'description'    => __('2 columns layout with text and image'),
  //   'keywords'      => array('2 columns', 'text', 'image')
  // ),
  // array(
  //   'name'   => 'platform',
  //   'title'  => __('Text and Lottie'),
  //   'description'    => __('Text and Lottie'),
  //   'keywords'      => array('Text and Lottie')
  // ),
  // array(
  //   'name'   => 'text-graphics',
  //   'title'  => __('Text and Graphics'),
  //   'description'    => __('Text and Graphics'),
  //   'keywords'      => array('Text and Graphics')
  // ),
  // array(
  //   'name'   => 'img-graphics-text',
  //   'title'  => __('Img, graphics and Text'),
  //   'description'    => __('Img, graphics and Text'),
  //   'keywords'      => array('Img, graphics and Text')
  // ),
  // array(
  //   'name'   => 'map',
  //   'title'  => __('Map'),
  //   'description'    => __('Map'),
  //   'keywords'      => array('Map', 'location')
  // ),
  // array(
  //   'name'   => 'security',
  //   'title'  => __('Grid / Security'),
  //   'description'    => __('An multi column component'),
  //   'keywords'      => array('Multi', 'Columns')
  // ),
  // array(
  //   'name'   => 'slider-news',
  //   'title'  => __('Slider News'),
  //   'description'    => __('Slider News'),
  //   'keywords'      => array('Slider', 'News')
  // ),
  // array(
  //   'name'   => 'quote',
  //   'title'  => __('Quote'),
  //   'description'    => __('Quote'),
  //   'keywords'      => array('Quote')
  // ),
  // array(
  //   'name'   => 'contact',
  //   'title'  => __('Contact'),
  //   'description'    => __('Contact'),
  //   'keywords'      => array('Contact', 'Forms')
  // ),
);

// Gutenberg Blocks allowed
$allowed_blocks = array(
  'core/image',
  'core/gallery',
  'core/video',
  'core/paragraph',
  'core/heading',
  'core/list',
  'core/quote',
  'core/html',
  'core/divider',
  'core/table',
  'core/list',
  'core/list-item',
);

// Only allow certain blocks in Gutenberg, here a full list:
// https://rudrastyh.com/gutenberg/remove-default-blocks.html#block_slugs
add_filter('allowed_block_types_all', 'allowed_block_types');
function allowed_block_types($allowed_blocks)
{
  global $acf_blocks;
  global $allowed_blocks;

  // Create array with all the names of the blocks
  $block_names = array();
  foreach ($acf_blocks as $block) {
    array_push($block_names, 'acf/' . $block['name']);
  }
  // Merge acf and allowed blocks
  return array_merge($allowed_blocks, $block_names);
}

// Init for custom blocks
add_action('acf/init', 'acf_blocks');
function acf_blocks()
{

  // check function exists
  if (function_exists('acf_register_block')) {

    global $acf_blocks;

    foreach ($acf_blocks as $block) {
      acf_register_block_type(array_merge(array(
        'render_callback'  => 'acf_blocks_render_callback',
        'category'      => 'design',
        'icon'        => 'star-filled',
        'supports' => array(
          'align' => false,
        ),
      ), $block));
    }
  }
}

function acf_blocks_render_callback($block, $content = '', $is_preview = false)
{

  $context = Timber::context();

  $slug = str_replace('acf/', '', $block['name']);
  $block['name'] = $slug;

  // Store block values.
  $context['fields'] = get_fields();

  // Store field values.
  $context['block'] = $block;

  // Store $is_preview value.
  $context['is_preview'] = $is_preview;
  $context['template'] = "template-parts/blocks/{$slug}.twig";
  $context['file_exists'] = get_theme_file_path("template-parts/blocks/{$slug}.twig");

  if (file_exists(get_theme_file_path("template-parts/blocks/{$slug}.twig"))) {
    $context['file_exists'] = get_theme_file_path("template-parts/blocks/{$slug}.twig");
    // Render the block.
    Timber::render("template-parts/blocks/block.twig", $context);
  }
}
