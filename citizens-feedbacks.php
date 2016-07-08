<?php
/*
Plugin name: Citizens Feedbacks
Description: Simple citizens feedback form.
Author: Evgeniy Pak
Text Domain: cfeedbacks
Version: 1.1.1
*/

if(!defined('ABSPATH')) exit;

class CitizensFeedbacks{
	public $data = array();
	public $cfeedback_status_arr = array('new', 'handling', 'ready', 'deleted');
	function CitizensFeedbacks(){
		global $wpdb;
		define('CITIZENS_FEEDBACKS', true);		
		$this->plugin_name = plugin_basename(__FILE__);
		$this->plugin_url = trailingslashit(WP_PLUGIN_URL.'/'.dirname(plugin_basename(__FILE__)));
		$this->tbl_cfeedbacks = $wpdb->prefix . 'cfeedbacks';
		register_activation_hook($this->plugin_name, array(&$this, 'activate'));
		register_deactivation_hook($this->plugin_name, array(&$this, 'deactivate'));
		
		if(is_admin()){
			add_action('admin_menu', array(&$this, 'generate_admin_menu'));
		}else{
			add_action('wp_print_styles', array(&$this, 'public_load_styles'));
			add_shortcode('show_cfeedbacks_form', array(&$this, 'public_show_cfeedbacks'));
			add_shortcode('check_status', array(&$this, 'public_check_status'));
		}
	}
	
	
	
	public function public_load_styles(){
		wp_register_style('CfeedbacksCss', $this->plugin_url . 'css/citizens-feedbacks-style.css');
		wp_enqueue_style('CfeedbacksCss');
	}
	
	
	
	function activate(){
		global $wpdb;
		require_once(ABSPATH . "wp-admin/upgrade-functions.php");
		$table = $this->tbl_cfeedbacks;
		if(version_compare(mysql_get_server_info(), '4.1.0', '>=')){
			if(!empty($wpdb->charset)){
				$charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
			}
			if(!empty($wpdb->collate)){
				$charset_collate .= " COLLATE $wpdb->collate";
			}
		}
		
		$cfeedbacks_tabel_query = "CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."cfeedbacks` (
			`id` INT(11) AUTO_INCREMENT,
			`cfeedbacks_title` VARCHAR(100) NOT NULL DEFAULT '0',
			`cfeedbacks_text` TEXT NOT NULL,
			`cfeedbacks_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
			`cfeedbacks_user_name` VARCHAR(100) NOT NULL,
			`cfeedbacks_user_email` VARCHAR(100) NOT NULL,
			`cfeedbacks_status` VARCHAR(10) NOT NULL DEFAULT 'new',
			PRIMARY KEY(`id`)
		)".$charset_collate.";";
		
		if($wpdb->get_var("SHOW TABLES LIKE '".$table."'") != $table){
			dbDelta($cfeedbacks_tabel_query);
		}
	}
	
	
	
	function deactivate(){
		return true;
	}
	
	
	
	function generate_admin_menu(){
		add_menu_page(esc_html__("Welcome to the Citizens Feedbacks settings page", "cfeedbacks"), esc_html__("Citizens Feedbacks", "cfeedbacks"), 'manage_options', 'edit-cfeedbacks', array(&$this, 'admin_edit_cfeedbacks'), "dashicons-testimonial");
		add_submenu_page('edit-cfeedbacks', esc_html__("About plugin", "cfeedbacks"), esc_html__("About plugin", "cfeedbacks"), 'manage_options', 'plugin_info', array(&$this,'admin_plugin_info'));
	}
	
	
	
	public function admin_edit_cfeedbacks(){
		global $wpdb;
		$action = isset($_GET['action']) ? esc_html($_GET['action']) : null;
		switch($action){
			case 'edit':
				$this->data['cfeedbacks'] = $wpdb->get_row("SELECT * FROM `".$this->tbl_cfeedbacks."` WHERE `id` = " . (int)$_GET['id'], ARRAY_A);
				include_once('edit_cfeedbacks.php');
				break;
			case 'submit':
				$cfeedbacks_title = sanitize_text_field($_POST['cfeedbacks_title']);
				$cfeedbacks_text = sanitize_text_field($_POST['cfeedbacks_text']);
				$cfeedbacks_user_name = sanitize_text_field($_POST['cfeedbacks_user_name']);
				$cfeedbacks_user_email = sanitize_email($_POST['cfeedbacks_user_email']);
				$cfeedbacks_status = sanitize_text_field($_POST['cfeedbacks_status']);
				
				$inputData = array(
					'cfeedbacks_title' => $cfeedbacks_title,
					'cfeedbacks_text' => $cfeedbacks_text,
					'cfeedbacks_user_name' => $cfeedbacks_user_name,
					'cfeedbacks_user_email' => $cfeedbacks_user_email,
					'cfeedbacks_status' => $cfeedbacks_status,
				);
				$editId = intval($_POST['id']);
				if($editId == 0)
					return false;
				
				$wpdb->update($this->tbl_cfeedbacks, $inputData, array('id' => $editId));
				$this->admin_show_cfeedbacks();
				break;
			case 'delete':
				$wpdb->query("DELETE FROM `".$this->tbl_cfeedbacks."` WHERE `id` = '".(int)$_GET['id']."'");
				$this->admin_show_cfeedbacks();
				break;
			case 'send_response':
				$citizens_email = sanitize_email($_POST['email_to_response']);
				$sitename = get_option('blogname');
				
				$mail_message = "Message:\n" . sanitize_text_field($_POST['cfeedbacks_response']);
				
				/* $header = "MIME-Version: 1.0\n";
				$header .= "Content-Type: text/html; charset=utf-8\n";
				$header .= "From:" . $sitename; */
				wp_mail($citizens_email, "Feedback was send from " . $sitename, $mail_message);
			default:
				$this->admin_show_cfeedbacks();
		}
	}
	
	private function admin_show_cfeedbacks(){
		global $wpdb;
		include_once('view_cfeedbacks.php');
	}
	
	
	
	public function admin_plugin_info(){
		require_once('plugin_info.php');
	}
	
	
	
	public function public_show_cfeedbacks($atts, $content=null){
		global $wpdb;
		if(isset($_POST['action']) && $_POST['action'] == 'add-cfeedbacks'){
			$url = strtok($_SERVER['HTTP_REFERER'], '?');
			$result = $this->add_cfeedbacks();
			if(strlen($result) > 0){
				$message = "?add_message=" . $result;
			}
			?>
			<script type="text/javascript">
				  document.location.href="<?php echo $url . $message; ?>";
			</script>
			<?php
		}
		require_once('public_cfeedbacks_form.php');
	}
	
	
	
	public function public_check_status(){
		if(isset($_POST['check_status'])){
			$id = intval($_POST['cfeedbacks_message_id']);
			$url = strtok($_SERVER['HTTP_REFERER'], '?');
			$result = $this->check_status($id);
			if(strlen($result) > 0){
				$message = "?check_message=" . $result;
			}
			?>
			<script type="text/javascript">
				document.location.href="<?php echo $url . $message; ?>";
			</script>
			<?php
		}
		require_once('check_status_form.php');
	}
	
	
	
	private function check_status($id){
		$message = "";
		global $wpdb;
		$status_res = $wpdb->get_results("SELECT `cfeedbacks_status` FROM `".$this->tbl_cfeedbacks."` WHERE `id` = '".$id."'");
		if($status_res != null){
			if($status_res[0]->cfeedbacks_status == "new"){
				$status_message = esc_html__("Your feedback will be processing soon.", "cfeedbacks");
			}elseif($status_res[0]->cfeedbacks_status == "handling"){
				$status_message = esc_html__("Your feedback is under processing now.", "cfeedbacks");
			}elseif($status_res[0]->cfeedbacks_status == "ready"){
				$status_message = esc_html__("Your feedback has been reviewed. A response will be send to your email.", "cfeedbacks");
			}elseif($status_res[0]->cfeedbacks_status == "deleted"){
				$status_message = esc_html__("Your feedback was deleted. Try send your question one more time.", "cfeedbacks");
			}
			$message = esc_html__("Feedback status: ", "cfeedbacks") . $status_message;
		}else{
			$message = esc_html__("There is no feedback with that ID.", "cfeedbacks");
		}
		return $message;
	}
	
	
	
	private function add_cfeedbacks(){
		global $wpdb;
		$message = "";
		$cfeedbacks_title = sanitize_text_field($_POST['cfeedbacks_title']);
		$cfeedbacks_text = sanitize_text_field($_POST['cfeedbacks_text']);
		$cfeedbacks_user_name = sanitize_text_field($_POST['cfeedbacks_user_name']);
		$cfeedbacks_user_email = sanitize_email($_POST['cfeedbacks_user_email']);
		if(!is_email($cfeedbacks_user_email)){
			$message = esc_html__("You need to type a correct email address.", "cfeedbacks");
			return $message;
		}
		$cfeedbacks_status = 'new';
		
		$inputData = array(
			'cfeedbacks_title' => $cfeedbacks_title,
			'cfeedbacks_text' => $cfeedbacks_text,
			'cfeedbacks_user_name' => $cfeedbacks_user_name,
			'cfeedbacks_user_email' => $cfeedbacks_user_email,
			'cfeedbacks_status' => $cfeedbacks_status,
		);
		
		$result = $wpdb->insert($this->tbl_cfeedbacks, $inputData);
		if($result != false){
			$id = $wpdb->insert_id;
			$message = esc_html__("Your feedback ID: " . $id, "cfeedbacks");
		}
		
		/* add_filter('wp_mail_from_name', 'custom_wp_mail_from_name');
		function custom_wp_mail_from_name(){
			return '';
		} */
		
		$admin_email = get_option('admin_email');
		$sitename = get_option('blogname');
		
		$mail_message = "Name: " . $inputData['cfeedbacks_user_name'] . "\n";
		$mail_message .= "Email: " . $inputData['cfeedbacks_user_email'] . "\n";
		$mail_message .= "Message:\n" . $inputData['cfeedbacks_text'];
		
		$header = "MIME-Version: 1.0\n";
		$header .= "Content-Type: text/html; charset=utf-8\n";
		$header .= "From:" . $inputData['cfeedbacks_user_email'];
		wp_mail($admin_email, "Feedback was sent from " . $sitename, $mail_message, $header);
		return $message;
	}
}

global $cf;
$cf = new CitizensFeedbacks();
?>