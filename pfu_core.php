<?php 
/*
Plugin Name: 分级可视化
Plugin URI: https://github.com/strongme/post-filter-user
Description: 根据用户的分组类型，进行文章显示的过来
Version: 1.0
Author: Strongme
Author URI: http://strongme.cn
License: GPLv2
*/

/*

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/


/*

根据用户分组类型来显示当前登录用户能够预览到的文章列表

1. 定义用户分组
2. 设置用户分组
3. 根据用户分组以及文章配置属性中的允许查看的用户分组过滤文章

*/

if ( ! defined( 'ABSPATH' ) ) exit;

define('PFU_PLUGIN_URL',plugins_url( '', __FILE__ ));
define('PFU_GENERAL_PAGE','pfu-general-page');
define('PFU_GROUP_LIST_PAGE','pfu-group-list-page');
define('PFU_TABLE_GROUP_NAME','pfu_user_group');
define('SELECT_ROWS_AMOUNT', 100);


add_action( 'plugins_loaded', 'pfu_create_user_group_table' );
function pfu_create_user_group_table(){
    global $wpdb;
    $table_name =PFU_TABLE_GROUP_NAME; 
    $sql = "CREATE TABLE $table_name (
    id bigint(20) NOT NULL KEY AUTO_INCREMENT,  
    name   varchar(500) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
    description  varchar(1000) CHARACTER SET utf8 COLLATE utf8_general_ci
    );";
    // $sql .= "INSERT INTO $table_name(name,description) VALUES('游客','即未登录的所有用户');";
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

    dbDelta($sql);
}

/*
	初始化此插件的菜单选项
*/
add_action( "_admin_menu", "pfu_init_menu");	
function pfu_init_menu() {
	global $wpdb;
    $table_name =PFU_TABLE_GROUP_NAME; 
	$sql_exist = "SELECT COUNT(id) count FROM $table_name WHERE name = '游客'";
	$is_exist = $wpdb->get_results($sql_exist);
	if($is_exist[0]->count == '0') {
		$sql_youke = "INSERT INTO $table_name(name,description) VALUES('游客','即未登录的所有用户');";
		$wpdb->get_results($sql_youke);
	}
	// add_menu_page( "分级可视化", "分级可视化", "manage_options", __FILE__, "post_filter_user_group_list" );
	global $user_level;
	if(/*$user_level >= 5*/current_user_can('create_users')) {
		$page_title = '分级可视化';
		$menu_title = '分级可视化';
		$capability = 'edit_users';
		$menu_slug = PFU_GENERAL_PAGE;
		$function =  '';
		$icon_url = '';
		add_object_page( $page_title, $menu_title, $capability, $menu_slug, $function, $icon_url );

		require_once('class-pfu-general.php');
		require_once('class-pfu-user-group-list.php');

		$generalObject = PFU_General::get_instance();
		$userGroupList = PFU_User_Group_List::get_instance();

	}

}


/*
	用户编辑页面钩子
*/
add_action( 'edit_user_profile', 'pfu_add_user_group_filed');	
function pfu_add_user_group_filed($user) {
	if(current_user_can('create_users')) {
		$output = '';
		//查询所有分组
		global $wpdb;
		$sql = 'SELECT * FROM '.PFU_TABLE_GROUP_NAME.' ORDER BY id ASC';
		$tmp = $wpdb->get_results($sql);
		$cbs = '';
		$user_groups = get_user_meta( $user->ID, 'user_group');
		foreach ($tmp as $key => $ug) {
			$checked = '';
			if(in_array($ug->id, $user_groups)) {$checked='checked';}
			$cbs .= '<input type="checkbox" name="user_group[]"  value="'.$ug->id.'" '.$checked.'>'.$ug->name.'&nbsp;';
		}
		$output .= '<table class="form-table"><tr><th><label for="user-group">用户分组</label></th>
		<td>'.$cbs.'</td></tr></table>';
		echo $output;
	}

}

/*
	保存用户页面自定义字段
*/
add_action( 'edit_user_profile_update', 'pfu_save_user_group' );	
function pfu_save_user_group($user_id) {
	if (current_user_can('edit_user',$user_id) ) {
		$user_groups = $_POST['user_group'];
		// var_dump($user_groups);exit();
		delete_user_meta( $user_id, 'user_group');
		foreach ($user_groups as $key => $value) {
			add_user_meta( $user_id, 'user_group', $value);
		}
		//
	}
}	


/*
	编辑文章页面钩子触发的方法
*/
add_action( 'edit_form_after_title', 'pfu_add_user_group_in_post');
function pfu_add_user_group_in_post($post) {
	if(current_user_can('create_users')) {
		$output = '';
		//查询所有分组
		global $wpdb;
		$sql = 'SELECT * FROM '.PFU_TABLE_GROUP_NAME.' ORDER BY id ASC';
		$tmp = $wpdb->get_results($sql);
		$cbs = '';
		$user_groups = get_post_meta( $post->ID, 'user_group');
		foreach ($tmp as $key => $ug) {
			$checked = '';
			if(in_array($ug->id, $user_groups)) {$checked='checked';}
			$cbs .= '<input type="checkbox" name="user_group[]"  value="'.$ug->id.'" '.$checked.'>'.$ug->name.'&nbsp;';
		}
		$output .= '<table class="form-table"><tr><td><label for="user-group"><b>允许查看的用户分组</b>：</label>
		'.$cbs.'</td></tr></table>';
		echo $output;
	}
}	


/*
	保存文章自定义字段
*/
add_action( 'save_post', 'pfu_save_user_group_in_post' );	
function pfu_save_user_group_in_post($post_id) {
	if (current_user_can('edit_posts') ) {
		$user_groups = $_POST['user_group'];
		if(!isset($user_groups))return;
		// var_dump($user_groups);exit();
		delete_post_meta( $post_id, 'user_group');
		foreach ($user_groups as $key => $value) {
			add_post_meta( $post_id, 'user_group', $value);
		}
	}
}	



/*
	该过滤器位于nav-menu.php 
*/
add_filter( 'wp_edit_nav_menu_walker', 'custom_nav_edit_walker',10,2 );   
function custom_nav_edit_walker($walker,$menu_id) {
	require_once('class-pfu-walker-nav-menu-edit.php');   
    return 'PFU_Walker_Nav_Menu_Edit';//新类名   
}  

/*
	更新菜单添加钩子保存自定义用户分组字段
*/
add_action( 'wp_update_nav_menu_item', 'pfu_update_nav_menu',10, 3);	
function pfu_update_nav_menu($menu_id, $menu_item_db_id, $args) {
	if ( is_array($_REQUEST['user_group_'.$menu_item_db_id]) ) {
		$user_groups =   $_REQUEST['user_group_'.$menu_item_db_id];
		delete_post_meta( $menu_item_db_id, 'user_group');
		foreach ($user_groups as $key => $value) {
			add_post_meta( $menu_item_db_id, 'user_group', $value);
		} 
    }   
}


/*
	添加过滤文章钩子
*/
add_action( 'pre_get_posts', 'pfu_post_filter_by_user_group' );
function pfu_post_filter_by_user_group($query) {
	if(is_user_logged_in()) {
		$user_id = get_current_user_id();
		//处理文章查询
		if(!is_super_admin( $user_id )) {
			//文章过滤
			// if($query->is_main_query()) {
				//如果登录则根据用户所在分组显示
				$current_user_groups = get_user_meta( $user_id, 'user_group' );
				$query->set('meta_query',array(
					array(
						'key' => 'user_group',
						'value' => $current_user_groups,
						'compare' => 'IN'
					)
				));	
			// }
		}
		
	}else {
		// if($query->is_main_query()) {
			$query->set('meta_key','user_group');
			$query->set('meta_value', '1' );

		// }

	}
	return $query;

}


?>