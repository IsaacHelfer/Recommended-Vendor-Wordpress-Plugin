<?php

class ChangeVendorDataMetaBox
{
	public function __construct()
	{
		add_action( 'add_meta_boxes', array( $this, 'create_meta_box' ) );

		add_action( 'save_post', array($this, 'test') );
	}

	public function test()
	{
		echo 'test';
		die;
	}

	public function create_meta_box()
	{
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
				<input type="text" value="<?php echo get_post_meta( $post_id, 'vendor_name' )[0] ?>" name="vendor_name">

				<label>Phone Number:</label>
				<input type="tel" value="<?php echo get_post_meta( $post_id, 'phone_num' )[0] ?>" name="phone_num">

				<label>Email:</label>
				<input type="email" value="<?php echo get_post_meta( $post_id, 'email' )[0] ?>" name="email">

				<label>Website:</label>
				<input type="text" value="<?php echo get_post_meta( $post_id, 'website_url' )[0]  ?>" name="wesbite_url">

				<label>Industry:</label>
				<select id="industry" name="industry" required>
					<?php foreach ($drop_down_items as $item) :?>
						<option <?php if ( $item === get_post_meta( $post_id, 'industry' )[0] ) { echo 'selected'; } ?>>
							<?php echo $item; ?>
						</option>
					<?php endforeach; ?>
				</select>

				<label>Why do you recommend this vendor?</label>
				<textarea id="why" name="why" required><?php echo get_post_meta( $post_id, 'why' )[0]?></textarea>
			</form>
		<?php
	}

	public function update_data( $id, $meta_key, $meta_value)
	{
		update_post_meta( $id, $meta_key, $meta_value );
	}
} 