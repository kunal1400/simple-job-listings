<?php 
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://example.com
 * @since             1.0.0
 * @package           Master Node
 *
 * @wordpress-plugin
 * Plugin Name:       Simple Job Listings
 * Plugin URI:        http://example.com/plugin-name-uri/
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            Kunal malviya
 * Author URI:        http://example.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       master-node
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'PLUGIN_NAME_VERSION', '1.0.0' );


function write_here_activation_actions() {
	do_action( 'wp_writehere_extension_activation' );	
}
register_activation_hook( __FILE__, 'write_here_activation_actions' );


function write_here_default_options(){
    // Subjects
	update_option("request_lead_info_subject", "Request To View Lead Information");
	update_option("approve_lead_info_subject", "Decision to view Lead Information");
	update_option("reject_lead_info_subject", "Decision to view Lead Information");
	// email body
	update_option("request_lead_info_message", "Your request to view details for {{jobTitle}} successfully submitted, we will check your request and made descission. You will also be notified on email also.");
	update_option("approve_lead_info_message", "Your request to view lead information for {{jobTitle}} has been successfully approved \n below are the lead information:\nclient_name: {{clientName}}
		\nclient_address: {{clientAddress}}
		\nclient_email:  {{clientEmail}}
		\nclient_telephone_number: {{clientMobile}}");
	update_option("reject_lead_info_message", "Your request to view lead information for {{jobTitle}} has been rejected");
}
add_action( 'wp_writehere_extension_activation', 'write_here_default_options' );


/**
* Registering the custom posttype for Jobs
**/
add_action( 'init', 'jobs_posttype_callback' );
function jobs_posttype_callback() {
	$labels = array(
		'name'               => _x( 'Jobs', 'post type general name', 'your-plugin-textdomain' ),
		'singular_name'      => _x( 'Job', 'post type singular name', 'your-plugin-textdomain' ),
		'menu_name'          => _x( 'Jobs', 'admin menu', 'your-plugin-textdomain' ),
		'name_admin_bar'     => _x( 'Job', 'add new on admin bar', 'your-plugin-textdomain' ),
		'add_new'            => _x( 'Add New', 'Job', 'your-plugin-textdomain' ),
		'add_new_item'       => __( 'Add New Job', 'your-plugin-textdomain' ),
		'new_item'           => __( 'New Job', 'your-plugin-textdomain' ),
		'edit_item'          => __( 'Edit Job', 'your-plugin-textdomain' ),
		'view_item'          => __( 'View Job', 'your-plugin-textdomain' ),
		'all_items'          => __( 'All Jobs', 'your-plugin-textdomain' ),
		'search_items'       => __( 'Search Jobs', 'your-plugin-textdomain' ),
		'parent_item_colon'  => __( 'Parent Jobs:', 'your-plugin-textdomain' ),
		'not_found'          => __( 'No Jobs found.', 'your-plugin-textdomain' ),
		'not_found_in_trash' => __( 'No Jobs found in Trash.', 'your-plugin-textdomain' )
	);

	$args = array(
		'labels'             => $labels,
		'description'        => __( 'Description.', 'your-plugin-textdomain' ),
		'public'             => true,
		'publicly_queryable' => true,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'query_var'          => true,
		'rewrite'            => array( 'slug' => 'jobs' ),
		'capability_type'    => 'post',
		'has_archive'        => true,
		'hierarchical'       => false,
		'menu_position'      => null,
		'supports'           => array( 'title', 'editor', 'thumbnail' )
	);

	register_post_type( 'simple_jobs', $args );
}


/**
* Adding submenu page in Wordpress Admin for simple jobs post type
**/
add_action('admin_menu', 'add_users_requested_submenu');
function add_users_requested_submenu() {
  add_submenu_page( 
      'edit.php?post_type=simple_jobs'
    , 'Users Requested' 
    , 'Users Requested'
    , 'manage_options'
    , 'simple_jobs_users_requested'
    , 'simple_jobs_users_requested_callback'
  );
  add_submenu_page( 
      'edit.php?post_type=simple_jobs'
    , 'Email Templates' 
    , 'Email Templates'
    , 'manage_options'
    , 'simple_jobs_email_templates'
    , 'simple_jobs_email_templates_callback'
  );
}

/**
* Callback function for simple_jobs_users_requested_callback
**/
function simple_jobs_users_requested_callback() {
	global $title;
	global $wpdb;
	echo "<h2>$title</h2>";

	$the_query = new WP_Query( array(  'post_type' => 'simple_jobs',
				'orderby' 	=> 'ID',
				'order' 	=> 'DESC',
				'meta_key'  => 'requested_users_id') 
			);

	if ( $the_query->have_posts() ) {
		$i = 1;
		while ( $the_query->have_posts() ) {
			$i++;
			$the_query->the_post();
			$title 		 = get_the_title();
			$postId 	 = get_the_ID();
			$appliedJobs = get_post_meta(get_the_ID(), 'requested_users_id', ARRAY_A);
			$permalink 	 = get_the_permalink(get_the_ID());
			
			echo "<h3>JOB TITLE: $title</h3>";
			echo '<table class="widefat fixed" cellspacing="0" >';
			echo "<thead><tr>
					<th width='40'>S.No</th>					
					<th>Email</th>
					<th>Job Url</th>
					<th>Action</th>
				</tr><thead>";
			echo "<tbody>";
			if($appliedJobs) {
				$appliedJobs = json_decode($appliedJobs);
				foreach ($appliedJobs as $key => $userId) {
					$currentUser = get_userdata( $userId );
					if($currentUser && $currentUser->data) {
						$currentUserId = $currentUser->data->ID;
						echo "<tr>";
						echo "<td>".($key+1)."</td>";
						echo "<td>".$currentUser->data->user_email."</td>";
						echo "<td><a href='".$permalink."' targe='_blank'>".$permalink."</a></td>";
						echo "<td>
								<button data-userId=".$currentUserId." data-postId=".$postId." class='button button-primary adminApproveViewLeadBtn'>Approve</button> 
								<button data-userId=".$currentUserId." data-postId=".$postId." class='button adminDeclineViewLeadBtn'>Deny</button></td>";
						echo "</tr>";
					}
				}
			}
			echo "<tbody>";
			echo '</table>';
			echo "<br/>";
			
		}
		wp_reset_postdata();
	} else {
		echo "No user requested on any job";
	}

}

/**
* Callback function for simple_jobs_email_templates
**/
function simple_jobs_email_templates_callback() {
	global $title;
	$request_lead_info_subject 	= get_option("request_lead_info_subject", "request lead info subject");
	$approve_lead_info_subject 	= get_option("approve_lead_info_subject", "approve lead info subject");
	$reject_lead_info_subject 	= get_option("reject_lead_info_subject", "reject lead info subject");
	$request_lead_info_message 	= get_option("request_lead_info_message", "request lead info message");
	$approve_lead_info_message 	= get_option("approve_lead_info_message", "approve lead info message");
	$reject_lead_info_message 	= get_option("reject_lead_info_message", "reject lead info message");

	echo "<div class='wrap'><h3>$title</h3>";
	echo "<form method='post' action=''>
		<input type='hidden' name='email_templates' value='set' />
		<h3>Dynamic variables: {{jobTitle}}, {{clientName}}, {{clientEmail}}, {{clientMobile}}, {{clientAddress}}</h3>
		<table class='emailTemplatesTable widefat striped'>
			<thead>
				<tr>
					<th width='20%'>Action</th>
					<th width='20%'>Subject</th>
					<th width='60%'>Message</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>When user requested for lead information</td>
					<td><input type='text' name='request_for_lead_subject' value='".$request_lead_info_subject."' /></td>
					<td><textarea name='request_for_lead' cols='4'>".$request_lead_info_message."</textarea></td>
				</tr>
				<tr>
					<td>When user approved to view lead information</td>
					<td><input type='text' name='approve_for_lead_subject' value='".$approve_lead_info_subject."' /></td>
					<td><textarea name='approve_for_lead' cols='4'>".$approve_lead_info_message."</textarea></td>
				</tr>
				<tr>
					<td>When user rejected to view lead information</td>
					<td><input type='text' name='reject_for_lead_subject' value='".$reject_lead_info_subject."' /></td>
					<td><textarea name='reject_for_lead' cols='4'>".$reject_lead_info_message."</textarea></td>
				</tr>
				<tr><td align='center' colspan='3'><button type='submit' class='button button-primary'>Submit</button></td></tr>
			</tbody>
		</table>
	</form></div>";
}



if( !empty($_POST['email_templates']) && $_POST['email_templates'] == "set" ) {
	update_option("request_lead_info_subject", $_POST['request_for_lead_subject']);
	update_option("request_lead_info_message", $_POST['request_for_lead']);
	update_option("approve_lead_info_subject", $_POST['approve_for_lead_subject']);
	update_option("reject_lead_info_subject", $_POST['reject_for_lead_subject']);
	update_option("approve_lead_info_message", $_POST['approve_for_lead']);
	update_option("reject_lead_info_message", $_POST['reject_for_lead']);	
}


/**
* Creating the taxonomies for Job type
**/
add_action( 'init', 'create_simple_jobs_taxonomies', 0 );
function create_simple_jobs_taxonomies() {
	$labels = array(
		'name'              => _x( 'Job Categories', 'taxonomy general name', 'textdomain' ),
		'singular_name'     => _x( 'Job Category', 'taxonomy singular name', 'textdomain' ),
		'search_items'      => __( 'Search Job Category', 'textdomain' ),
		'all_items'         => __( 'All Job Categories', 'textdomain' ),
		'parent_item'       => __( 'Parent Job Category', 'textdomain' ),
		'parent_item_colon' => __( 'Parent Job Category:', 'textdomain' ),
		'edit_item'         => __( 'Edit Job Category', 'textdomain' ),
		'update_item'       => __( 'Update Job Category', 'textdomain' ),
		'add_new_item'      => __( 'Add New Job Category', 'textdomain' ),
		'new_item_name'     => __( 'New Job Category', 'textdomain' ),
		'menu_name'         => __( 'Job Categories', 'textdomain' ),
	);

	$args = array(
		'hierarchical'      => true,
		'labels'            => $labels,
		'show_ui'           => true,
		'show_admin_column' => true,
		'query_var'         => true,
		'rewrite'           => array( 'slug' => 'job_categories' ),
	);

	register_taxonomy( 'simple_job_categories', array( 'simple_jobs' ), $args );
}


/**
* Registering the meta boxes for other job information
**/
add_action( 'add_meta_boxes_simple_jobs', 'adding_simple_jobs_boxes', 10, 2 );
function adding_simple_jobs_boxes() {
    $screen = get_current_screen();
    add_meta_box(
        'other-job-informations-meta-box',
        __( 'Other Informations' ),
        'render_other_informations_meta_box'
    );    
}


/**
* Callback function of other information meta box
**/
function render_other_informations_meta_box() {
	if( !empty($_GET['post']) ) {
		$job_location 	= get_post_meta($_GET['post'], '_job_location', ARRAY_A);
		$job_salary 	= get_post_meta($_GET['post'], '_job_salary', ARRAY_A);
		$client_name 	= get_post_meta($_GET['post'], '_client_name', ARRAY_A);
		$client_address = get_post_meta($_GET['post'], '_client_address', ARRAY_A);
		$client_email 	= get_post_meta($_GET['post'], '_client_email', ARRAY_A);
		$client_telephone_number = get_post_meta($_GET['post'], '_client_telephone_number', ARRAY_A);
	}
	else {
		$job_location = $job_salary = $client_name = $client_address = $client_email = $client_telephone_number = "";
	}
	echo '<div id="feedsGeneratorId1">			
		<form action="" method="post">			
			<ul>
		        <li>
		        	<label for="job_location">Job Location: </label>
		        	<input type="text" name="job_location" id="job_location" value="'.$job_location.'" required/>
		        </li>
		        <li>
		        	<label for="job_salary">Estimated Value (in £): </label>
		        	<input type="number" name="job_salary" id="job_salary" value="'.$job_salary.'" required/>
		        </li>		        
		    </ul>
		    <hr/>
		    <ul>
		        <li>
		        	<label for="client_name">Client name: </label>
		        	<input type="text" name="client_name" id="client_name" value="'.$client_name.'" required/>
		        </li>
		        <li>
		        	<label for="client_address">Client address: </label>
		        	<input type="text" name="client_address" id="client_address" value="'.$client_address.'" required/>
		        </li>
		        <li>
		        	<label for="client_email">Client email: </label>
		        	<input type="email" name="client_email" id="client_email" value="'.$client_email.'" required/>
		        </li>
		        <li>
		        	<label for="client_telephone_number ">Client telephone number : </label>
		        	<input type="number" name="client_telephone_number" id="client_telephone_number" value="'.$client_telephone_number.'" required/>
		        </li>		        
		    </ul>
		</form>		
	</div>';
}

/**
* Hooking the save post action
**/
add_action('save_post', 'simple_jobs_save_postdata');
function simple_jobs_save_postdata($post_id) {	
    if( !empty($_POST['job_location']) && $post_id) {
	    update_post_meta($post_id, '_job_location', $_POST['job_location']);		
    }
    if( !empty($_POST['job_salary']) && $post_id) {
	    update_post_meta($post_id, '_job_salary', $_POST['job_salary']);		
    }
	if(!empty($_POST['client_name']) && $post_id) {
		update_post_meta($post_id, '_client_name', $_POST['client_name']);
	}
	if(!empty($_POST['client_address']) && $post_id) {
		update_post_meta($post_id, '_client_address', $_POST['client_address']);
	}
	if(!empty($_POST['client_email']) && $post_id) {
		update_post_meta($post_id, '_client_email', $_POST['client_email']);
	}
	if(!empty($_POST['client_telephone_number']) && $post_id) {
		update_post_meta($post_id, '_client_telephone_number', $_POST['client_telephone_number']);
	}
}


/**
 * Checks to see if appropriate templates are present in active template directory.
 * Otherwises uses templates present in plugin's template directory.
 */
add_filter('template_include', 'simple_job_listings_set_template');
function simple_job_listings_set_template( $template ){

    /* 
     * Optional: Have a plug-in option to disable template handling
     * if( get_option('wpse72544_disable_template_handling') )
     *     return $template;
     */

    if(is_singular('simple_jobs') && 'single-simple_jobs.php' != $template ){
        //WordPress couldn't find an 'event' template. Use plug-in instead:
        $template = plugin_dir_path( __FILE__ ) . 'single-job-listing.php';
    }

    // Is archive page of simple jobs
    if( is_post_type_archive('simple_jobs') ) {
    	//WordPress couldn't find an 'event' template. Use plug-in instead:
        $template = plugin_dir_path( __FILE__ ) . 'archive-job-listing.php';    	
    }

    return $template;
}


/**
 * Enqueuing the js and css files on frontend
 */
function simple_jobs_enqueue_script() {
    wp_enqueue_script( 'simple_jobs_js', plugin_dir_url( __FILE__ ) . 'js/script.js', array('jquery'), '1.0' );
    wp_localize_script( 'simple_jobs_js', 'simple_jobs_js_var', array( 'ajax_url' => admin_url( 'admin-ajax.php' ), 'we_value' => 1234 ) );
    wp_enqueue_style( 'simple_jobs_admin', plugin_dir_url( __FILE__ ) . 'css/admin.css');
}
add_action('wp_enqueue_scripts', 'simple_jobs_enqueue_script');
add_action('admin_enqueue_scripts', 'simple_jobs_enqueue_script');

/**
 * Enqueuing the js and css files on frontend
 */
function simple_jobs_enqueue_frontend_css() {
    wp_enqueue_style( 'simple_jobs_frontend', plugin_dir_url( __FILE__ ) . 'css/front.css');
}
add_action('wp_enqueue_scripts', 'simple_jobs_enqueue_frontend_css');


/**
 * Returning the button so that user can request for lead details
 */
function request_additional_fields_button( $postId ) {
	$currentUserId = get_current_user_id();
	if( $postId && $currentUserId ) {		
		$requestedUsers = get_post_meta($postId, 'requested_users_id', ARRAY_A);
		$approvedUsers = get_post_meta($postId, 'approved_user_id', ARRAY_A);

		if( $requestedUsers ) {
			$requestedUsers = json_decode($requestedUsers);
			if( in_array($currentUserId, $requestedUsers) ) {
				$approvedUsers = json_decode($approvedUsers);
				if( in_array($currentUserId, $approvedUsers) ) {
					return;	
				}
				else {
					return "<button class='userRequestedToJob'>Already Requested</button>";
				}
			}
			else {
				return "<button data-postId=".$postId." data-userId=".$currentUserId." class='requestLeadDetails'>Request Lead Details</button>";
			}
		}
		else {
			return "<button data-postId=".$postId." data-userId=".$currentUserId." class='requestLeadDetails'>Request Lead Details</button>";
		}

	}
	else {
		return;
	}
}

function show_fields_after_registration( $postId ) {
	$currentUserId 	= get_current_user_id();
	$approvedUsers 	= get_post_meta($postId, 'approved_user_id', ARRAY_A);
	if( $approvedUsers ) {
		$approvedUsers = json_decode($approvedUsers);
		if( in_array($currentUserId, $approvedUsers) ) {
			$client_name 	= get_post_meta($postId, '_client_name', ARRAY_A);
			$client_address = get_post_meta($postId, '_client_address', ARRAY_A);
			$client_email 	= get_post_meta($postId, '_client_email', ARRAY_A);
			$client_telephone_number = get_post_meta($postId, '_client_telephone_number', ARRAY_A);
			return "<div class='jobClientName'>client Name: $client_name<div/>
			<div class='jobClientAddress'>client Address: $client_address<div/>
			<div class='jobClientEmail'>client Email: $client_email<div/>
			<div class='jobClientPhone'>client Telephone Number: $client_telephone_number<div/>";
		}
		else {
			return;
		}
	}
}

function show_common_fields( $postId ) {
	$title 		  = get_the_title( $postId );
	$link 		  = get_the_permalink( $postId );
	$post_date 	  = get_the_date("", $postId);
	$job_location = get_post_meta($postId, '_job_location', ARRAY_A);
	$job_salary   = get_post_meta($postId, '_job_salary', ARRAY_A);
	$categories   = get_the_terms( $postId, 'simple_job_categories' );	
	$categoryHtml = "";

	if($categories) {
		foreach ($categories as $key => $category) {
			if($key == 0) {
				$categoryHtml .= "<span class='jobCategories first'>".$category->name."</span>";
			}
			else {
				$categoryHtml .= "<span class='jobCategories others'>, ".$category->name."</span>";
			}
		}
	}

	if( is_single()	) {
		$description = get_the_content($postId);
	} else {
		$description = get_the_excerpt($postId);		
	}

	return '<div class="col-md-12">
		<div class="row">
			<h1 class="post-title entry-title jobTitle"><a href="'.$link.'" >'.$title.'</a></h1>
			<div class="col-md-12 jobCategoriesWrapper">'.$categoryHtml.'</div>
		</div>
		<div class="row">
			<div class="col-md-4 jobDate">Date: '.$post_date.'</div>
			<div class="col-md-4 jobLocation">Location: '.$job_location.'</div>
			<div class="col-md-4 jobSalary">Salary: '.$job_salary.'</div>
		</div>
		<div class="row">
			<div class="row jobDescription">'.$description.'</div>
		</div>
	</div>';
}

/**
 * lead detail action ajax handler
 */
add_action( 'wp_ajax_action_request_lead_details', 'action_request_lead_details_callback' );
add_action( 'wp_ajax_nopriv_action_request_lead_details', 'action_request_lead_details_callback' );
function action_request_lead_details_callback() {
	if( $_POST['postId'] && $_POST['userId'] && $_POST['string'] ) {		

		// Getting the current user info
		$currentUser 	= get_userdata( $_POST['userId'] );
		$currentUserId 	= $currentUserEmail = "";
		$postId 		= $_POST['postId'];

		// After getting the post id get the information which we have to send by email
		$postTitle 		= get_the_title($postId);
		$client_name 	= get_post_meta($postId, '_client_name', ARRAY_A);
		$client_address = get_post_meta($postId, '_client_address', ARRAY_A);
		$client_email 	= get_post_meta($postId, '_client_email', ARRAY_A);
		$client_telephone_number = get_post_meta($postId, '_client_telephone_number', ARRAY_A);

		if($currentUser && $currentUser->data) {
			$currentUserId = $currentUser->data->ID;
			$currentUserEmail = $currentUser->data->user_email;			
		}

		$emailBody = "";
		$string = "approved_user_id";

		$approve_lead_info_message = get_option("approve_lead_info_message", "approve lead info message");			
		$approve_lead_info_message = str_replace("{{jobTitle}}", $client_name, $approve_lead_info_message);
		$approve_lead_info_message = str_replace("{{jobTitle}}", $client_address, $approve_lead_info_message);
		$approve_lead_info_message = str_replace("{{jobTitle}}", $client_email, $approve_lead_info_message);
		$approve_lead_info_message = str_replace("{{jobTitle}}", $client_telephone_number, $approve_lead_info_message);

		$reject_lead_info_message = get_option("reject_lead_info_message", "reject lead info message");			
		$reject_lead_info_message = str_replace("{{jobTitle}}", $client_name, $reject_lead_info_message);
		$reject_lead_info_message = str_replace("{{jobTitle}}", $client_address, $reject_lead_info_message);
		$reject_lead_info_message = str_replace("{{jobTitle}}", $client_email, $reject_lead_info_message);
		$reject_lead_info_message = str_replace("{{jobTitle}}", $client_telephone_number, $reject_lead_info_message);

		$approve_lead_info_subject 	= get_option("approve_lead_info_subject", "approve lead info subject");
		$reject_lead_info_subject 	= get_option("reject_lead_info_subject", "reject lead info subject");

		if( $_POST['string'] == "approve" ) {
			$subject 	= $approve_lead_info_subject;
			$string 	= "approved_user_id";			
			$emailBody  = $approve_lead_info_message;
		} 
		else if( $_POST['string'] == "decline" ) {
			$subject 	= $reject_lead_info_subject;
			$string	   = "rejected_user_id";
			$emailBody = $reject_lead_info_message;
		}
		
		// Getting all old requested jobs
		$appliedJobs = get_post_meta($postId, $string, ARRAY_A);		
		if( $appliedJobs ) {
			$arrayToStore = json_decode($appliedJobs);
		}
		else {
			$arrayToStore = array();	
		}
		$arrayToStore[] = $_POST['userId'];

		$arrayToStore = array_unique($arrayToStore);
		$updateuserMetaFlag = update_post_meta( $postId, $string, json_encode( array_values($arrayToStore) ) );		

		wp_mail($currentUserEmail, $subject, $emailBody);
	}
	wp_die();
}

/**
 * Request lead detail ajax handler
 */
add_action( 'wp_ajax_request_lead_details', 'request_lead_details_callback' );
add_action( 'wp_ajax_nopriv_request_lead_details', 'request_lead_details_callback' );
function request_lead_details_callback() {
	if( $_POST['postId'] && $_POST['userId'] ) {		

		$currentUser 	= get_userdata( $_POST['userId'] );
		$jobTitle 		= get_the_title( $_POST['postId'] );
		$currentUserId 	= $currentUserEmail = "";

		if($currentUser && $currentUser->data) {
			$currentUserId = $currentUser->data->ID;
			$currentUserEmail = $currentUser->data->user_email;			
		}

		// Getting all old requested jobs
		$appliedJobs = get_post_meta($_POST['postId'], 'requested_users_id', ARRAY_A);		
		if( $appliedJobs ) {
			$arrayToStore = json_decode($appliedJobs);
		}
		else {
			$arrayToStore = array();	
		}
		$arrayToStore[] = $_POST['userId'];

		$arrayToStore = array_unique($arrayToStore);

		$updateuserMetaFlag = update_post_meta( $_POST['postId'], 'requested_users_id', json_encode( array_values($arrayToStore) ) );

		$request_lead_info_subject = get_option("request_lead_info_subject", "request lead info subject");
		$request_lead_info_message = get_option("request_lead_info_message", "request lead info message");
		$request_lead_info_subject = str_replace("{{jobTitle}}", $jobTitle, $request_lead_info_subject);
		$request_lead_info_message = str_replace("{{jobTitle}}", $jobTitle, $request_lead_info_message);

		//$approve_lead_info_subject 	= get_option("approve_lead_info_subject", "approve lead info subject");
		//$reject_lead_info_subject 	= get_option("reject_lead_info_subject", "reject lead info subject");
		//$approve_lead_info_message 	= get_option("approve_lead_info_message", "approve lead info message");
		//$reject_lead_info_message 	= get_option("reject_lead_info_message", "reject lead info message");

		wp_mail($currentUserEmail, $request_lead_info_subject, $request_lead_info_message);
	}	
	wp_die();
}