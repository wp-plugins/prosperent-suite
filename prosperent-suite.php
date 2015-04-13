<?php
/*
Plugin Name: Prosperent Suite
Description: Contains all of the Prosperent tools in one plugin to easily monetize your blog.
Version: 3.6.4
Author: Prosperent Brandon
Author URI: http://prosperent.com
Plugin URI: http://community.prosperent.com/forumdisplay.php?35-Wordpress-Plugin-Suite
License: GPLv3

    Copyright 2012  Prosperent Brandon  (email : brandon@prosperent.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

// Default caching time for products (in seconds)
if (!defined( 'PROSPER_CACHE_PRODS'))
    define( 'PROSPER_CACHE_PRODS', 86400 );
// Default caching time for trends and coupons (in seconds)
if (!defined( 'PROSPER_CACHE_COUPS'))
    define( 'PROSPER_CACHE_COUPS', 86400 );

if (!defined( 'WP_CONTENT_DIR'))
    define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );
if (!defined('PROSPER_URL'))
    define('PROSPER_URL', plugin_dir_url(__FILE__));
if (!defined('PROSPER_PATH'))
    define('PROSPER_PATH', plugin_dir_path(__FILE__));
if (!defined('PROSPER_BASENAME'))
    define('PROSPER_BASENAME', plugin_basename(__FILE__));
if (!defined('PROSPER_FOLDER'))
    define('PROSPER_FOLDER', plugin_basename(dirname(__FILE__)));
if (!defined('PROSPER_FILE'))
    define('PROSPER_FILE', basename((__FILE__)));
if (!defined('PROSPER_CACHE'))
	define('PROSPER_CACHE', WP_CONTENT_DIR . '/prosperent_cache');
if (!defined('PROSPER_INCLUDE'))
	define('PROSPER_INCLUDE', PROSPER_PATH . 'includes');
if (!defined('PROSPER_MODEL'))
	define('PROSPER_MODEL', PROSPER_INCLUDE . '/models');
if (!defined('PROSPER_WIDGET'))
	define('PROSPER_WIDGET', PROSPER_INCLUDE . '/widgets');
if (!defined('PROSPER_VIEW'))
	define('PROSPER_VIEW', PROSPER_INCLUDE . '/views');
if (!defined('PROSPER_IMG'))
	define('PROSPER_IMG', PROSPER_URL . 'includes/img');
if (!defined('PROSPER_JS'))
	define('PROSPER_JS', PROSPER_URL . 'includes/js');
if (!defined('PROSPER_CSS'))
	define('PROSPER_CSS', PROSPER_URL . 'includes/css');
if (!defined('PROSPER_THEME'))
	define('PROSPER_THEME', WP_CONTENT_DIR . '/prosperent-themes');

error_reporting(0);   

require_once(PROSPER_INCLUDE . '/ProsperIndexController.php');

if(is_admin())
{
	require_once(PROSPER_INCLUDE . '/ProsperAdminController.php');
}
