<?php
// Creating the widget 
class movie_widget extends WP_Widget 
{
	function __construct() 
	{
		parent::__construct(
		// Base ID of your widget
		'movie_widget', 

		// Widget name will appear in UI
		__('Top Movie Widget', 'movie_widget_domain'), 

		// Widget description
		array( 'description' => __( 'Top Movie widget ', 'movie_widget_domain' ), ) 
		);
	}

/* WIDGET FRONTEND*/
public function widget( $args, $instance ) 
{
		$title = apply_filters( 'widget_title', $instance['title'] );
		$movie_director = apply_filters( 'widget_title', $instance['movie_director'] );
		$movie_actor = apply_filters( 'widget_title', $instance['movie_actor'] );
		$movie_actress = apply_filters( 'widget_title', $instance['movie_actress'] );
		
		
		// before and after widget arguments are defined by themes
		echo $args['before_widget'];
		if ( ! empty( $title ) )
		echo $args['before_title'] . $title . $args['after_title'];
		
		// Create and run custom loop
		if(empty($movie_director) && empty($movie_actor) && empty($movie_actress) )
		{
			//echo "Empty Selection";
			$args = array(
				'post_type' => 'movie_reviews',
				'meta_key' => 'movie_profit',
				'orderby' => 'movie_profit',
			);
		}
		else
		{
			$args = array(
				'post_type' => 'movie_reviews',
				'meta_key' => 'movie_profit',
				'orderby' => 'movie_profit',
				 'meta_query' => array(
					'relation'    => 'OR',
						'movie_director' => array(
							'key'     => 'movie_director',
							'compare' => 'EXISTS',
							'value' => $movie_director,
						),
						'movie_actor'    => array(
							'key'     => 'movie_actor',
							'value' => $movie_actor,
							'compare' => 'EXISTS',
						),
						'movie_actress'   => array(
							'key'     => 'movie_actress',
							'value' => $movie_actress,
							'compare' => 'EXISTS',
						),
				),
			);
		}
		$custom_posts = new WP_Query($args);
		while ($custom_posts->have_posts()) : $custom_posts->the_post();
		?>
		<div class="movie_block">
			<h5><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h5>
			<div class="movie_pic">
			<?php if ( has_post_thumbnail() ) 
				{
					the_post_thumbnail(array(100,100));
				} 
				// the_content(); ?>
			</div>
			<div class="profit">
				<?php $profit = esc_html( get_post_meta( get_the_ID(), 'movie_profit', true ) ); ?>
				<h6>	<?php if(!empty($profit)){ echo "Net Profit " . bd_nice_number($profit); } ?></h6>
			</div>
			
		</div>
		<?php endwhile; ?>
		<?php wp_reset_postdata(); ?>
		<?php		
		// This is where you run the code and display the output
		echo $args['after_widget'];
}
/* END WIDGET FRONTEND*/

/* WIDGET BACK END*/
public function form( $instance ) 
{
	if ( isset( $instance[ 'title' ] ) ) 
	{
	$title = $instance[ 'title' ];
	}
	else 
	{
	$title = __( 'Top Movies', 'movie_widget_domain' );
	}
	
	if ( isset( $instance[ 'movie_director' ] ) ) 	{	$movie_director = $instance[ 'movie_director' ];	}
	if ( isset( $instance[ 'movie_actor' ] ) ) 	{	$movie_actor = $instance[ 'movie_actor' ];	}
	if ( isset( $instance[ 'movie_actress' ] ) ) 	{	$movie_actress = $instance[ 'movie_actress' ];	}
	
// Widget admin form
?>
	<p>
	<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
	<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
	</p>
	
	<p>
	<label for="<?php echo $this->get_field_id( 'movie_director' ); ?>"><?php _e( 'Movie Director:' ); ?></label> 
	<?php
		global  $wpdb;
		$directors = $wpdb->get_results( "SELECT DISTINCT meta_value FROM wp_postmeta WHERE meta_key = 'movie_director' ORDER BY meta_value DESC");
	?>
	<select   id="<?php echo $this->get_field_id( 'movie_director' ); ?>" name="<?php echo $this->get_field_name( 'movie_director' ); ?>" selected="<?php echo $movie_director ; ?>" >
		<option value="">-</option>
		<?php
			foreach ( $directors as $director ) 
			{
				if($movie_director == $director->meta_value )
				{
						echo  '<option value="'. $director->meta_value. '" selected>'.$director->meta_value.'</option>';
				}
				else{
						echo  '<option value="'. $director->meta_value. '">'.$director->meta_value.'</option>';
				}
				
			}
		?>
	</select>
	</p>
	
	<p>
	<label for="<?php echo $this->get_field_id( 'movie_actor' ); ?>"><?php _e( 'Movie Actor:' ); ?></label> 
	<?php
		global  $wpdb;
		$actors = $wpdb->get_results( "SELECT DISTINCT meta_value FROM wp_postmeta WHERE meta_key = 'movie_actor' ORDER BY meta_value DESC");
	?>
	<select  id="<?php echo $this->get_field_id( 'movie_actor' ); ?>" name="<?php echo $this->get_field_name( 'movie_actor' ); ?>" selected="<?php echo $movie_actor ; ?>" >
		<option value="">-</option>
		<?php
			foreach ( $actors as $actor ) {
				if($movie_actor == $actor->meta_value )
				{
					echo  '<option value="'. $actor->meta_value. '" selected>'.$actor->meta_value.'</option>';
				}
				else
				{
					echo  '<option value="'. $actor->meta_value. '">'.$actor->meta_value.'</option>';
				}
			}
		?>
	</select>
	</p>
	
	<p>
	<label for="<?php echo $this->get_field_id( 'movie_actress' ); ?>"><?php _e( 'Movie Actress:' ); ?></label> 
	<?php
		global  $wpdb;
		$actresses = $wpdb->get_results( "SELECT DISTINCT meta_value FROM wp_postmeta WHERE meta_key = 'movie_actress' ORDER BY meta_value DESC");
	?>
	<select id="<?php echo $this->get_field_id( 'movie_actress' ); ?>" name="<?php echo $this->get_field_name( 'movie_actress' ); ?>" selected="<?php echo $movie_actress ; ?>">
		<option value="">-</option>
		<?php
			foreach ( $actresses as $actress ) {
				if($movie_actress == $actress->meta_value )
				{
					echo  '<option value="'. $actress->meta_value. '" selected>'.$actress->meta_value.'</option>';
				}
				else
				{
					echo  '<option value="'. $actress->meta_value. '">'.$actress->meta_value.'</option>';
				}
			}
		?>
	</select>
	</p>
<?php 
}
	
// Updating widget replacing old instances with new
public function update( $new_instance, $old_instance ) 
{
	$instance = array();
	$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
	
	$instance['movie_director'] = ( ! empty( $new_instance['movie_director'] ) ) ? strip_tags( $new_instance['movie_director'] ) : '';
	$instance['movie_actor'] = ( ! empty( $new_instance['movie_actor'] ) ) ? strip_tags( $new_instance['movie_actor'] ) : '';
	$instance['movie_actress'] = ( ! empty( $new_instance['movie_actress'] ) ) ? strip_tags( $new_instance['movie_actress'] ) : '';
	
	return $instance;
}
} 
/* END WIDGET BACK END*/

// Register and load the widget
function wpb_load_widget() {
	register_widget( 'movie_widget' );
}
add_action( 'widgets_init', 'wpb_load_widget' );
?>