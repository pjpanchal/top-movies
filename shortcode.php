<?php
// SHORTCODE FOR ALL MOVIES 
add_shortcode( 'list-top-movies', 'top_movies_list' );
function top_movies_list( $atts ) {
    ob_start();
    $query = new WP_Query( array(
        'post_type' => 'movie_reviews',
        'posts_per_page' => -1,
        'order' => 'ASC',
        'orderby' => 'title',
    ) );
    if ( $query->have_posts() ) { ?>
        <ul class="movie-reviews-listing">
            <?php while ( $query->have_posts() ) : $query->the_post(); ?>
			<?php
				$movie_director = esc_html( get_post_meta( get_the_ID(), 'movie_director', true ) ); 
				$movie_actor = esc_html( get_post_meta( get_the_ID(), 'movie_actor', true ) ); 
				$movie_actress = esc_html( get_post_meta( get_the_ID(), 'movie_actress', true ) ); 
				$movie_rating = esc_html( get_post_meta( get_the_ID(), 'movie_rating', true ) ); 
			?>
            <li id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                <div class="movie_pic">
				<?php if ( has_post_thumbnail() ) 
					{
						the_post_thumbnail(array(200,200));
					} 
					// the_content(); ?>
				</div>
				<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
				<div class="profit">
				<?php $profit = esc_html( get_post_meta( get_the_ID(), 'movie_profit', true ) ); ?>
				<p><?php if(!empty($profit)){ echo "Net Profit : " .bd_nice_number($profit); } ?></br>
						<?php if(!empty($movie_director)){ echo "Director  : " .$movie_director; } ?></br>
						<?php if(!empty($movie_actor)){ echo "Actor  : " .$movie_actor; } ?></br>
						<?php if(!empty($movie_rating)){ echo "Rating 5 /  " .$movie_rating; } ?></p>
				</div>
				
            </li>
            <?php endwhile;
            wp_reset_postdata(); ?>
        </ul>
    <?php $myvariable = ob_get_clean();
    return $myvariable;
    }
}
?>
<style>
ul.movie-reviews-listing li{list-style: none;width: 32%;display: inline-block;vertical-align: top;padding: 1%;}
ul.movie-reviews-listing li .movie_pic{max-height: 109px;overflow: hidden;}
</style>