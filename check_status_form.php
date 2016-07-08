<?php
if(!defined('ABSPATH')) exit;

if(isset($_GET['check_message'])){
	echo "<div class='show_cfeedbacks_message'>" . esc_html($_GET['check_message']) . "</div>";
}

?>
<div class="cfeedbacks-status-form" id="cfeedbacks-status-form">
	<form method="POST" action="">
		<span class="cfeedbacks-status-form-control-wrap"><input type="text" name="cfeedbacks_message_id" required /></span>
		<span class="cfeedbacks-status-form-control-wrap"><input type="submit" name="check_status" value="<?php esc_html_e("Check", "cfeedbacks"); ?>" /></span>
	</form>
</div>