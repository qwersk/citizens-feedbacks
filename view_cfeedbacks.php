<?php
if(!defined('ABSPATH')) exit;

global $wpdb;

$query_by_status = "";
if(isset($_GET['status']) && $_GET['status'] != "all"){
	$status = sanitize_text_field($_GET['status']);
	$query_by_status = "WHERE `cfeedbacks_status` = '".$status."'";
}
$pagenum = isset( $_GET['pagenum'] ) ? absint( $_GET['pagenum'] ) : 1;
$limit   = 10;
$offset  = ( $pagenum - 1 ) * $limit;
$entries = $wpdb->get_results( "SELECT * FROM `".$this->tbl_cfeedbacks."` ".$query_by_status." ORDER BY `id` DESC LIMIT $offset, $limit" );
 
?>
<div class="wrap">

<?php if(isset($_GET['action']) && $_GET['action'] == 'send_response'){ ?>
	<div class="updated notice">
		<p><?php esc_html_e("Response was sent successfully", "cfeedbacks"); ?></p>
	</div>
<?php } ?>
	<h2><?php esc_html_e("Feedback management", "cfeedbacks"); ?></h2>
		<ul class="cfeedbacks-status-list subsubsub">
			<li>
				<?php $current = (isset($_GET['status']) && $_GET['status'] == "all") ? "class='current'" : ""; ?>
				<a href="admin.php?page=edit-cfeedbacks&status=all" <?php echo $current; ?>><?php esc_html_e("All", "cfeedbacks"); ?></a>
			</li>
			<?php foreach($this->cfeedback_status_arr as $status_list){ ?>
				<?php $current = (isset($_GET['status']) && $_GET['status'] == $status_list) ? "class='current'" : ""; ?>
				<li><a href="admin.php?page=edit-cfeedbacks&status=<?php echo $status_list; ?>" <?php echo $current; ?>><?php echo ucfirst($status_list); ?></a></li>
			<?php } ?>
		</ul>
		<table class="widefat">
			<tr class="alternate">
				<th><?php esc_html_e("ID", "cfeedbacks"); ?></th>
				<th><?php esc_html_e("Name", "cfeedbacks"); ?></th>
				<th><?php esc_html_e("Date", "cfeedbacks"); ?></th>
				<th><?php esc_html_e("Feedback status", "cfeedbacks"); ?></th>
				<th><?php esc_html_e("Actions", "cfeedbacks"); ?></th>
			</tr>
			<?php
				if($entries){
					$count = 1;
					foreach($entries as $entry){
			?>
					<tr class="alternate">
						<td><?php echo esc_html($entry->id); ?></td>
						<td><?php echo esc_html($entry->cfeedbacks_user_name); ?>, <a href="mailto:<?php echo $entry->cfeedbacks_user_email; ?>"><?php echo $entry->cfeedbacks_user_email; ?></a></td>
						<td><?php echo esc_html($entry->cfeedbacks_date); ?></td>
						<td><?php echo esc_html($entry->cfeedbacks_status); ?></td>
						<td><a href="admin.php?page=edit-cfeedbacks&action=edit&id=<?php echo esc_html($entry->id); ?>"><?php esc_html_e("Edit", "cfeedbacks"); ?></a> | <a href="admin.php?page=edit-cfeedbacks&action=delete&id=<?php echo esc_html($entry->id); ?>" onclick="return confirm('<?php echo esc_js("Do you really want to delete this feedback?"); ?>');"><?php esc_html_e("Delete", "cfeedbacks"); ?></a></td>
					</tr>
					<tr>
						<td><?php esc_html_e("Message:", "cfeedbacks"); ?></td>
						<td colspan="4"><strong><?php echo esc_html($entry->cfeedbacks_title); ?></strong><br /><?php echo esc_textarea($entry->cfeedbacks_text); ?></td>
					</tr>
			<?php
					$count++;
					}
				}else{
			?>
					<tr>
						<td colspan="4" style="text-align:center"><?php esc_html_e("Feedbacks not found", "cfeedbacks"); ?></td>
					</tr>
			<?php
				}
			?>
		</table>
		<?php
		$total        = $wpdb->get_var( "SELECT COUNT(`id`) FROM `".$this->tbl_cfeedbacks."`" );
		$num_of_pages = ceil( $total / $limit );
		$page_links   = paginate_links( array(
			'base'      => add_query_arg( 'pagenum', '%#%' ),
			'format'    => '',
			'prev_text' => esc_html__( '&laquo;', 'appeal-form' ),
			'next_text' => esc_html__( '&raquo;', 'appeal-form' ),
			'total'     => $num_of_pages,
			'current'   => $pagenum
		) );
		 
		if ( $page_links ) {
			echo '<div class="tablenav-pages">' . $page_links . '</div>';
		}
	?>	
</div>