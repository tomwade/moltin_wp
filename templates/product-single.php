<?php get_header(); ?>

<div id="main-content" class="main-content">

	<div id="primary" class="content-area">
		<div id="content" class="site-content" role="main">

			<article>
				<header class="entry-header">
					<h1 class="entry-title"><?php echo $product['title']; ?></h1>
				</header>

				<?php echo moltin_breadcrumb(); ?>

				<?php echo moltin_render_messages(); ?>

				<div class="entry-content">


			<!-- Product Detail -->
	            <div class="row">
            		<div class="sp-wrap col-sm-3">
						<?php if(product_has_images($product)) { ?>
							<?php foreach($product['images'] as $image_id => $i) { ?><a href="<?php echo $i['url']['http']; ?>"><img src="<?php echo $i['url']['http']; ?>" alt="<?php echo esc_attr($i['name']); ?>" title="<?php echo esc_attr($i['name']); ?>" alt=""></a><?php } ?>
						<?php } ?>
					</div>
					<div class="col-sm-9">
						<h2><?php echo $product['title']; ?></h2>
						<?php echo $product['stock_level']; ?> in stock

						<?php if($product['stock_status']['value'] == 'Out of Stock' || $product['stock_status']['value'] == 'More Stock Ordered' || ($product['stock_status']['value'] != 'Unlimited' && $product['stock_level'] == 0)) { ?>
							&middot; Out of stock
						<?php } else { ?>
							<?php if($product['stock_status']['value'] != 'Unlimited') { ?>
								&middot; <?php echo $product['stock_status']['value']; ?>
							<?php } ?>
						<?php } ?>
						<hr/>
						<p><?php echo nl2br($product['description']); ?></p>
						<hr/>
						<h3><?php echo $product['pricing']['formatted']['without_tax']; ?></h3>
						<div class="input-qty-detail">

							<form method="post" action="<?php echo site_url('store/cart/add'); ?>">
								<input type="hidden" name="id" value="<?php echo $product['id']; ?>" />
								<input type="hidden" name="redirect" value="<?php echo product_link($product); ?>" />

								<?php if($variations = product_has_variations($product)) { ?>
									<div class="variations">
										<?php foreach($variations as $v_id => $v) { ?>
											<div class="variant form-group">
												<label for="<?php echo $v_id; ?>"><?php echo $v['title']; ?></label>

												<select name="modifier[<?php echo $v_id; ?>]" id="<?php echo $v_id; ?>" class="form-control">
													<?php foreach($v['variations'] as $v_opt_id => $v_opt) { ?>
														<option value="<?php echo $v_opt_id; ?>"><?php echo $v_opt['title']; ?><?php echo ($v_opt['mod_price'] != '+0.00') ? ' (' . $v_opt['difference'] . ')' : ''; ?></option>
													<?php } ?>
												</select>
										<?php } ?>
									</div>
								<?php } ?>

								<?php wp_nonce_field( 'moltin-product-add', 'moltin-nonce' ); ?>

								<?php if($product['stock_status']['value'] == 'Out of Stock' || $product['stock_status']['value'] == 'More Stock Ordered' || ($product['stock_status']['value'] != 'Unlimited' && $product['stock_level'] == 0)) { ?>

								<?php } else { ?>
									<input type="text" name="quantity" value="1" class="form-control input-qty text-center" /> <input type="submit" class="btn btn-primary pull-left" value="Add to cart" />
								<?php } ?>
							</form>
						</div>
					</div>
	            </div>

	            <p>&nbsp;</p>

	            <div class="clearfix"></div>

	            <?php if($related) { ?>
		            <div class="col-lg-12 col-sm-12">
	            		<h2 class="title">RELATED PRODUCTS</h2>
	            	</div>
	            	
	            	<div class="row">
	           			<?php foreach($related as $product) { ?>
							<?php moltin_get_template_part('product', 'listing', array('product' => $product)); ?>
						<?php } ?>
	            	</div>
            	<?php } ?>
            </div>
        	<!-- End Product Detail -->


			</article>

		</div>
	</div>
	<?php get_sidebar( 'content' ); ?>
</div>

<?php
get_sidebar();
get_footer();
