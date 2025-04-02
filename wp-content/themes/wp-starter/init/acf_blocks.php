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
  array(
    'name'   => 'membership-fees',
    'title'  => __('Membership fees'),
    'description'    => __('Membership fees'),
    'keywords'      => array('Membership fees')
  ),
  array(
    'name'   => 'eea-anchor-listing',
    'title'  => __('EEA anchor listing'),
    'description'    => __('EEA anchor listing'),
    'keywords'      => array('eea', 'anchor')
  ),
  array(
    'name'   => 'eea-board-wall',
    'title'  => __('EEA board wall'),
    'description'    => __('EEA board wall'),
    'keywords'      => array('eea', 'board', 'wall')
  ),
  array(
    'name'   => 'ctas',
    'title'  => __('CTAs'),
    'description'    => __('CTAs'),
    'keywords'      => array('CTA', 'CTAs')
  ),
  array(
    'name'   => 'listed-paragraphs',
    'title'  => __('Listed Paragraphs'),
    'description'    => __('Listed Paragraphs'),
    'keywords'      => array('Listed', 'Paragraphs')
  ),
  array(
    'name'   => 'home-teaser',
    'title'  => __('Home Teaser'),
    'description'    => __('Home Teaser'),
    'keywords'      => array('Home', 'Teaser')
  ),
  array(
    'name'   => 'application',
    'title'  => __('Application'),
    'description'    => __('Application'),
    'keywords'      => array('Application')
  ),
  array(
    'name'   => 'eea-groups',
    'title'  => __('EEA Groups'),
    'description'    => __('EEA Groups'),
    'keywords'      => array('EEA Groups')
  ),
  array(
    'name'   => 'partners-carousel',
    'title'  => __('Slider Partner'),
    'description'    => __('A Carousel to display partners'),
    'keywords'      => array('Carousel', 'Partners')
  ),
  array(
    'name'   => 'horizontal-accordion',
    'title'  => __('Horizontal Accordion'),
    'description'    => __('An horizontal accordions made with tiles'),
    'keywords'      => array('accordion', 'horizontal')
  ),
  array(
    'name'   => 'faq',
    'title'  => __('FAQ'),
    'description'    => __('FAQ Accordion'),
    'keywords'      => array('Accordion', 'FAQ', 'QA')
  ),
  array(
    'name'   => 'entries',
    'title'  => __('Entries'),
    'description'    => __('Entries'),
    'keywords'      => array('entries')
  ),
  array(
    'name'   => 'quote',
    'title'  => __('Quote'),
    'description'    => __('Quote'),
    'keywords'      => array('Quote')
  ),
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
