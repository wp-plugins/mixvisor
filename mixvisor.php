<?php
/**
 * Plugin Name: Mixvisor
 * Plugin URI: http://mixvisor.com
 * Description: Mixvisor helps your users discover the artists they read about in your content.
 * Version: 0.1.2
 * Author: Mixvisor
 * Author URI: http://mixvisor.com
 * Copyright: Copyright 2015 Mixvisor - Giles Butler
 */

// Setup admin page
// ----------------

add_action('admin_menu', 'mixvisor_menu');

function mixvisor_menu() {
  add_submenu_page('options-general.php', 'Mixvisor Settings', 'Mixvisor', 'manage_options', 'mixvisor', 'mixvisor_settings_page');
}

function mixvisor_settings_page() {
  // Get categories
  $cat_args = array(
    'hide_empty' => 0
  );
  $site_categories     = get_categories( $cat_args );
  $mixvisor_categories = get_option('mixvisor_exclude_categories');
  $selected_categories = explode(",", $mixvisor_categories);

  // Get pages
  $site_pages          = get_pages();
  $mixvisor_pages      = get_option('mixvisor_exclude_pages');
  $home_page           = get_option('page_on_front');
  $default_home_page   = true;
  if ( isset($mixvisor_pages) ) {
    $selected_pages    = explode(",", $mixvisor_pages);
  }
  else {
    $selected_pages    = array();
  }

  // Check if the homepage's ID is in the $site_pages array
  foreach ($site_pages as $page) {
    if ( $page->ID == $home_page ) {
      // Homepage is in site pages
      $default_home_page = false;
      // Remove defualt homepage from $selected_pages array
      if (($key = array_search('0', $selected_pages)) !== false) {
        unset($selected_pages[$key]);
        // Update the exlcuded pages option
        $updated_pages_option = implode(",", $selected_pages);
        update_option('mixvisor_exclude_pages', $updated_pages_option);
      }
      break;
    }
  }

  // If the default homepage is being used add it to the $site_pages array
  if ( $default_home_page ) {
    // Create the homepage object
    $default_home_page_object = new stdClass();
    // Assign the homepage object properties
    $default_home_page_object->ID = $home_page;
    $default_home_page_object->post_name = 'homepage';
    $default_home_page_object->post_title = 'Homepage';
    // Add the homepage object to the $site_pages array
    array_push($site_pages, $default_home_page_object);
  }
  ?>

  <!-- Output Styles -->
  <style type="text/css" media="screen">
    @media screen and (min-width: 783px) {
      .mv-admin-inline-list {
        max-width: 630px;
      }

      .mv-admin-inline-list li {
        float: left;
        margin-bottom: 20px;
        margin-right: 20px;
      }
    }
  </style>

  <!-- Output markup -->
  <div class="wrap">
    <h2>Mixvisor <?php _e( 'Settings', 'mixvisor-plugin' ) ?></h2>

    <form method="post" action="options.php" id="mixvisor_options">
      <?php settings_fields( 'mixvisor-settings-group' ); ?>
      <?php do_settings_sections( 'mixvisor-settings-group' ); ?>
      <table class="form-table">
        <tr valign="top">
          <th scope="row"><?php _e( 'Site ID', 'mixvisor-plugin' ) ?>:</th>
          <td>
          <textarea name="mixvisor_sid" cols="85" rows="1"><?php echo esc_attr( get_option('mixvisor_sid') ); ?></textarea>
          </td>
        </tr>

        <tr valign="top">
          <th scope="row"><?php _e( 'Access Token', 'mixvisor-plugin' ) ?>:</th>
          <td>
            <textarea name="mixvisor_at" cols="85" rows="2"><?php echo esc_attr( get_option('mixvisor_at') ); ?></textarea>
          </td>
        </tr>

        <tr valign="top">
          <th scope="row"><?php _e( 'Categories', 'mixvisor-plugin' ) ?>:</th>
          <td>
            <p class="description">Choose which categories to exclude Mixvisor from.</p>
            <ul class="mv-admin-inline-list">
            <?php
              foreach ($site_categories as $category) {
                $checked = '';
                if (in_array($category->term_id, $selected_categories)) {
                  $checked = 'checked';
                }
                ?>
                  <li>
                    <input
                      type="checkbox"
                      data-mv-type="categories"
                      class="js-mv-checkbox" <?php echo $checked; ?>
                      id="mv_<?php echo $category->slug; ?>"
                      name="<?php echo $category->slug; ?>"
                      value="<?php echo $category->term_id; ?>">

                    <label for="mv_<?php echo $category->slug; ?>"><?php echo $category->name; ?></label>
                  </li>
                <?php
              }
            ?>
            </ul>
            <input type="hidden" id="mixvisor_categories" name="mixvisor_exclude_categories" value="<?php echo esc_attr( get_option('mixvisor_exclude_categories') ); ?>">
            <div class="clear"></div>
          </td>
        </tr>

        <tr valign="top">
          <th scope="row"><?php _e( 'Pages', 'mixvisor-plugin' ) ?>:</th>
          <td>
            <p class="description">Choose which pages to exclude Mixvisor from.</p>
            <ul class="mv-admin-inline-list">
            <?php
              foreach ($site_pages as $page) {
                $checked = '';
                if (in_array($page->ID, $selected_pages)) {
                  $checked = 'checked';
                }
                ?>
                  <li>
                    <input
                      type="checkbox"
                      data-mv-type="pages"
                      class="js-mv-checkbox" <?php echo $checked; ?>
                      id="mv_<?php echo $page->post_name; ?>"
                      name="<?php echo $page->post_name; ?>"
                      value="<?php echo $page->ID; ?>">

                    <label for="mv_<?php echo $page->post_name; ?>"><?php echo $page->post_title; ?></label>
                  </li>
                <?php
              }
            ?>
            </ul>
            <input type="hidden" id="mixvisor_pages" name="mixvisor_exclude_pages" value="<?php echo esc_attr( get_option('mixvisor_exclude_pages') ); ?>">
            <div class="clear"></div>
          </td>
        </tr>
      </table>

      <?php submit_button(); ?>
    </form>

    <hr>

    <p><a href="http://mixvisor.com">Mixvisor</a> &#124; <a href="#" class="mv-js-trigger-doorbell mv-feedback">Support</a> &#124; <a href="https://twitter.com/mixvisor">Twitter</a> &#124; <a href="https://www.facebook.com/mixvisor">Facebook</a></p>
  </div>

  <!-- Output JS -->
  <script>
    // Update the categories when the inputs are toggled
    (function ($) {
      // Vars
      var $mixvisorOptions = $('#mixvisor_options');

      // Events
      $mixvisorOptions.on('click', '.js-mv-checkbox', toggleCheckbox);

      // Functions
      function toggleCheckbox(e) {
        // Get the item type, ID and current values
        var mvType            = e.currentTarget.getAttribute('data-mv-type'),
            itemID            = e.currentTarget.value,
            currentItems      = document.getElementById('mixvisor_' + mvType),
            currentItemsArray = [];

        if ( currentItems.value !== '' ) {
          currentItemsArray = currentItems.value.split(',');
        }

        // Check if the item is already in the array
        if (currentItemsArray.indexOf(itemID) > -1) {
          // If it is remove it
          var index = currentItemsArray.indexOf(itemID);
          currentItemsArray.splice(index, 1);
        }
        else {
          // If not add the item to the array
          currentItemsArray.push(itemID);
        }
        // Set the hidden input field to the value of the array
        currentItems.value = currentItemsArray;
      }

    })(jQuery);
  </script>

  <!-- Output Doorbell -->
  <script type="text/javascript">
    window.doorbellOptions = {
      hideButton: true,
      appKey: 'ZBTzR5e4vbaKLA1OQfifJVT1bdfnlTqUyi7sxudc8hLLl8s7R3ud9tTZrbrrqfEB',
      timestamp: <?php echo $time=time(); ?>,
      token: '<?php echo $token=mt_rand(0, 999999); ?>',
      signature: '<?php echo hash_hmac('sha256', $time.$token, '76aWfGtcbJ8XLTF9GOt5EKxDbo1mkOsOS1DZxYKR34CczGPXl4llxOHKiD6QRWaN'); ?>',
    };
    (function(d, t) {
      var g = d.createElement(t);g.id = 'doorbellScript';g.type = 'text/javascript';g.async = true;g.src = 'https://doorbell.io/button/771?t='+(new Date().getTime());(d.getElementsByTagName('head')[0]||d.getElementsByTagName('body')[0]).appendChild(g);
    }(document, 'script'));

    function showDoorbellModal() {
      doorbell.show(); // The doorbell object gets created by the doorbell.js script
    }

    jQuery('.mv-js-trigger-doorbell').on('click', showDoorbellModal);
  </script>

  <?php
}

add_action( 'admin_init', 'mixvisor_settings' );

function mixvisor_settings() {
  register_setting( 'mixvisor-settings-group', 'mixvisor_sid' );
  register_setting( 'mixvisor-settings-group', 'mixvisor_at' );
  register_setting( 'mixvisor-settings-group', 'mixvisor_exclude_categories' );
  register_setting( 'mixvisor-settings-group', 'mixvisor_exclude_pages' );
}

// Add script to page
// ------------------

add_action( 'wp_footer', 'add_mixvisor_script_tag' );

function add_mixvisor_script_tag() {
  // Check whether a user has a SID and AT
  $MVSID = get_option('mixvisor_sid');
  $MVAT  = get_option('mixvisor_at');

  // If they do, do our thang!
  if ($MVAT && $MVSID) {
    $embedCode = '<script async defer src="//storage.mixvisor.com/mvjsp/v1/mv-latest.js" data-mv-sid="' . esc_attr( $MVSID ) . '" data-mv-at="' . esc_attr( $MVAT ) . '" id="_MXV_"></script>';

    // Selected Categories to exclude
    $mixvisor_categories = get_option('mixvisor_exclude_categories');

    // If there are any categories excluded create an array
    if ( empty($mixvisor_categories) && $mixvisor_categories !== '0'  ) {
      $selected_categories = array();
    }
    else {
      $selected_categories = explode(",", $mixvisor_categories);
    }

    // Selected Pages to exclude
    $mixvisor_pages           = get_option('mixvisor_exclude_pages');
    $include_default_homepage = true;
    $code_embedded            = false;

    // If there are any pages excluded create an array
    if ( empty($mixvisor_pages) && $mixvisor_pages !== '0'  ) {
      $selected_pages = array();
    }
    else {
      $selected_pages = explode(",", $mixvisor_pages);
    }

    // Get the current Page ID
    $page_id = get_queried_object_id();

    // Check to see if default homepage is excluded
    if (($key = array_search('0', $selected_pages)) !== false) {
      // If it is excluded remove its key from the $selected_pages array as passing a false value (id: 0) to some wordpress functions can break them
      unset($selected_pages[$key]);
      // Set $include_default_homepage to false so we know not to include
      $include_default_homepage = false;
    }

    // 1. If a category has been excluded don't embed the code on the category landing page
    if ( is_category() ) {
      if ( !empty($selected_categories) && !is_category($selected_categories) ) {
        echo $embedCode;
        $code_embedded = true;
      }
    }

    // 2. If a category has been excluded don't embed the code in any of its posts
    if ( is_single() ) {
      // If they have excluded any categories and its not the homepage (wp bug with in_category)
      if ( !empty($selected_categories) && !in_category($selected_categories) && !is_home() ) {
        echo $embedCode;
        $code_embedded = true;
      }
    }

    // 3. If a page has been excluded don't embed the code on that page
    if ( is_page() ) {
      if ( !empty($selected_pages) && !is_page($selected_pages) && $page_id !== 0 ) {
        echo $embedCode;
        $code_embedded = true;
      }
    }

    // 4. If the default homepage hasn't been excluded embed the code
    if ( $page_id === 0 && $include_default_homepage ) {
      echo $embedCode;
      $code_embedded = true;
    }

    // 5. If no categories have been selected and no pages have been excluded add the code to every page
    if ( empty($selected_pages) && empty($selected_categories) && !$code_embedded && $page_id !== 0 ) {
      echo $embedCode;
      $code_embedded = true;
    }
  }

}

// Add settings link on plugin page
// --------------------------------
function mixvisor_settings_link($links) {
  $settings_link = '<a href="options-general.php?page=mixvisor">Settings</a>';
  array_unshift($links, $settings_link);
  return $links;
}

$plugin = plugin_basename(__FILE__);
add_filter("plugin_action_links_$plugin", 'mixvisor_settings_link' );
