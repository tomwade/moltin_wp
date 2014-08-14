<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

get_header();

$fields = moltin_call('fields', 'address');

unset($fields['customer']);
unset($fields['save_as']);
?>

<div id="main-content" class="main-content">

	<div id="primary" class="content-area">
		<div id="content" class="site-content" role="main">

			<article>
				<header class="entry-header">
					<h1 class="entry-title">Checkout</h1>
				</header>

				<?php echo moltin_breadcrumb(); ?>

				<?php echo moltin_render_messages(); ?>

				<div class="entry-content">

					<form name="checkout" method="post" class="checkout" action="">

						<div style="margin-bottom: 2em; display: block; overflow: hidden">

							<div style="float: left; width: 50%">

								<h3><?php _e( 'Billing Information', 'woocommerce' ); ?></h3>

								<?php foreach($fields as $f_name => $f_data) { ?>
									<div class="form-group">
										<label for="billing_<?php echo $f_name; ?>"><?php echo $f_data['name']; ?></label>
										<?php echo set_address_post_array($f_data['input'], 'billing'); ?>
									</div>
								<?php } ?>

								<?php if(!$user) { ?>
									<label for="create_user"><input type="checkbox" name="create_user" id="create_user" /> Create user with these details</label>
								<?php } ?>

							</div>

							<div style="float: right; width: 50%">

								<div style="overflow: hidden; display: block;">
									<h3 style="float: left;"><?php _e( 'Shipping Information', 'woocommerce' ); ?></h3>

									<span style="float: right">
										<label for="ship_to_billing"><input type="checkbox" name="ship_to_billing" id="ship_to_billing" /> Ship to billing address</label>
									</span>
								</div>

								<?php foreach($fields as $f_name => $f_data) { ?>
									<div class="form-group">
										<label for="shipping_<?php echo $f_name; ?>"><?php echo $f_data['name']; ?></label>
										<?php echo set_address_post_array($f_data['input'], 'shipping'); ?>
									</div>
								<?php } ?>

							</div>

						</div>

						<h3><?php _e( 'Shipping Method', 'woocommerce' ); ?></h3>

						<?php foreach($shipping as $s) { ?>
							<input type="radio" name="shipping_method" value="<?php echo $s['id']; ?>" <?php echo (count($shipping) == 1) ? 'checked' : ''; ?> /> <?php echo $s['title']; ?> (£<?php echo number_format($s['price'], 2); ?>) - <?php echo $s['description']; ?><br />
						<?php } ?>

						<h3><?php _e( 'Payment Method', 'woocommerce' ); ?></h3>

						<?php foreach($payment as $p) { ?>
							<input type="radio" name="payment_method" value="<?php echo $p['slug']; ?>" /> <?php echo $p['name']; ?><br />
						<?php } ?>

						<h3><?php _e( 'Payment Details', 'woocommerce' ); ?></h3>

						<div class="card-wrapper"></div>

		                <input placeholder="Card number" type="text" name="number">
		                <input placeholder="Full name" type="text" name="name">
		                <input placeholder="MM/YY" type="text" name="expiry">
		                <input placeholder="CVC" type="text" name="cvc">

						<script type="text/javascript">
						jQuery(function($) {

							$('form.checkout').card({
							    // a selector or jQuery object for the container
							    // where you want the card to appear
							    container: '.card-wrapper', // *required*

							    // Strings for translation - optional
							    messages: {
							        validDate: 'expiry\ndate', // optional - default 'valid\nthru'
							    },

							    // Default values for rendered fields - options
							    values: {
							        number: '•••• •••• •••• ••••',
							        name: 'Full Name',
							        expiry: '••/••',
							        cvc: '•••'
							    }
							});
						});
						</script>

						<?php wp_nonce_field( 'moltin-checkout', 'moltin-nonce' ); ?>

						<input type="submit" name="confirm_order" value="Confirm Order" style="float: right" />

					</form>
				</div>
			</article>


		</div>
	</div>
	<?php get_sidebar( 'content' ); ?>
</div>

<?php
get_sidebar();
get_footer();