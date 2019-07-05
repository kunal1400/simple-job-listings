<?php
if( !is_user_logged_in() ) {
	wp_redirect( home_url() );
	exit;
}

get_header();

$selectedId = 0;
if( !empty($_GET['term_id']) ) {
	$selectedId = $_GET['term_id'];
}

// Job categories for dropdown
$jobCategories 	= get_terms( array(
    'taxonomy' => 'simple_job_categories',
    'hide_empty' => false,
    'orderby' => 'count', 
    'order' => 'DESC'
) );

// the query
$args = array('post_type' => 'simple_jobs');
if($selectedId != 0) {
	$args['tax_query'] = array( array(
			'taxonomy' => 'simple_job_categories',
			'field'    => 'term_id',
			'terms'    => array($selectedId),
			//'operator' => 'IN',
		));
}

$the_query = new WP_Query( $args ); 
?>

<div class="container">
	<div class="content">
		<div class="row">
			<div class="col-md-4">
				<div class="border padding15">
	  				<h1>Filter By Category</h1>
	  				<?php 
					if($jobCategories) {					
						foreach ($jobCategories as $i => $jobCategory) {
							if($selectedId == $jobCategory->term_id) {
								echo '<div class="activeCatergory">
									<a href="'.site_url("jobs").'?term_id='.$jobCategory->term_id.'">'.$jobCategory->name.' ('.$jobCategory->count.')</a>
								</div>';
							}
							else {
								echo '<div>
									<a href="'.site_url("jobs").'?term_id='.$jobCategory->term_id.'">'.$jobCategory->name.' ('.$jobCategory->count.')</a>
								</div>';
							}
						}
					}
					?>
	  			</div>
				<!-- <form id="jobFilterForm" method="get" class="form-inline" action="">
					<div class="form-group">
						<?php 
						// if($jobCategories) {
						// 	echo  '<select name="term_id" class="form-control">
						// 	<option value="">Select Category</option>';
						// 	foreach ($jobCategories as $i => $jobCategory) {
						// 		if($selectedId == $jobCategory->term_id) {
						// 			echo '<option selected value="'.$jobCategory->term_id.'">'.$jobCategory->name.'</option>';
						// 		}
						// 		else {
						// 			echo '<option value="'.$jobCategory->term_id.'">'.$jobCategory->name.'</option>';	
						// 		}
						// 	}
						// 	echo '</select>';
						// }
						?>
					</div>			
					<button type="submit" class="filterButton">Filter</button>
				</form> -->
			</div>
			<div class="col-md-8">
				<section id="jobDataSection">
					<main id="jobDataMain" class="row">
						<?php if ( $the_query->have_posts() ) : ?>
							<?php while ( $the_query->have_posts() ) : $the_query->the_post(); ?>
								<article id="post-<?php the_ID(); ?>" class="col-md-12 jobData border">
									<div class="commonFieldsSection"><?php echo show_common_fields( get_the_ID() ) ?></div>
								</article>
							<?php endwhile; ?>
							<?php wp_reset_postdata(); ?>
						<?php else : ?>
							<p><?php esc_html_e( 'Sorry, no posts matched your criteria.' ); ?></p>
						<?php endif; ?>
					</main>
				</section>
			</div>
		</div>
	</div>
</div>

<?php
get_footer();
