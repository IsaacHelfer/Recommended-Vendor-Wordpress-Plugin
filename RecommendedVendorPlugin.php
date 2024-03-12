<?php

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