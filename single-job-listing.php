<?php

if( !is_user_logged_in() ) {
	wp_redirect( home_url() );
	exit;
}

get_header();
?>
<section id="jobDataSection" class="container">
	<main id="jobDataMain" class="row">
		<?php
		/* Start the Loop */
		while ( have_posts() ) :
			the_post();
			?>
			<article id="post-<?php the_ID(); ?>" class="col-md-12 jobData">
				<div class="commonFieldsSection"><?php echo show_common_fields( get_the_ID() ) ?></div>
				<div class="approvedFieldsSection">
					<?php echo show_fields_after_registration( get_the_ID() ) ?>
				</div>
				<div class="requestedFieldsSection">
					<?php echo request_additional_fields_button( get_the_ID() ); ?>
				</div>			
			</article>			
		<?php
			// If comments are open or we have at least one comment, load up the comment template.
			if ( comments_open() || get_comments_number() ) {
				comments_template();
			}
		endwhile; // End of the loop.
		?>
	</main>
</section>
<?php
get_footer();
