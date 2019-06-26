<?php
get_header();
?>
<section id="primary" class="content-area">
	<main id="main" class="site-main">
		<?php
		/* Start the Loop */
		while ( have_posts() ) :
			the_post();
			?>
			<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
				<header class="entry-header">
					<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
				</header>
				<div class="entry-content">
					<?php the_excerpt() ?>
				</div>
				<div>
					<?php echo show_fields_after_registration( get_the_ID() ) ?>
				</div>
				<div>
					<?php echo request_additional_fields_button( get_the_ID() ); ?>
				</div>
			</article>
			<?php			
		endwhile; // End of the loop.
		?>
	</main>
</section>
<?php
get_footer();
