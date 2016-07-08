<?php
if(!defined('ABSPATH')) exit;

if(isset($_GET['add_message'])){
	echo "<div class='show_cfeedbacks_message'>" . esc_html($_GET['add_message']) . "</div>";
}

?>
<div class="cfeedbacks-form" id="cfeedbacks-form">
	<form action="" method="POST">
		<p><label for="cfeedbacks_user_name"><?php esc_html_e("Your name:", "cfeedbacks"); ?></label></p>
		<span class="cfeedbacks-form-control-wrap"><input type="text" name="cfeedbacks_user_name" id="cfeedbacks_user_name" required /></span>
		<p><label for="cfeedbacks_user_email"><?php esc_html_e("Your email:", "cfeedbacks"); ?></label></p>
		<span class="cfeedbacks-form-control-wrap"><input type="email" name="cfeedbacks_user_email" id="cfeedbacks_user_email" required /></span>
		<p><label for="cfeedbacks_title"><?php esc_html_e("Subject:", "cfeedbacks"); ?></label></p>
		<span class="cfeedbacks-form-control-wrap"><input type="text" name="cfeedbacks_title" id="cfeedbacks_title" required /></span>
		<p><label for="cfeedbacks_text"><?php esc_html_e("Message:", "cfeedbacks"); ?></label></p>
		<span class="cfeedbacks-form-control-wrap"><textarea name="cfeedbacks_text" id="cfeedbacks_text" rows="3" cols="3" required ></textarea></span>
		<input type="hidden" name="action" value="add-cfeedbacks" />
		<p><input type="submit" name="send" value="<?php esc_html_e("Send feedback", "cfeedbacks"); ?>" /></p>
	</form>
</div>