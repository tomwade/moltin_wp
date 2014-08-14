<?php get_header(); ?>

<div id="main-content" class="main-content">

	<div id="primary" class="content-area">
		<div id="content" class="site-content" role="main">

			<article>
				<header class="entry-header">
					<h1 class="entry-title"><?php echo get_the_title(); ?></h1>
				</header>

				<?php echo moltin_breadcrumb(); ?>

				<?php echo moltin_render_messages(); ?>

				<div class="entry-content">
					<p><?php echo $post->post_content; ?></p>

					<?php if($add) { ?>
						<p><?php echo $add; ?></p>
					<?php } ?>

					<form action="" method="post">

						<table class="shop_table cart" cellspacing="0">
							<thead>
								<tr>
									<th class="product-remove">&nbsp;</th>
									<th class="product-thumbnail">&nbsp;</th>
									<th class="product-name"><?php _e( 'Product', 'woocommerce' ); ?></th>
									<th class="product-price"><?php _e( 'Price', 'woocommerce' ); ?></th>
									<th class="product-quantity"><?php _e( 'Quantity', 'woocommerce' ); ?></th>
									<th class="product-subtotal"><?php _e( 'Total', 'woocommerce' ); ?></th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ( $cart['contents'] as $cart_item_key => $cart_item ) { ?>
									<tr>
										<td class="product-remove">
											<a href="?remove_item=<?php echo $cart_item_key; ?>">Remove</a>
										</td>

										<td class="product-thumbnail">
											<?php if(count($cart_item['images'])) { ?>
												<?php $first_image = reset($cart_item['images']); ?>

												<img src="<?php echo $first_image['url']['http']; ?>" alt="<?php echo esc_attr($cart_item['title']); ?>" title="<?php echo esc_attr($cart_item['title']); ?>" width="40" height="40" />
											<?php } ?>
										</td>

										<td class="product-name">
											<?php echo $cart_item['title']; ?>
										</td>

										<td class="product-price">
											<?php echo $cart_item['pricing']['formatted']['without_tax']; ?>
										</td>

										<td class="product-quantity">
											<input type="hidden" name="original_qty[<?php echo $cart_item_key; ?>]" value="<?php echo $cart_item['quantity']; ?>" />
											<input type="text" size="2" name="update_qty[<?php echo $cart_item_key; ?>]" value="<?php echo $cart_item['quantity']; ?>" style="text-align: center" />
										</td>

										<td class="product-subtotal">
											<?php echo moltin_currency_format($cart_item['total_before_tax']); ?>
										</td>
									</tr>
								<?php } ?>

								<tr>
									<td colspan="6" class="actions">

										<span style="float: right">
											<input type="submit" class="button" name="update_cart" value="<?php _e( 'Update Cart', 'woocommerce' ); ?>" /> <input type="submit" class="checkout-button button alt wc-forward" name="checkout_cart" value="<?php _e( 'Proceed to Checkout', 'woocommerce' ); ?>" />

											<?php wp_nonce_field( 'moltin-nonce', 'moltin-cart' ); ?>
										</span>

										<?php if(1 == 2) { ?>
										<div class="coupon">

											<label for="coupon_code"><?php _e( 'Coupon', 'woocommerce' ); ?>:</label> <input type="text" name="coupon_code" class="input-text" id="coupon_code" value="" placeholder="<?php _e( 'Coupon code', 'woocommerce' ); ?>" /> <input type="submit" class="button" name="apply_coupon" value="<?php _e( 'Apply Coupon', 'woocommerce' ); ?>" />

											<?php do_action('woocommerce_cart_coupon'); ?>

										</div>
										<?php } ?>
									</td>
								</tr>
							</tbody>
						</table>

					</form>

					<div class="cart-collaterals">

						<div class="cart_totals">

							<h2><?php _e( 'Cart Totals', 'woocommerce' ); ?></h2>

							<table cellspacing="0">

								<tr class="cart-subtotal">
									<th><?php _e( 'Cart Subtotal', 'woocommerce' ); ?></th>
									<td><?php echo moltin_currency_format($cart['total_before_tax']); ?></td>
								</tr>

								<tr>
									<th>Tax</th>
									<td><?php echo moltin_currency_format($cart['total'] - $cart['total_before_tax']); ?></td>
								</tr>

								<tr>
									<th>Shipping</th>
									<td><?php echo moltin_currency_format($shipping_price); ?></td>
								</tr>


								<tr class="order-total">
									<th>Total</th>
									<td><?php echo moltin_currency_format($cart['total'] + floatval($shipping_price)); ?></td>
								</tr>

							</table>

						</div>

					</div>

				</div>
			</article>


		</div>
	</div>
	<?php get_sidebar( 'content' ); ?>
</div>

<?php
get_sidebar();
get_footer();