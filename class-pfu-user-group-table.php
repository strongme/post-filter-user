<?php  
	
class PFU_User_Group_Table extends WP_List_Table {

	private $rawData = array();
	private $found_data = array();

	public function __construct($data) {
		global $status, $page;
		$this->rawData = $data;
		parent::__construct(array(
			'singular' => 'tpl',
			'plural' => 'tpls',
			'ajax' => false
		));
	}

	public function no_items(){
		_e('暂未添加任何用户分组');
	}

	public function column_default($item, $column_name) {
		switch($column_name) {
			case 'name':
				return '<a href="'.menu_page_url(PFU_GROUP_LIST_PAGE,false).'&edit='.$item['id'].'">'.$item[$column_name].'</a>';
			case 'id':
			case 'description':
				return $item[$column_name];
			default:
				return print_r($item, true);
		}
	}

	public function get_sortable_columns() {
		$sortable_columns = array(
			'id' => array('id', flase),
			'name' => array('name',false),
			'description' => array('description', false)
		);
		return $sortable_columns;
	}

	public function get_columns() {
		$columns = array(
			'cb' => '<input type="checkbox" />',
			'name' => '名称',
			'description' => '描述'
		);
		return $columns;
	}

	public function usort_reorder( $a, $b ) {
		// If no sort, default to title
		$orderby = ( ! empty( $_GET['orderby'] ) ) ? $_GET['orderby'] : 'id';
		// If no order, default to asc
		$order = ( ! empty($_GET['order'] ) ) ? $_GET['order'] : 'asc';
		// Determine sort order
		$result = strcmp( $a[$orderby], $b[$orderby] );
		// Send final sort direction to usort
		return ( $order === 'asc' ) ? $result : -$result;
	}

	public function column_title($item){
		$actions = array(
		        'edit'      => sprintf('<a href="'.menu_page_url(PFU_GROUP_LIST_PAGE, false).'&edit=%s">Edit</a>',$item['id']),
		        'delete'    => sprintf('<a href="'.menu_page_url(PFU_GROUP_LIST_PAGE, false).'&delete=%s">Delete</a>',$item['id']),
		    );
		return sprintf('%1$s %2$s', $item['name'], $this->row_actions($actions) );
	}

	public function get_bulk_actions() {
		$actions = array(
			'delete' => '删除'
		);
		return $actions;
	}


	public function column_cb($item) {
		return sprintf(
			'<input type="checkbox" name="tpl[]" value="%s" />', $item['id']	
		);
	}

	public function extra_tablenav( $which ) {
		if ( $which == "top" ){
			//The code that goes before the table is here
		}
		if ( $which == "bottom" ){
			//The code that goes after the table is there
			//echo 'bottom';
		}
	}	


	public function prepare_items() {
		$columns  = $this->get_columns();
		$hidden   = array();
		$sortable = $this->get_sortable_columns();
		$this->_column_headers = array( $columns, $hidden, $sortable );
		usort( $this->rawData, array( &$this, 'usort_reorder' ) );
		$per_page = 10;
		$current_page = $this->get_pagenum();

		$total_items = count( $this->rawData );
		
		// only ncessary because we have sample data
   		$current_page_idx = ( $current_page-1 ) * $per_page;
		$this->found_data = array_slice($this->rawData, 
                                    $current_page_idx,
                                    $per_page );
    
		$this->set_pagination_args( array(
				'total_items' => $total_items, 
        //WE have to calculate the total number of items
				'per_page'    => $per_page 
        //WE have to determine how many items to show on a page
		));
		$this->items = $this->found_data;
	}

}



?>