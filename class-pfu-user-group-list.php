<?php  
	
	
class PFU_User_Group_List {

	private $file_user_group_edit= '_user_group_edit.php';
	private $file_user_group_list= '_user_group_list.php';

	private static $_instance;

	public static function get_instance() {
		if(!isset(self::$_instance)) {
			$c = __CLASS__;
			self::$_instance = new $c;
		}
		return self::$_instance;
	}

	public function __clone() {
		trigger_error('Clone is not allow', E_USER_ERROR);
	}

	private function __construct() {
		add_action('admin_menu',array($this, 'add_plugin_page'));
	}

	public function add_plugin_page() {
		$parent_slug = PFU_GENERAL_PAGE;
		$page_title = '分级可视化';
		$menu_title = '用户分组管理';
		$capability = 'edit_users';
		$menu_slug = PFU_GROUP_LIST_PAGE;

		add_submenu_page( 
			$parent_slug, 
			$page_title, 
			$menu_title, 
			$capability, 
			$menu_slug, 
			array($this, 'create_admin_page'));
	}

	public function create_admin_page() {
		if(isset($_GET['edit'])){
			require_once( $this->file_user_group_edit);
		}else{
			require_once($this->file_user_group_list);
		}
	}

}



?>