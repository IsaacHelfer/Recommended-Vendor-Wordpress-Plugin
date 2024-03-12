<?php

class MetaBox
{
	public function __construct()
	{
		add_action( 'add_meta_boxes', array( $this, 'create_meta_box' ) );

		add_action( 'save_post', array($this, 'update_data') );
	}

	public function create_meta_box()
	{
		if ( empty( $_GET ) ) {
			return;
		}

		add_meta_box( 'change_vendor_data', 'Change Vendor Data', array( $this, 'render_html' ), 'vendor' );
	}

	public function render_html()
	{ 
		global $post;

		$drop_down_items = array( 'Aviation', 'Healthcare', 'Enviroment', 'Technology' );

		$post_id = $post->ID;

		?>
			<style>
				#poststuff .inside {
				    display: flex;
				    flex-direction: column;
				    gap: 0.5rem;
				}
			</style>

			<form method="POST">
				<label>Vendor Name:</label>
				<input type="text" value="<?php echo get_post_meta( $post_id, 'vendor_name', true ) ?>" name="vendor_name" required>

				<label>Phone Number:</label>
				<label><small>Format: 000-000-0000</small></label>
				<input type="tel" value="<?php echo get_post_meta( $post_id, 'phone_num', true ) ?>" name="phone_num" pattern="[0-9]{3}-[0-9]{3}-[0-9]{4}" required>

				<label>Email:</label>
				<input type="email" value="<?php echo get_post_meta( $post_id, 'email', true ) ?>" name="email" required>

				<label>Website:</label>
				<input type="text" value="<?php echo get_post_meta( $post_id, 'website_url', true ) ?>" name="wesbite_url" required>

				<label>Industry:</label>
				<select id="industry" name="industry" required>
					<?php foreach ($drop_down_items as $item) :?>
						<option <?php if ( $item === get_post_meta( $post_id, 'industry', true ) ) { echo 'selected'; } ?>>
							<?php echo $item; ?>
						</option>
					<?php endforeach; ?>
				</select>

				<label>Why do you recommend this vendor?</label>
				<textarea id="why" name="why" required><?php echo get_post_meta( $post_id, 'why', true ) ?></textarea>
			</form>
		<?php
	}

	public function update_data()
	{
		global $post;

		$meta_keys = array( 'vendor_name', 'phone_num', 'email', 'website_url', 'industry', 'why' );

		// get the meta values needed from $_POST
		if ( ! empty( $_POST ) ) {
			foreach ( $meta_keys as $key ) {
				if ( array_key_exists( $key, $_POST ) ) {
					update_post_meta( $post->ID, $key, trim( $_POST[$key] ) );
				}
			}
		}
	}
} 