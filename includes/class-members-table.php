<?php
class MembersTbl extends WP_List_Table
{
    /**
     * Prepare the items for the table to process
     *
     * @return Void
     */
    public function prepare_items()
    {
        $columns = $this->get_columns();
        $hidden = $this->get_hidden_columns();
        $sortable = $this->get_sortable_columns();
        $action = $this->current_action();

        $data = $this->table_data();
        usort($data, array(&$this, 'usort_reorder'));

        $perPage = 50;
        $currentPage = $this->get_pagenum();
        $totalItems = count($data);

        $this->set_pagination_args(array(
            'total_items' => $totalItems,
            'per_page' => $perPage,
        ));

        $data = array_slice($data, (($currentPage - 1) * $perPage), $perPage);
        $this->_column_headers = array($columns, $hidden, $sortable);
      
        $this->items = $data;
    }

    // Sorting function
    function usort_reorder($a, $b)
    {
        // If no sort, default to user_login
        $orderby = (!empty($_GET['orderby'])) ? $_GET['orderby'] : 'ID';
        // If no order, default to asc
        $order = (!empty($_GET['order'])) ? $_GET['order'] : 'desc';
        // Determine sort order
        $result = strnatcmp($a[$orderby], $b[$orderby]);
        
        // Send final sort direction to usort
        return ($order === 'asc') ? $result : -$result;
    }
    
    /**
     * Override the parent columns method. Defines the columns to use in your listing table
     *
     * @return Array
     */
    public function get_columns()
    {
        $columns = array(
            'cb' => '<input type="checkbox" name="member[]" />',
            'name' => 'Name',
            'email' => 'Email',
            'phone' => 'Phone',
            'date' => 'Registered'
        );

        return $columns;
    }

    /**
     * Define which columns are hidden
     *
     * @return Array
     */
    public function get_hidden_columns()
    {
        return array();
    }

    /**
     * Define the sortable columns
     *
     * @return Array
     */
    public function get_sortable_columns()
    {
        return array(
            'name' => array('name', true),
            'date' => array('date', true)
        );
    }    

    /**
     * Get the table data
     *
     * @return Array
     */
    private function table_data()
    {
        global $wpdb;
        $data = array();

        $members = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}member_records");
        if($members){
            foreach($members as $member){
                $arr = [
                    'ID' => $member->ID,
                    'name' => $member->fname." ".$member->fname,
                    'email' => '<a href="mailto: '.$member->email.'">'.$member->email.'</a>',
                    'phone' => '<a href="tel: '.$member->phone.'">'.$member->phone.'</a>',
                    'date' => date("y-m-d h:i a", strtotime($member->created))
                ];
                
                $data[] = $arr;
            }
        }
        
        return $data;
    }

    /**
     * Define what data to show on each column of the table
     *
     * @param  Array $item        Data
     * @param  String $column_name - Current column name
     *
     * @return Mixed
     */
    public function column_default($item, $column_name)
    {
        switch ($column_name) {
            case $column_name:
                return $item[$column_name];
            default:
                return print_r($item, true);
        }
    }

    public function column_name($item){
        $actions = array(
            'view' => '<a href="?page=member-registration&action=view&member='.$item['ID'].'">View</a>',
            'delete' => '<a href="?page=member-registration&action=delete&member='.$item['ID'].'">Delete</a>',
        );

        return sprintf('%1$s %2$s', $item['name'], $this->row_actions($actions));
    }

    public function get_bulk_actions(){
        $actions = array(
            'send_mail' => 'Send email',
            'delete' => 'Delete'
        );
        return $actions;
    }

    public function bulk_actions($which = ''){
        if ( is_null( $this->_actions ) ) {
			$this->_actions = $this->get_bulk_actions();
			/**
			 * Filters the list table Bulk Actions drop-down.
			 *
			 * The dynamic portion of the hook name, `$this->screen->id`, refers
			 * to the ID of the current screen, usually a string.
			 *
			 * This filter can currently only be used to remove bulk actions.
			 *
			 * @since 3.5.0
			 *
			 * @param string[] $actions An array of the available bulk actions.
			 */
			$this->_actions = apply_filters( "bulk_actions-{$this->screen->id}", $this->_actions );
			$two            = '';
		} else {
			$two = '2';
		}

		if ( empty( $this->_actions ) ) {
			return;
		}

		echo '<label for="bulk-action-selector-' . esc_attr( $which ) . '" class="screen-reader-text">' . __( 'Select bulk action' ) . '</label>';
		echo '<select name="action' . $two . '" id="bulk-action-selector-' . esc_attr( $which ) . "\">\n";
		echo '<option value="-1">' . __( 'Bulk Actions' ) . "</option>\n";

		foreach ( $this->_actions as $name => $title ) {
			$class = 'edit' === $name ? ' class="hide-if-no-js"' : '';

			echo "\t" . '<option value="' . $name . '"' . $class . '>' . $title . "</option>\n";
		}

		echo "</select>\n";

		submit_button( __( 'Apply' ), 'action', '', false, array( 'id' => "doaction$two" ) );
		echo "\n";
    }

    public function column_cb($item){
        return sprintf(
            '<input type="checkbox" name="member[]" value="%s" />', $item['ID']
        );
    }

    function send_email($email, $phone, $name){
		
        $subject = get_option('mr_email_subject');
        $subject = str_replace('{email}', '<strong>'.$email.'</strong>', $subject);
        $subject = str_replace('{name}', '<strong>'.$name.'</strong>', $subject);
        $subject = str_replace('{phone}', '<strong>'.$phone.'</strong>', $subject);

        $message = get_option('mr_email_body');
        $message = str_replace('{email}', '<strong>'.$email.'</strong>', $message);
        $message = str_replace('{name}', '<strong>'.$name.'</strong>', $message);
        $message = str_replace('{phone}', '<strong>'.$phone.'</strong>', $message);

        $emlData = [
			'logo' => get_option('mr_email_logo'),
			'body' => $message,
			'footer' => ((get_option('mr_email_footer_text')) ? get_option('mr_email_footer_text') : '@2022 '.get_bloginfo( 'name' ).' inc.' )
		];

		$body = mr_email_template($emlData);

		$headers = array('Content-Type: text/html; charset=UTF-8');
		 
		wp_mail( $email, $subject, $body, $headers );
	}

    // All form actions
    public function current_action(){
        global $wpdb;
        if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'delete' && isset($_REQUEST['member'])) {
            if(is_array($_REQUEST['member'])){
                $ids = $_REQUEST['member'];
                foreach($ids as $ID){
                    $wpdb->query("DELETE FROM {$wpdb->prefix}member_records WHERE ID = $ID");
                }
            }else{
                $ID = intval($_REQUEST['member']);
                $wpdb->query("DELETE FROM {$wpdb->prefix}member_records WHERE ID = $ID");
            }

            if(!is_wp_error( $wpdb )){
                wp_safe_redirect( admin_url( 'admin.php?page=member-registration' ) );
                exit;
            }
        }

        if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'send_mail' && isset($_REQUEST['member'])) {
            $records = $_REQUEST['member'];
            $formSetting = get_option( 'member_register_form_data' );

            if(is_array($records)){
                foreach($records as $record){
                    $memberResult = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}member_records WHERE ID = $record");
                    if($memberResult){
                        $email = $memberResult->email;
                        $phone = $memberResult->phone;
                        $name = $memberResult->fname." ".$memberResult->lname;

                        $this->send_email($email, $phone, $name);
                    }
                }
            }
        }
    }

} //class
