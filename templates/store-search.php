<?php get_header(); ?>

<div id="main-content" class="main-content">

	<div id="primary" class="content-area">
		<div id="content" class="site-content" role="main">

			<article>
				<header class="entry-header">
					<h1 class="entry-title">Search Results</h1>
				</header>

				<?php echo moltin_breadcrumb(); ?>

				<?php echo moltin_render_messages(); ?>

				<div class="entry-content">
					<p>Your search results are displayed below</p>

					<div class="row">
						<?php foreach($products as $product) { ?>
							<?php moltin_get_template_part('product', 'listing', array('product' => $product)); ?>
						<?php } ?>
					</div>

					<?php echo moltin_paginate($pagination); ?>
				</div>
			</article>


		</div>
	</div>
	<?php get_sidebar( 'content' ); ?>
</div>

<?php
get_sidebar();
get_footer();
