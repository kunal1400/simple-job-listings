<?php
get_header();

$selectedId = 0;
if( !empty($_GET['term_id']) ) {
	$selectedId = $_GET['term_id'];
}

// Job categories for dropdown
$jobCategories 	= get_terms( array(
    'taxonomy' => 'simple_job_categories',
    'hide_empty' => false,
) );

?>

<div class="container">
	<div class="row">
		<form method="get" class="form-inline" action="">
			<div class="form-group">
				<?php 
				if($jobCategories) {
					echo  '<label for="jobCategory">Category</label><select name="term_id">
					<option value="">Select</option>';
					foreach ($jobCategories as $i => $jobCategory) {
						if($selectedId == $jobCategory->term_id) {
							echo '<option selected value="'.$jobCategory->term_id.'">'.$jobCategory->name.'</option>';
						}
						else {
							echo '<option value="'.$jobCategory->term_id.'">'.$jobCategory->name.'</option>';	
						}
					}
					echo '</select>';
				}
				?>
			</div>			
			<button type="submit" class="btn btn-default">Filter</button>
		</form>
	</div>
</div>

<?php 
// the query
$args = array('post_type' => 'simple_jobs');
if($selectedId != 0) {
	$args['tax_query'] = array( array(
			'taxonomy' => 'simple_job_categories',
			'field'    => 'term_id',
			'terms'    => array($selectedId),
			'operator' => 'IN',
		));
}

$the_query = new WP_Query( $args ); 
?>
<section id="primary" class="container">
	<main id="main" class="row">
		<?php if ( $the_query->have_posts() ) : ?>
			<?php while ( $the_query->have_posts() ) : $the_query->the_post(); ?>
				<article id="post-<?php the_ID(); ?>" class="col-md-12">
					<div><?php echo show_common_fields( get_the_ID() ) ?></div>
					<div class="commonFieldsSection">
						<?php echo show_fields_after_registration( get_the_ID() ) ?>
					</div>
					<div class="requestedFieldsSection">
						<?php echo request_additional_fields_button( get_the_ID() ); ?>
					</div>
				</article>
			<?php endwhile; ?>
			<?php wp_reset_postdata(); ?>
		<?php else : ?>
			<p><?php esc_html_e( 'Sorry, no posts matched your criteria.' ); ?></p>
		<?php endif; ?>
	</main>
</section>
<?php
get_footer();
