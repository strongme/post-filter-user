<?php  

class PFU_General {

	private $file_general_tpl = '_general.php';

	private static $_instance;

	public static function get_instance() {
		if(!isset(self::$_instance)) {
			$c = __CLASS__;
			self::$_instance = new $c;
		}
		return self::$_instance;
	}

	public function __clone() {
		trigger_error('CLone is not allow', E_USER_ERROR);
	}

	private function __construct() {
		add_action('admin_menu',array($this, 'add_plugin_page'));
	}

	
	public function add_plugin_page() {
		$parent_slug = PFU_GENERAL_PAGE;
		$page_title = '分级可视化';
		$menu_title = '常规';
		$capability = 'edit_users';
		$menu_slug = PFU_GENERAL_PAGE;
		add_submenu_page( 
			$parent_slug, 
			$page_title, 
			$menu_title, 
			$capability, 
			$menu_slug, 
			array($this, 'create_admin_page'));
	}

	public function create_admin_page() {
		require_once($this->file_general_tpl);
	}

}

?>