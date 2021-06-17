<?php
/*
Plugin Name: Fab Rest Api Mail
Plugin URI: https://www.telnetsrl.com/
Description: Plugin per inviare mail by restapi
Author: Fabrizio MESTO
Version: 0.0.1
Author URI: https://www.telnetsrl.com/
Text Domain: fabrestapimail
Domain Path: lang
*/

namespace restapimail;

defined('ABSPATH') or die('No script kiddies please!');

error_reporting(E_ALL | E_WARNING | E_NOTICE);
ini_set('display_errors', TRUE);

if (!defined(__NAMESPACE__ . '\FAB_PLUGIN_DIR_PATH')) define(__NAMESPACE__ . '\FAB_PLUGIN_DIR_PATH', plugin_dir_path(__FILE__));
if (!defined(__NAMESPACE__ . '\FAB_PLUGIN_DIR_URL')) define(__NAMESPACE__ . '\FAB_PLUGIN_DIR_URL', plugin_dir_url(__FILE__));

if (class_exists('\fab\Fab_Base')) {

  class Fab_RestApiMail extends \fab\Fab_Base
  {
    public $shortcode_name = 'fab-rest-api-mail';

    public $action_name = 'A';
    public $controller_name = 'C';
    public $default_controller = 'dashboard';
    public $current_controller = 'dashboard';
    public $PLUGIN_DIR_PATH = FAB_PLUGIN_DIR_PATH;
    public $PLUGIN_DIR_URL = FAB_PLUGIN_DIR_URL;
    public $NAMESPACE = __NAMESPACE__;
    public $macaddress_name = "mailrestapi_macaddress";
    public $rewrite_rule = true;

    public function plugins_loaded()
    {
      parent::plugins_loaded();

      require FAB_PLUGIN_DIR_PATH . 'includes/settings.php';
      new settings($this);

      require FAB_PLUGIN_DIR_PATH . 'includes/hooks.php';
      new hooks();

      require FAB_PLUGIN_DIR_PATH . 'includes/metabox.php';
      new metabox();
    }
  }

  $BASE = Fab_RestApiMail::getInstance();
} else {
  include_once(ABSPATH . 'wp-admin/includes/plugin.php');
  if (!is_plugin_active('fab-base-plugin/fab-base.php')) {
    echo "Questo plugin richiede fab-base";
  }
}
