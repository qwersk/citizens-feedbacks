<?php
if(!defined('ABSPATH')) exit;
?>
<div class="wrap">
<form action="admin.php?page=edit-cfeedbacks&action=submit" method="POST">
	<h2><?php esc_html_e("Edit feedback", "cfeedbacks"); ?></h2>
	<table class="widefat">
		<tr>
			<td><?php esc_html_e("ID:", "cfeedbacks"); ?></td>
			<td><?php echo $this->data['cfeedbacks']['id']; ?></td>
		</tr>
		<tr>
			<td><label for="cfeedbacks_user_name"><?php esc_html_e("Name:", "cfeedbacks"); ?></label></td>
			<td><input type="text" name="cfeedbacks_user_name" class="large-text" id="cfeedbacks_user_name" value="<?php echo esc_attr($this->data['cfeedbacks']['cfeedbacks_user_name']); ?>" required /></td>
		</tr>
		<tr>
			<td><label for="cfeedbacks_user_email"><?php esc_html_e("Email:", "cfeedbacks"); ?></label></td>
			<td><input type="email" name="cfeedbacks_user_email" class="large-text" id="cfeedbacks_user_email" value="<?php echo esc_attr($this->data['cfeedbacks']['cfeedbacks_user_email']); ?>" required /></td>
		</tr>
		<tr>
			<td><label for="cfeedbacks_title"><?php esc_html_e("Subject:", "cfeedbacks"); ?></label></td>
			<td><input type="text" name="cfeedbacks_title" class="large-text" id="cfeedbacks_title" value="<?php echo esc_attr($this->data['cfeedbacks']['cfeedbacks_title']); ?>" required /></td>
		</tr>
		<tr>
			<td><label for="cfeedbacks_text"><?php esc_html_e("Message:", "cfeedbacks"); ?></label></td>
			<td><textarea name="cfeedbacks_text" class="large-text" id="cfeedbacks_text" rows="10" required ><?php echo esc_textarea($this->data['cfeedbacks']['cfeedbacks_text']); ?></textarea></td>
		</tr>
		<tr>
			<td><label for="cfeedbacks_status"><?php esc_html_e("Feedback status:", "cfeedbacks"); ?></label></td>
			<td>
				<select name="cfeedbacks_status" id="cfeedbacks_status">
					<?php
						foreach($this->cfeedback_status_arr as $status){
							$selected = ($this->data['cfeedbacks']['cfeedbacks_status'] == $status) ? "selected" : "";
					?>
						<option value="<?php echo esc_attr($status); ?>" <?php echo esc_attr($selected); ?>><?php echo ucfirst(esc_html($status)); ?></option>
					<?php
						}
					?>
				</select>
			</td>
		</tr>
		<tr>
			<td>
				<input type="hidden" name="id" value="<?php echo esc_html($this->data['cfeedbacks']['id']); ?>" />
				<input type="submit" class="button" name="send" value="<?php esc_html_e("Edit", "cfeedbacks"); ?>" />
			</td>
		</tr>
	</table>
</form>

<form action="admin.php?page=edit-cfeedbacks&action=send_response" method="POST">
	<h2><?php echo esc_html__("Send a response", "cfeedbacks"); ?></h2>
	<table class="widefat form-table">
		<tr>
			<td>
				<textarea name="cfeedbacks_response" class="large-text" id="cfeedbacks_response" rows="10" placeholder="<?php esc_html_e("Type a response", "cfeedbacks"); ?>" required></textarea>
				<input type="hidden" name="email_to_response" value="<?php echo esc_attr($this->data['cfeedbacks']['cfeedbacks_user_email']); ?>" />
			</td>
		</tr>
		<tr>
			<td><input type="submit" class="button" name="send_response" value="<?php esc_html_e("Send", "cfeedbacks"); ?>" /></td>
		</tr>
	</table>
</form>
</div>
