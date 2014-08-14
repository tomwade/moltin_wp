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

					<div class="row">
						<?php foreach($categories as $category) { ?>
							<?php moltin_get_template_part('category', 'listing', array('category' => $category)); ?>
						<?php } ?>
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