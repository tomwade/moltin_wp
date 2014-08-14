<?php get_header(); ?>

<div id="main-content" class="main-content">

	<div id="primary" class="content-area">
		<div id="content" class="site-content" role="main">

			<article>
				<header class="entry-header">
					<h1 class="entry-title">Cart Failure</h1>
				</header>

				<?php echo moltin_breadcrumb(); ?>

				<?php echo moltin_render_messages(); ?>

				<div class="entry-content">

					<p>Sorry but there was an issue whilst taking payment for your order. Please return to the checkout and try again.</p>
				
				</div>
			</article>


		</div>
	</div>
	<?php get_sidebar( 'content' ); ?>
</div>

<?php
get_sidebar();
get_footer();