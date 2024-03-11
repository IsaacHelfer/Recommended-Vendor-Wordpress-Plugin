<?php

/**
 * Plugin Name: Recommended Vendor
 * Description: This plugin is a form for users to fill out and give feedback as to why they recommend a certain vendor
 * Version: 1.0.0
 * Author: Isaac Helfer
*/

// Exit if accessed directly
if ( ! defined('ABSPATH') ) 
{
	exit;
}

class RecommendedVendorPlugin
{
	public $testing = true;

	public function __construct() 
	{
		require ABSPATH . 'wp-includes/pluggable.php';

		add_action( 'init', array( $this, 'create_custom_post_type' ) );

		add_shortcode( 'vendor-form', array( $this, 'make_vendor_form' ) );
	}

	public function create_custom_post_type()
	{
		$args = array(
			'public' => true,
			'has_archive' => true,
			'supports' => array( 'title' ),
			'exclude_from_search' => true,
			'publicly_queryable' => false,
			'capability' => 'manage_options',
			'labels' => array( 
				'name' => 'Vendors',
				'singular_name' => 'Vendor'
			),
			'menu_icon' => 'dashicons-media-text'
		);

		register_post_type( 'vendor' , $args );
	}

	public function make_vendor_form()
	{ ?>
		<style>
			form {
				display: flex;
				flex-direction: column;
				gap: 1rem;
			}

			select {
				width: fit-content;
			}
		</style>

		<script>
			function getRandomDataFromArray(arr) {
				return arr[Math.floor(Math.random() * arr.length)];
			}

			function fillForm() {
				const vendorName = ['McDonalds', 'Wendys', 'Sheetz', 'Burger King', 'Pizza Hut'];

				document.getElementById("vendorName").value = getRandomDataFromArray(vendorName);
				document.getElementById("phone_num").value = "123-456-7890";
				document.getElementById("email").value = "test@vendor.com";
				document.getElementById("website").value = "test-website.com";
				document.getElementById("industry").value = "Aviation";
				document.getElementById("why").value = "Cause I'm Testing";
			}
		</script>

		<div>
			<div>
				<h1>Recommend a Vendor:</h1>
			<div>

			<div>
				<form method="POST">
					<?php if ($this->testing) : ?>
						<div>
							<button type="button" onclick="fillForm()">Fill Form (testing)</button>
						</div>
					<?php endif; ?>

					<div>
						<label>Vendor Name:</label>
						<input type="text" id="vendorName" name="vendor_name" required>
					</div>

					<div>
						<label>Phone Number:</label>
						<input type="tel" id="phone_num" name="phone_num" pattern="[0-9]{3}-[0-9]{3}-[0-9]{4}" required>
					</div>

					<div>
						<label>Email:</label>
						<input type="email" id="email" name="email" required>
					</div>

					<div>
						<label>Website:</label>
						<input type="text" id="website" name="website_url" required>
					</div>

					<div>
						<label>Industry:</label>

						<br>

						<select id="industry" name="industry" required>
							 <option disabled selected value>-- select an option --</option>
							<option>Aviation</option>
							<option>Healthcare</option>
							<option>Enviroment</option>
							<option>Technology</option>
						</select>
					</div>

					<div>
						<label>Why do you recommend this vendor?</label>
						<textarea id="why" name="why" required></textarea>
					</div>

					<div>
						<input type="hidden" value="pending" name="status">
					</div>

					<div>
						<input type="submit" value="Submit">
					</div>
				</form>
			</div>
		</div>
	<?php }

	public function make_post( $vendor_name, $content ) : int
	{
		$post = array(
		'post_title'   => 'Vendor Data: ' . $vendor_name,
		'post_content' => $content,
		'post_status'  => 'publish',
		'post_author'  => 1,
		'post_type'    => 'vendor'
		);

		$id = wp_insert_post( $post );

		return $id;
	}
}

class ChangeVendorDataMetaBox
{
	public function __construct()
	{
		add_action( 'add_meta_boxes', array( $this, 'create_meta_box' ) );
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
				<input type="text" value="<?php echo get_post_meta( $post_id, 'vendor_name' )[0] ?>">

				<label>Phone Number:</label>
				<input type="tel" value="<?php echo get_post_meta( $post_id, 'phone_num' )[0] ?>">

				<label>Email:</label>
				<input type="email" value="<?php echo get_post_meta( $post_id, 'email' )[0] ?>">

				<label>Website:</label>
				<input type="text" value="<?php echo get_post_meta( $post_id, 'website_url' )[0]  ?>">

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
} 

$vendorPlugin = new RecommendedVendorPlugin();
$metaBox = new ChangeVendorDataMetaBox();

// if vendor form submited
if ( ! empty( $_POST ) && $_SERVER['REQUEST_URI'] === '/form/' ) {
	// Prevents putting empty data in the custom post type
	if ( array_key_exists( 'vendor_name', $_POST ) ) {
		// make the post
		$post_id = $vendorPlugin->make_post( $_POST['vendor_name'], implode( '<br>', $_POST ) );
		
		// add data to post_meta
		foreach ( $_POST as $form_data ) {
			add_post_meta( $post_id, array_search( $form_data, $_POST ), $form_data );
		}

		// redirect the user to the vendors page
		wp_redirect( 'test-website/vendors' );

		exit;
	}
}

