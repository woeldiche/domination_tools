<?php
/**
 * A toolbox of functions to help you theme Drupal sites.
 *
 * Most of these functions are intended to inject classes for styling into
 * your Drupal site to allow for a more object oriented approach to CSS.
 *
 * Secondly the functions expand on Drupal core's template suggestions and 
 * give examples of how to insert custom template suggestions to streamline
 * the number of tpl.php-files in your theme.
 * 
 * - Replace any instance of THEMENAME with the system name of the theme.
 * - Replace any instance of MENU_NAME or FORMID with the system name of the
 *   menu or formID of the form.
 * - Adapt, remove or combine conditions as your design dictates.
 * - Remove all unneeded code.
 *
 * Fork this on Github.com/woeldiche/domination-tools.
 */

/**
 * Expand Drupal's standard template suggestions.
 * 
 * Some options are:
 * - For page.tpl.php:
 *   - Node type:   $vars['node']->type
 *   - User role:   $vars['user']['roles']
 * - For node.tpl.php:
 *   - View mode:   $vars['view_mode']
 *   - Node type:   $vars['type']
 */
function THEMENAME_preprocess(&$vars, $hook) {
  
  if ($hook == 'page') {
    // Check if the page has a node type and add template suggestion.
    if (isset($vars['node']->type)) {
      // Pattern: page--node--[node_type].tpl.php for node pages.
      $vars['theme_hook_suggestions'][] = 'page__node__' . $vars['node']->type;
    }
  }
  
  if ($hook == 'node') {  
    // Pattern: node--[view mode].tpl.php including custom view modes.
    $vars['theme_hook_suggestions'][] = 'node__' . $vars['view_mode'];
    
    // Pattern: node--[node type]--[view mode].tpl.php.
    $vars['theme_hook_suggestions'][] = 'node__' . $vars['type'] . '__' . $vars['view_mode'];    
  }
  
  if ($hook == 'block') {
    // Add theme suggestion based module.
    switch($vars['elements']['#block']->module) {
      case 'menu':
      case 'menu_block':
        $vars['theme_hook_suggestions'][] = 'block__navigation';
        break;
      
      // Render some blocks without wrapper and leave it to the module.
      case 'views':
      case 'mini_panels':
        $vars['theme_hook_suggestions'][] = 'block__nowrapper';
      
      default:
        break;
    }
  }
}


/**
 * Implements template_preprocess_html().
 *
 * Adds classes to <body> based on path.
 */
function THEMENAME_preprocess_html(&$vars) {
  // Get the current path and break it into sections.
  $parts = explode('/', $vars['page']['path_alias']);

  // Add classes to body based on first section of path.    
  switch ($parts[0]) {
    case 'path_foo':
      $vars['classes_array'][] = 'section-foo';
      break;
    case 'path_bar':
    case 'path_baz':
      $vars['classes_array'][] = 'section-bar';
      break;
    default:
      $vars['classes_array'][] = 'section-baz';
      break;
  }
  
  // Add classes based on combined first and second section.
  switch ($parts[0] . '-' . $parts[1]) {
    case 'path_foo-list':
    case 'path_baz-list':
      $vars['classes_array'][] = 'list-page';
      break;
    default:
      break;
  }
}

/**
 * Sets variable that tells the page if it is being rendered in the overlay.
 *
 * Allows you to hide eg. sidebars in the overlay.
 *
 * Usage in page.tpl.php:
 *   <php? if ($region_name && !$in_overlay): ?>
 *     <?php print render($region_name); ?>
 *   <?php endif; ?>
 */
function THEMENAME_preprocess_page(&$vars) {
  if (module_exists('overlay')) {
    if (overlay_get_mode() == 'child') {
      $vars['in_overlay'] = TRUE;
    } else {
      $vars['in_overlay'] = FALSE;
    }
  }
}


/**
 * Implements template_preprocess_block().
 *
 * Adds classes for styling.
 *
 * Good options are:
 * - Block name:    $vars['elements']['#block']->bid.
 * - Module:        $vars['elements']['#block']->module.
 * - Region:        $vars['elements']['#block']->region.
 */
function THEMENAME_preprocess_block(&$vars, $hook) {
  /**
   * Add classes to blocks created by Views based on views name.
   */
  // Check if block was created by views.
  if ($vars['elements']['#block']->module == 'views') {
    
    // Get views name from $vars['elements']['#block']->delta.
    $block_delta = explode('-', $vars['elements']['#block']->delta);
    $views_name = $block_delta[0];
           
    // Add classes based on views name.
    switch ($views_name) {
      case 'view_foo':
      case 'view_bar':
        $vars['title_attributes_array']['class'][] = 'title-list';
        break;
      
      case 'view_baz':
        $vars['title_attributes_array']['class'] = 'title-block';
        $vars['classes_array'][] = 'block-secondary';
        break;
      
      default:
        $vars['title_attributes_array']['class'][] = 'title-block';
    }      
  }   
        
  /**
   * Add classes based on region.
   */
  switch ($vars['elements']['#block']->region) {
    case 'region_foo':
    case 'region_bar':
    case 'region_baz':
      $vars['title_attributes_array']['class'][] = 'title-list';
      break;
    
    case 'region_foobar':
      $vars['classes_array'][] = 'block-list';
      break;
      
    default;
  }
  
  /* 
   * Add classes based on module excluding certain regions.
   */
  switch ($vars['elements']['#block']->region) { 

    // Exclude certain blocks in certain regions.
    case 'footer_sitemap':
    case 'user_first':
    case 'user_second': 
    case 'menu':
    case 'footer_first':
    case 'footer_second': 
      // Do nothing.
      break;
       
    default:
      switch($vars['elements']['#block']->module) {
        // For the rest of the regions add classes to navigation blocks.
        case 'menu':
        case 'menu_block':
          $vars['attributes_array']['class'][] = 'block-style-menu';
          break;
        
        // And style standard blocks.
        case 'block':
          $vars['attributes_array']['class'][] = 'block-secondary';
          break;

        default:
          break;
      }
  }
}


/**
 * Implements hook_preprocess_node.
 * 
 * Add styling classes based on content type.
 *
 * Good options are:
 * - View Mode: $vars['view_mode']
 * - Content type: $vars['type']
 */
function THEMENAME_preprocess_node(&$vars) {
  // Add classes based on node type.
  switch ($vars['type']) {
    case 'news':
    case 'pages':
      $vars['attributes_array']['class'][] = 'content-wrapper';
      $vars['attributes_array']['class'][] = 'text-content';
      break;
    default:
      break;
  }
  
  // Add classes & theme hook suggestions based on view mode.
  switch ($vars['view_mode']) {
    case 'block_display':
      $vars['theme_hook_suggestions'][] = 'node__aside';
      $vars['title_attributes_array']['class'] = array('title-block');
      $vars['attributes_array']['class'][] = 'block-content';
      $vars['attributes_array']['class'][] = 'st-spot';
      $vars['attributes_array']['class'][] = 'vgrid';
      $vars['attributes_array']['class'][] = 'clearfix';      
      break;
      
    default:
      break;
  }
  
}


/**
 * Implements template_preprocess_field().
 *
 * Adds classes to field based on field name.
 *
 * Good options are:
 * - Field name:    $vars['element']['#field_name'].
 * - Content type:  $vars['element']['#bundle'].
 * - View mode:     $vars['element']['#view_mode'].
 */
function THEMENAME_preprocess_field(&$vars,$hook) {
  // add class to a specific fields across content types.
  switch ($vars['element']['#field_name']) {
    case 'body':
      $vars['classes_array'][] = 'text-content';
      break;
    
    case 'field_summary':
      $vars['classes_array'][] = 'text-teaser';
      break;
    
    case 'field_location':
    case 'field_date':
    case 'field_price':
    case 'field_deadline':
    case 'field_website':
    case 'field_organizer':
    case 'field_contact_information':
      // Replace classes entirely, instead of adding extra.
      $vars['classes_array'] = array('list-definition', 'text-content');
      break;
    
    case 'field_image':
      // Replace classes entirely, instead of adding extra.
      $vars['classes_array'] = array('title-image');
      break;
    
    default:
      break;
  }
  
  // Add classes to body based on content type and view mode.
  if ($vars['element']['#field_name'] = 'body') {
    
    // Add classes to Foobar content type.
    if ($content_type == 'foobar') {
      $vars['classes_array'][] = 'text-secondary';
    } 
    
    // Add classes to other content types with view mode 'teaser';
    elseif ($view_mode == 'teaser') {
      $vars['classes_array'][] = 'text-secondary';
    }
    
    // The rest is text-content.
    else {
      $vars['classes_array'][] = 'text-content';
    }
  }
}


/**
 * Implements template_preprocess_views_view().
 *
 * Adds styling classes to views.
 * Adds custom template suggestions.
 */
function THEMENAME_preprocess_views_view (&$vars) {
  /** 
   * Add custom template suggestions to specific views.
   */
  switch ($vars['view']->name) {
    case 'view_foo':
    case 'view_bar':
    case 'view_baz':
      $vars['theme_hook_suggestions'][] = 'views_view__no_wrapper';
      break;
    
    default:
      break;
  }
  
  /**
   * Add alternating classes to View Foo based on offset.
   */
  if ($vars['view']->name == 'view_foo') {
    switch ($vars['view']->offset) {
      case 0:
        break;
      
      case 1:
        $vars['classes_array'][] = 'st-magenta';
        break;
      
      case 2:
        $vars['classes_array'][] = 'st-yellow';
        break;
      
      case 3:
        $vars['classes_array'][] = 'st-petroleum';
        break;
      
      // Set same style on the rest.
      default:
        $vars['classes_array'][] = 'st-default';
        break;
    }    
  }
}

/**
 * Implements template_preprocess_views_views_fields().
 * 
 * Shows/hides summary on tiles based on presence of images.
 */
function THEMENAME_preprocess_views_view_fields(&$vars) {
  if ($vars['view']->name == 'nodequeue_1') {
    
    // Check if we have both an image and a summary
    if (isset($vars['fields']['field_image'])) {
      
      // If a combined field has been created, unset it and just show image
      if (isset($vars['fields']['nothing'])) {
        unset($vars['fields']['nothing']);
      }
    
    } elseif (isset($vars['fields']['title'])) {
      unset ($vars['fields']['title']);
    }
    
    // Always unset the separate summary if set
    if (isset($vars['fields']['field_summary'])) {
      unset($vars['fields']['field_summary']);
    }
  }
}


/**
 * Implements template_preprocess_panels_pane().
 * 
 * Adds classes for styling.
 */
function THEMENAME_preprocess_panels_pane(&$vars) {
  /**
   * Add styling classes to labels/pane-titles for fields as panes.
   */
  if ($vars['pane']->type == 'entity_field') {
    switch ($vars['content']['#field_name']) {
      case 'field_location':
      case 'field_date':
      case 'field_price':
      case 'field_deadline':
      case 'field_website':
      case 'field_organizer':
      case 'field_contact_information':
        $vars['title_attributes_array']['class'] = array('list-key');

      default;
    }
  
  /**
   * add classes to classes to labels/pane-titles for views panes.
   */
  if ($vars['pane']->type == 'views_panes') {

    // First add classes based on display
    switch ($vars['pane']->subtype) {
      case 'display_name_foo':
        $vars['title_attributes_array']['class'][] = 'content-footer-title';
        $vars['title_attributes_array']['class'][] = 'text-secondary';
        break;
        
      case 'display_name_bar':
      case 'display_name_baz':
        $vars['title_attributes_array']['class'][] = 'title-field';

      default: 
        break;
    }
  }
  
  /**
   * Add classes to labels/pane-titles based on location.
   */
  switch ($vars['pane']->panel) {
    case 'outer_right':
      $vars['title_attributes_array']['class'][] = 'title-block';
      break;
  }
}


/**
 * Implements theme_menu_tree().
 * 
 * Adds classes to all menu wrappers.
 */
function THEMENAME_menu_tree($vars) {
  return '<ul class="menu">' . $vars['tree'] . '</ul>';
}


/**
 * Implements theme_menu_tree().
 *
 * Adds additional wrapper classes for specific menu.
 */
function THEMENAME_menu_tree__MENU_NAME($vars) {
  return '<ul class="menu vertical-menu">' . $vars['tree'] . '</ul>';
}


/**
 * Implements hook_form_FORMID_alter().
 * 
 * Adjust global search box.
 */
function THEMENAME_form_FORMID_alter(&$form) {
  // .. add example of how to add classes to fields and buttons.
}
