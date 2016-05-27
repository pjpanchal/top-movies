<?php
/*
Plugin Name: A Top Movies Reviews
Plugin URI: http://bestalavista.com/
Description: Declares a plugin that will create a custom post type displaying top movies
Version: 1.0
Author: Pankaj Panchal
Author URI: http://bestalavista.com/
License: GPLv2
*/
?>
<?php
/* REGISTER CUSTOM POST TYPE */
add_action( 'init', 'create_top_movies' );
function create_top_movies() {
    register_post_type( 'movie_reviews',
        array(
            'labels' => array(
                'name' => 'Top Movie ',
                'singular_name' => 'Top Movie',
                'add_new' => 'Add New',
                'add_new_item' => 'Add New Top Movie',
                'edit' => 'Edit',
                'edit_item' => 'Edit Top Movie',
                'new_item' => 'New Top Movie',
                'view' => 'View',
                'view_item' => 'View Top Movie',
                'search_items' => 'Search Top Movie ',
                'not_found' => 'No Movie found',
                'not_found_in_trash' => 'No Movie found in Trash',
                'parent' => 'Parent Top Movie'
            ),
            'public' => true,
            'menu_position' => 15,
            'supports' => array( 'title', 'editor', 'comments', 'thumbnail', 'custom-fields' ),
            'taxonomies' => array( '' ),
            'menu_icon' => 'dashicons-image-filter',
            'has_archive' => true
        )
    );
}
/* END REGISTER CUSTOM POST TYPE */

/* META BOX */
add_action( 'admin_init', 'my_admin' );
function my_admin() {
    add_meta_box( 'movie_review_meta_box',
        'Top Movies Details',
        'display_movie_review_meta_box',
        'movie_reviews', 'normal', 'high'
    );
}

function display_movie_review_meta_box( $movie_review ) {
    // Retrieve current name of the Director and Movie Rating based on review ID
    $movie_director = esc_html( get_post_meta( $movie_review->ID, 'movie_director', true ) );
	$movie_actor = esc_html( get_post_meta( $movie_review->ID, 'movie_actor', true ) );
	$movie_actress = esc_html( get_post_meta( $movie_review->ID, 'movie_actress', true ) );
	$movie_profit = esc_html( get_post_meta( $movie_review->ID, 'movie_profit', true ) );
    $movie_rating = intval( get_post_meta( $movie_review->ID, 'movie_rating', true ) );
    ?>
    <table>
        <tr>
            <td style="width: 100%">Movie Director</td>
            <td><input type="text" size="80" name="movie_review_director_name" value="<?php echo $movie_director; ?>" required /></td>
        </tr>
		<tr>
            <td style="width: 100%">Movie Actor</td>
            <td><input type="text" size="80" name="movie_review_actor_name" value="<?php echo $movie_actor; ?>" required /></td>
        </tr>
		<tr>
            <td style="width: 100%">Movie Actress</td>
            <td><input type="text" size="80" name="movie_review_actress_name" value="<?php echo $movie_actress; ?>" required /></td>
        </tr>
		<tr>
            <td style="width: 100%">Movie Profit</td>
            <td><input type="text" size="80" name="movie_review_profit" value="<?php echo $movie_profit; ?>"  required  data-validation="number "/></td>
        </tr>
        <tr>
            <td style="width: 150px">Movie Rating</td>
            <td>
                <select style="width: 100px" name="movie_review_rating" required>
                <?php
                // Generate all items of drop-down list
                for ( $rating = 5; $rating >= 1; $rating -- ) {
                ?>
                    <option value="<?php echo $rating; ?>" <?php echo selected( $rating, $movie_rating ); ?>>
                    <?php echo $rating; ?> stars <?php } ?>
                </select>
            </td>
        </tr>
    </table>
<?php
}
/* SAVE POST */
add_action( 'save_post', 'add_top_movie_fields', 10, 2 );
function add_top_movie_fields( $movie_review_id, $movie_review ) {
    // Check post type for movie reviews
    if ( $movie_review->post_type == 'movie_reviews' ) {
        // Store data in post meta table if present in post data
        if ( isset( $_POST['movie_review_director_name'] ) && $_POST['movie_review_director_name'] != '' ) {
            update_post_meta( $movie_review_id, 'movie_director', $_POST['movie_review_director_name'] );
        }
		
		 if ( isset( $_POST['movie_review_actor_name'] ) && $_POST['movie_review_actor_name'] != '' ) {
            update_post_meta( $movie_review_id, 'movie_actor', $_POST['movie_review_actor_name'] );
        }
		
		 if ( isset( $_POST['movie_review_actress_name'] ) && $_POST['movie_review_actress_name'] != '' ) {
            update_post_meta( $movie_review_id, 'movie_actress', $_POST['movie_review_actress_name'] );
        }
		
		 if ( isset( $_POST['movie_review_profit'] ) && $_POST['movie_review_profit'] != '' ) {
            update_post_meta( $movie_review_id, 'movie_profit', $_POST['movie_review_profit'] );
        }
		
        if ( isset( $_POST['movie_review_rating'] ) && $_POST['movie_review_rating'] != '' ) {
            update_post_meta( $movie_review_id, 'movie_rating', $_POST['movie_review_rating'] );
        }
    }
}

/* Convert Currency in M, B */
  function bd_nice_number($n) {
        // first strip any formatting;
        $n = (0+str_replace(",","",$n));
        
        // is this a number?
        if(!is_numeric($n)) return false;
        
        // now filter it;
        if($n>1000000000000) return round(($n/1000000000000),1).' trillion';
        else if($n>1000000000) return round(($n/1000000000),1).' billion';
        else if($n>1000000) return round(($n/1000000),1).' million';
        else if($n>1000) return round(($n/1000),1).' thousand';
        
        return number_format($n);
   }
   
 /* For Custom Single.php file for Movies */  
 function get_custom_post_type_template($single_template) {
     global $post;

     if ($post->post_type == 'movie_reviews') {
          $single_template = dirname( __FILE__ ) . '/movies-template.php';
     }
     return $single_template;
}
add_filter( 'single_template', 'get_custom_post_type_template' );

function add_posttype_slug_template( $single_template )
{
	$object = get_queried_object();
	$single_postType_postName_template = locate_template("single-{$object->post_type}-{$object->post_name}.php");
	if( file_exists( $single_postType_postName_template ) )
	{
		return $single_postType_postName_template;
	} else {
		return $single_template;
	}
}
add_filter( 'single_template', 'add_posttype_slug_template', 10, 1 );

/* SHOW METAS ONLY  IF SINGLE POST IS MOVIE */
function insert_movie_meta($content) {
   if(is_singular( 'movie_reviews')) 
   {
      $content.= '<div class="movie_meta">';
      $content.= '<p>Director : '.esc_html( get_post_meta( get_the_ID(), 'movie_director', true ) ) .'</p>'; 
	  $content.= '<p>Actor : '.esc_html( get_post_meta( get_the_ID(), 'movie_actor', true ) )  .'</p>'; 
	  $content.= '<p>Actress : '.esc_html( get_post_meta( get_the_ID(), 'movie_actress', true ) ) .'</p>'; 
	  $content.= '<p>Rating : 5 / '.esc_html( get_post_meta( get_the_ID(), 'movie_rating', true ) ) .'</p>'; 
	  $content.= '</div>';
   }
   return $content;
}
add_filter ('the_content', 'insert_movie_meta');

/* INCLUDE FILES */
function wpdocs_enqueue_movie_admin_files() {
        //wp_enqueue_style( 'custom_wp_admin_movie_css',  plugins_url( '/css/top_movies.css', __FILE__ ) );
		wp_enqueue_script( 'custom_wp_admin_movie_js',  plugins_url( '/js/jquery.form-validator.min.js', __FILE__ ),array('jquery'), '1.0', true);
		wp_enqueue_script( 'custom_wp_admin_movie_js_c',  plugins_url( '/js/top_movies.js', __FILE__ ) );
}
add_action( 'admin_enqueue_scripts', 'wpdocs_enqueue_movie_admin_files' );

/* CSS For Front End */
function wpdocs_enqueue_movie_css_files() {
        wp_enqueue_style( 'custom_wp_admin_movie_css',  plugins_url( '/css/top_movies.css', __FILE__ ) );
		wp_enqueue_script( 'custom_wp_admin_movie_js',  plugins_url( '/js/jquery.form-validator.min.js', __FILE__ ),array('jquery'), '1.0', true);
		wp_enqueue_script( 'custom_wp_admin_movie_js_c',  plugins_url( '/js/top_movies.js', __FILE__ ) );
}
add_action( 'wp_enqueue_scripts', 'wpdocs_enqueue_movie_css_files' );

/* INCLUDE WIDGET & SHORTCODE FILE*/
include( plugin_dir_path( __FILE__ ) . 'widget.php');
include( plugin_dir_path( __FILE__ ) . 'shortcode.php');
?>