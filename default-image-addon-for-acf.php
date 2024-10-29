<?php
/*
 * Plugin Name: Default Image Addon for ACF
 * Plugin URI:  https://wordpress.org/plugins/acf-default-image-addon
 * Description: This plugin provides the feature to add an option for the default image in the field type image.
 * Version:     1.3
 * Author:      Galaxy Weblinks
 * Author URI:  https://www.galaxyweblinks.com/
 * Text Domain: defaultimageaddonforacf
 * License: GPL v2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
*/

if (!defined('ABSPATH')) {
  exit; // disable direct access
}

/**
 * Add notice error message when ACF plugin not activated.
 * @param array $field
 * @return void
*/
function diaa_notice_missing_has_parent_plugin() {
if ( is_admin() && current_user_can( 'activate_plugins' ) && ! is_plugin_active( 'advanced-custom-fields-pro/acf.php' ) && ! is_plugin_active( 'advanced-custom-fields/acf.php' ) ) {
    deactivate_plugins( plugin_basename( __FILE__ ) );

    /* If we try to activate this plugin while the parent plugin isn't active. */
    if ( isset( $_GET['activate'] ) && ! wp_verify_nonce( $_GET['activate'] ) ) {
      add_action( 'admin_notices', 'diaa_notice_missing_acf' );
      unset( $_GET['activate'] );
      /* If we deactivate the parent plugin while this plugin is still active. */
    } elseif ( ! isset( $_GET['activate'] ) ) {
      add_action( 'admin_notices', 'diaa_notice_missing_acf' );
      unset( $_GET['activate'] );
    }
  }
}
add_action( 'admin_init', 'diaa_notice_missing_has_parent_plugin' );

function diaa_notice_missing_acf()
{
  global $pagenow;
  if ( is_admin() && $pagenow == 'plugins.php' ) {
    _e( '<div class="notice notice-error is-dismissible"><p>Default Image Addon for ACF plugin needs "Advanced Custom Fields or Advanced Custom Fields PRO" to run. Please download from <a href="https://wordpress.org/plugins/acf-default-image-addon/" target="_blank">here</a> and activate it</p></div>', 'acf-nav-menu' );
    $diaa_plugins = array(
      'advanced-custom-nav-menu-field/advanced-custom-nav-menu-field.php'
    );
    require_once(ABSPATH . 'wp-admin/includes/plugin.php');
    deactivate_plugins($diaa_plugins);     
  }   
}

/**
 * Add backend option for default image in field type "image" in ACF.
 * @param array $field
 * @return void
 */
if (!function_exists('diaa_add_default_value_to_image_field')) {
  function diaa_add_default_value_to_image_field($field) {
    acf_render_field_setting( $field, array(
      'label'           => 'Default Image',
      'instructions'    => 'Add default image here',
      'type'            => 'image',
      'name'            => 'default_value',
    ));
  }
  add_action('acf/render_field_settings/type=image', 'diaa_add_default_value_to_image_field');
}

/**
 * Save and render the default image on the front end.
 * @param array $value
 * @param int $post_id
 * @param array $field
 * @return void
 */
if (!function_exists('diaa_reset_default_image')) {
  function diaa_reset_default_image($value, $post_id, $field) {
    if (!$value && isset($field['default_value'])) {
      $value = $field['default_value'];
    }
    return $value;
  }
  add_filter('acf/load_value/type=image', 'diaa_reset_default_image', 10, 3);
}

