<?php
/**
 * materialwp functions and definitions
 *
 * @package materialwp
 */

if ( ! function_exists( 'materialwp_setup' ) ) :
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function materialwp_setup() {

	/**
	* Set the content width based on the theme's design and stylesheet.
	*/
	global $content_width;
	if ( ! isset( $content_width ) ) {
		$content_width = 640; /* pixels */
	}

	/*
	 * Make theme available for translation.
	 * Translations can be filed in the /languages/ directory.
	 * If you're building a theme based on materialwp, use a find and replace
	 * to change 'materialwp' to the name of your theme in all the template files
	 */
	load_theme_textdomain( 'materialwp', get_template_directory() . '/languages' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	//Suport for WordPress 4.1+ to display titles
	add_theme_support( 'title-tag' );

	/*
	 * Enable support for Post Thumbnails on posts and pages.
	 *
	 * @link http://codex.wordpress.org/Function_Reference/add_theme_support#Post_Thumbnails
	 */
	add_theme_support( 'post-thumbnails' );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus( array(
		'primary' => __( 'Primary Menu', 'materialwp' ),
	) );

	/*
	 * Switch default core markup for search form, comment form, and comments
	 * to output valid HTML5.
	 */
	add_theme_support( 'html5', array(
		'search-form', 'comment-form', 'comment-list', 'gallery', 'caption',
	) );

	/*
	 * Enable support for Post Formats.
	 * See http://codex.wordpress.org/Post_Formats
	 */
	// add_theme_support( 'post-formats', array(
	// 	'aside', 'image', 'video', 'quote', 'link',
	// ) );

	// Set up the WordPress core custom background feature.
	add_theme_support( 'custom-background', apply_filters( 'materialwp_custom_background_args', array(
		'default-color' => 'ffffff',
		'default-image' => '',
	) ) );
}
endif; // materialwp_setup
add_action( 'after_setup_theme', 'materialwp_setup' );

/**
 * Register widget area.
 *
 * @link http://codex.wordpress.org/Function_Reference/register_sidebar
 */
function materialwp_widgets_init() {
	register_sidebar( array(
		'name'          => __( 'Sidebar', 'materialwp' ),
		'id'            => 'sidebar-1',
		'description'   => '',
		'before_widget' => '<aside id="%1$s" class="widget %2$s" style="width: 360px;"><div class="panel panel-warning">',
		'after_widget'  => '</div></aside>',
		'before_title'  => ' <div class="panel-heading"><h3 class="panel-title">',
		'after_title'   => '</h3></div>',
	) );
}
add_action( 'widgets_init', 'materialwp_widgets_init' );

/**
 * Enqueue scripts and styles.
 */
function materialwp_scripts() {
	//wp_enqueue_script('mwp-jquery', '//code.jquery.com/jquery-1.12.0.min.js');

	//wp_enqueue_script('mwp-jquery-migrate', '//code.jquery.com/jquery-migrate-1.2.1.min.js');

	//wp_enqueue_style( 'datepicker-styles', 'https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css');

	//wp_enqueue_script( 'datepicker-js', 'https://code.jquery.com/ui/1.12.1/jquery-ui.js');

	wp_enqueue_style( 'datatable-1-styles', 'https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.0.0-alpha.2/css/bootstrap.css');

	wp_enqueue_style( 'datatable-2-styles', 'https://cdn.datatables.net/1.10.12/css/dataTables.bootstrap4.min.css');

	wp_enqueue_style( 'datatable-3-styles', 'https://cdn.datatables.net/select/1.2.0/css/select.bootstrap4.min.css');

	wp_enqueue_style( 'datatable-4-styles', 'https://cdn.datatables.net/scroller/1.4.2/css/scroller.bootstrap4.min.css');

	wp_enqueue_script( 'datatable-js', 'https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js');

	wp_enqueue_script( 'datatable-select-js', 'https://cdn.datatables.net/select/1.2.0/js/dataTables.select.min.js');

	wp_enqueue_script( 'datatable-bootstrap-js', 'https://cdn.datatables.net/1.10.12/js/dataTables.bootstrap4.min.js');

	wp_enqueue_script( 'datatable-scroller-js', 'https://cdn.datatables.net/scroller/1.4.2/js/dataTables.scroller.min.js');

	wp_enqueue_style( 'rating-styles', 'http://maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css',array(), false, 'all');

	wp_enqueue_style( 'star-styles', get_template_directory_uri() .'/jquery-bar-rating-master/dist/themes/bootstrap-stars.css');

	wp_enqueue_script('star-jquery', get_template_directory_uri() .'/jquery-bar-rating-master/dist/jquery.barrating.min.js');

	wp_enqueue_style( 'file-styles', get_template_directory_uri() .'/jasny-bootstrap/css/jasny-bootstrap.css');

	wp_enqueue_script('file-jquery', get_template_directory_uri() .'/jasny-bootstrap/js/jasny-bootstrap.min.js');

	wp_enqueue_script('file-min-jquery', get_template_directory_uri() .'/jasny-bootstrap/js/jasny-bootstrap.js');

	wp_enqueue_script( 'modernizr', get_template_directory_uri() . '/grid-effects/modernizr.custom.js');

	wp_enqueue_style( 'mwp-bootstrap-styles', get_template_directory_uri() . '/bower_components/bootstrap/dist/css/bootstrap.min.css', array(), '3.3.4', 'all' );

	wp_enqueue_style( 'mwp-roboto-styles', get_template_directory_uri() . '/bower_components/bootstrap-material-design/dist/css/roboto.min.css', array(), '', 'all' );

	wp_enqueue_style( 'mwp-material-styles', get_template_directory_uri() . '/bower_components/bootstrap-material-design/dist/css/material-fullpalette.min.css', array(), '', 'all' );

	wp_enqueue_style( 'mwp-ripples-styles', get_template_directory_uri() . '/bower_components/bootstrap-material-design/dist/css/ripples.min.css', array(), '', 'all' );

	wp_enqueue_style( 'materialwp-style', get_stylesheet_uri() );

	wp_enqueue_style( 'grid-effects-styles', get_template_directory_uri() . '/grid-effects/component.css', array(), '', 'all' );

	wp_enqueue_style( 'slippry-styles', get_template_directory_uri() . '/slippry/slippry.css' );

	//wp_enqueue_style( 'nouislider-styles', 'https://cdnjs.cloudflare.com/ajax/libs/noUiSlider/9.2.0/nouislider.min.css');

	wp_enqueue_script( 'mwp-bootstrap-js', get_template_directory_uri() . '/bower_components/bootstrap/dist/js/bootstrap.min.js', array('jquery'), '3.3.4', true );

	wp_enqueue_script( 'mwp-ripples-js', get_template_directory_uri() . '/bower_components/bootstrap-material-design/dist/js/ripples.min.js', array('jquery'), '', true );

	wp_enqueue_script( 'mwp-material-js', get_template_directory_uri() . '/bower_components/bootstrap-material-design/dist/js/material.min.js', array('jquery'), '', true );

	wp_enqueue_script( 'main-js', get_template_directory_uri() . '/js/main.js', array('jquery'), '', true );

	wp_enqueue_script( 'misc-js', get_template_directory_uri() . '/js/misc.js');

	wp_enqueue_script( 'masonry', 'https://unpkg.com/masonry-layout@4/dist/masonry.pkgd.min.js', array(), '', true);

	wp_enqueue_script( 'slippry-js', get_template_directory_uri() . '/slippry/slippry.min.js', array(), '', false);

	wp_enqueue_script( 'i18n-lp', 'http://www.localeplanet.com/api/auto/icu.js', array(), '', true);

	wp_enqueue_script( 'trans-lp', 'http://www.localeplanet.com/api/translate.js', array(), '', true);

	wp_enqueue_script( 'imageloaded', 'https://npmcdn.com/imagesloaded@4.1/imagesloaded.pkgd.min.js', array(), '', true);

	wp_enqueue_script( 'classie', get_template_directory_uri() . '/grid-effects/classie.js', array(), '', true);

	wp_enqueue_script( 'animOnScroll', get_template_directory_uri() . '/grid-effects/AnimOnScroll.js', array(), '', true);

	wp_enqueue_script( 'nouislider', 'https://cdnjs.cloudflare.com/ajax/libs/noUiSlider/9.2.0/nouislider.min.js');

	wp_enqueue_script( 'wnumb', 'https://cdnjs.cloudflare.com/ajax/libs/wnumb/1.1.0/wNumb.min.js');
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'materialwp_scripts' );

/*
function add_comment_fields($fields) {

	$fields = '<div class="form-group">
        <label for="star" class="col-md-2 control-label">Please Rate</label>
	    <div class="br-wrapper br-theme-bootstrap-stars">
        <div class="stars stars-example-bootstrap">
          <select id="star" name="star">
            <option>1</option>
            <option>2</option>
            <option>3</option>
            <option>4</option>
            <option>5</option>
          </select>
          <script type="text/javascript">
		   $(function() {
      		$(\'#star\').barrating({
        		theme: \'fontawesome-stars\',
        		initialRating:3
      			});
  			 });
		  </script>
		</div>
        </div>
    </div>
        <div class="form-group is-empty"><div class="col-md-10">' . $fields;
	return $fields;

}
add_filter('comment_form_field_comment','add_comment_fields');
*/

function add_comment_class($classes, $class, $commentID, $comment, $post_id ){
	array_push($classes, "well");
	return $classes;
}
add_filter('comment_class', 'add_comment_class',1,5);
function get_total_reviews($post_id) {
	global $wpdb;
	$query = 'SELECT count(meta_ID) as review_count FROM wp_comments as c, wp_commentmeta as m where c.comment_ID = m.comment_ID and comment_post_ID = '.$post_id;
	$results = $wpdb->get_results( $query, ARRAY_A );
	//var_dump(get_template_directory());
	echo"<label>".$results[0]['review_count']." Reviews</label>";
}

function get_review_star($post_id) {
	global $wpdb;
	$query1 = 'SELECT AVG(meta_value) as avg_star FROM wp_commentmeta as m, wp_comments as c where m.comment_id = c.comment_id and meta_key=\'star\' and comment_post_ID = '.$post_id;
	$query2 = 'SELECT count(meta_ID) as review_count FROM wp_comments as c, wp_commentmeta as m where c.comment_ID = m.comment_ID and comment_post_ID = '.$post_id;
	$result1 = $wpdb->get_results( $query1, ARRAY_A );
	$result2 = $wpdb->get_results( $query2, ARRAY_A );
	echo "<div class=\"br-wrapper br-theme-bootstrap-stars\">
 		  <div class=\"stars stars-example-bootstrap\">
 		  <h4 class=\"secondary-heading\" >".$result2[0]['review_count']." Reviews</h4>
 		  <select id=\"example\">
  			<option value=\"1\">1</option>
  			<option value=\"2\">2</option>
  			<option value=\"3\">3</option>
  			<option value=\"4\">4</option>
  			<option value=\"5\">5</option>
		  </select>
		  </div>
		  </div>
		  <script type=\"text/javascript\">
		   $(function() {
      		$('#example').barrating({
        		theme: 'fontawesome-stars',
        		readonly:true,
        		initialRating:".round($result1[0]['avg_star'],0,PHP_ROUND_HALF_UP)."
      			});
  			 });
		  </script>";
}

function add_comment_meta_values($comment_id) {
	if(isset($_POST['star'])) {
		$age = wp_filter_nohtml_kses($_POST['star']);
		add_comment_meta($comment_id, 'star', $age, false);
	}

}
add_action ('comment_post', 'add_comment_meta_values', 1);

function add_rating_feild() {
	echo'<div class="form-group">
        <input type="hidden" name="star" id="star">
        <label for="star" class="col-md-2 control-label">Please Rate</label>
	    <div class="br-wrapper br-theme-bootstrap-stars">
        <div class="stars stars-example-bootstrap">
          <select id="rate" name="rate">
            <option>1</option>
            <option>2</option>
            <option>3</option>
            <option>4</option>
            <option>5</option>
          </select>
          <script type="text/javascript">
		   $(function() {
      		$(\'#rate\').barrating(\'show\',{
        		theme: \'fontawesome-stars\',
        		onSelect: function(value, text, event) {
    				if (typeof(event) !== \'undefined\') {
      					// rating was selected by a user
      					$("#star").val(value);
    				} else {
    				}
    			  }
      			});
  			 });
		  </script>
		</div>
        </div>
    </div>
        <div class="form-group is-empty"><div class="col-md-10">';

}
add_action ('comment_form_top', 'add_rating_feild');

function debug_to_console( $data ) {

	if ( is_array( $data ) )
		$output = "<script>console.log( 'Debug Objects: " . implode( ',', $data) . "' );</script>";
	else
		$output = "<script>console.log( 'Debug Objects: " . $data . "' );</script>";

	echo $output;
}

add_action( 'personal_options_update', 'checkName' );
add_action( 'edit_user_profile_update', 'checkName');
add_action( 'personal_options_update', 'uploadPassport' );
add_action( 'edit_user_profile_update', 'uploadPassport');
add_action( 'personal_options_update', 'uploadStudentID' );
add_action( 'edit_user_profile_update', 'uploadStudentID');

/*function modify_contact_methods($profile_fields) {
	//$profile_fields['twitter'] = '';
	$profile_fields['passport_expire_date'] = 'expire_date';
	$profile_fields['Passport'] = 'passport_file';



	return $profile_fields;
}*/
//add_filter('user_contactmethods', 'modify_contact_methods');

function checkName() {
	if (!isset($_POST['first_name']) || $_POST['first_name'] == '')
		add_action( 'user_profile_update_errors', 'first_name_missing_error');
	if (!isset($_POST['last_name']) || $_POST['last_name'] == '')
		add_action( 'user_profile_update_errors', 'last_name_missing_error');
}
function uploadPassport() {
	global $user_ID;

	//If no passport is uploaded, exit
	if(!$_FILES['passport']['tmp_name'])
		return;

	$target_dir = "/wp-content/uploads/ID/";
	$target_file_name = basename($_FILES['passport']['name']);
	$uploadOk = 0;
	$imageFileType = pathinfo($target_file_name,PATHINFO_EXTENSION);
	$target_file = "/var/www/wordpress".$target_dir . "user_" . $user_ID ."_passport." . $imageFileType;
	$target_file1 = $target_dir . "user_" . $user_ID ."_passport." . $imageFileType;
	$pp_expire_date = $_POST['pp_expiry_date'];

	// Check if image file is a actual image or fake image
	if(isset($_POST["submit"])) {
		$check = getimagesize($_FILES['passport']['tmp_name']);
		if($check !== false) {
			$uploadOk = 1;
		} else {
			add_action( 'user_profile_update_errors', 'file_upload_error_not_image');
			return;
		}
	}
	// Check file size
	if($_FILES["passport"]["size"] > 500000) {
		add_action( 'user_profile_update_errors', 'file_upload_error_too_large');
		return;
	}
    // Allow certain file formats
	if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
		&& $imageFileType != "gif" )
	{
		add_action( 'user_profile_update_errors', 'file_upload_error_invalid_format');
		return;
	}
	// if everything is ok, try to upload file
	if ($uploadOk == 1) {
		if (move_uploaded_file($_FILES['passport']['tmp_name'], $target_file)) {
			update_user_meta( $user_ID, "passport_expire_date", $pp_expire_date);
			update_user_meta( $user_ID, "Passport", $target_file1);
		} else {
			add_action( 'user_profile_update_errors', 'upload_passport_error');
		}
	}
}

function uploadStudentID() {
	global $user_ID;

	//If no passport is uploaded, exit
	if(!$_FILES['studentID']['tmp_name'])
		return;

	$target_dir = "/wp-content/uploads/ID/";
	$target_file_name = basename($_FILES['studentID']['name']);
	$uploadOk = 0;
	$imageFileType = pathinfo($target_file_name,PATHINFO_EXTENSION);
	$target_file = "/var/www/wordpress".$target_dir . "user_" . $user_ID ."_studentID." . $imageFileType;
	$target_file1 = $target_dir . "user_" . $user_ID ."_studentID." . $imageFileType;
	$pp_expire_date = $_POST['stuid_expiry_date'];

	// Check if image file is a actual image or fake image
	if(isset($_POST["submit"])) {
		$check = getimagesize($_FILES['studentID']['tmp_name']);
		if($check !== false) {
			$uploadOk = 1;
		} else {
			add_action( 'user_profile_update_errors', 'file_upload_error_not_image');
			return;
		}
	}
	// Check file size
	if($_FILES["studentID"]["size"] > 500000) {
		add_action( 'user_profile_update_errors', 'file_upload_error_too_large');
		return;
	}
	// Allow certain file formats
	if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
		&& $imageFileType != "gif" )
	{
		add_action( 'user_profile_update_errors', 'file_upload_error_invalid_format');
		return;
	}
	// if everything is ok, try to upload file
	if ($uploadOk == 1) {
		if (move_uploaded_file($_FILES['studentID']['tmp_name'], $target_file)) {
			update_user_meta( $user_ID, "stuid_expire_date", $pp_expire_date);
			update_user_meta( $user_ID, "StudentID", $target_file1);
		} else {
			add_action( 'user_profile_update_errors', 'upload_student_id_error');
		}
	}
}

function first_name_missing_error($errors) {
	$errors->add( 'user_login', __('<p><strong>ERROR</strong>: Please enter your first name.</p>') );

}

function last_name_missing_error($errors) {
	$errors->add( 'user_login', __('<p><strong>ERROR</strong>: Please enter your last name.</p>') );

}

function file_upload_error_not_image($errors) {
	$errors->add( 'user_login', __('<p><strong>ERROR</strong>: Sorry, your file is not an image.</p>') );

}

function file_upload_error_too_large($errors) {
	$errors->add( 'user_login', __('<p><strong>ERROR</strong>: Sorry, your file is too large. Please make sure your file size is less than 500 KB.</p>') );

}

function file_upload_error_invalid_format($errors) {
	$errors->add( 'user_login', __('<p><strong>ERROR</strong>: Sorry, only JPG, JPEG, PNG & GIF files are allowed.</p>') );

}

function upload_passport_error($errors) {
	$errors->add( 'user_login', __('<p class="text-danger"><strong>ERROR</strong>: An error occurs to upload passport.</p>') );

}

function upload_student_id_error($errors) {
	$errors->add( 'user_login', __('<p class="text-danger"><strong>ERROR</strong>: An error occurs to upload student id.</p>') );

}

function edit_submit_button($submit_button, $args) {
	$submit_button = '<button name="submit" type="submit" class="btn btn-primiry" id="submitz" style="color: #009688;">SUBMIT</button>';
/*	$submit_button = '
	<button name="submit" type="button" class="btn btn-primiry" id="submit1" style="color: #009688;">SUBMIT</button>
	<script type="text/javascript">
	$(document).ready( function() {
	  var $ta = $("textarea");
	  $ta.removeProp("background");
	  
      var form = $(\'#commentform\');
	
      form.find(\'button\').click( function() {
        if ($("#comment").val() == "")
        {
        	window.alert("Please say something for the property in the comment feild.");
        }
        else{
        	$.ajax( {
        	type: "POST",
        	url: form.attr( \'action\' ),
        	data: form.serialize(),
        	success: function( response ) {
          	window.alert("Your Comment has been submitted successfully.");
          	$("#comment").val("");
          	}
        	} );
        }
      } );
    } );
    </script>';*/
	return $submit_button;

}
add_filter('comment_form_submit_button','edit_submit_button', 1, 2);

function ajaxHelpers(){ ?>
	<script>
		function getAjaxData(url, callback) {
			$.ajax({
				type: "GET",
				url: url,
				dataType: 'json',
				success: callback
			});
		}
	</script> <?php
}
/**
 * Implement the Custom Header feature.
 */
//require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Custom functions that act independently of the theme templates.
 */
require get_template_directory() . '/inc/extras.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
require get_template_directory() . '/inc/jetpack.php';

/**
 * Adds a Walker class for the Bootstrap Navbar.
 */
require get_template_directory() . '/inc/bootstrap-walker.php';

/**
 * Comments Callback.
 */
require get_template_directory() . '/inc/comments-callback.php';