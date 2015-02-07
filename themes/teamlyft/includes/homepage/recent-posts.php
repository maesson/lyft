<?php
if ( ! defined( 'ABSPATH' ) ) exit;
/**
 * Recent Posts Component
 *
 * Display X Recent Posts
 *
 * @author Tiago
 * @since 1.0.0
 * @package WooFramework
 * @subpackage Component
 */

	$settings = array(
			'homepage_recent_posts_title' => __( 'From the Hub', 'woothemes' ),
			'homepage_recent_posts_byline' => __( 'Discover', 'woothemes' ),
			'homepage_recent_posts_number' => 9,
		);

	$settings = woo_get_dynamic_values( $settings );

	// Enqueue JavaScript
	wp_enqueue_script( 'recent-posts' );

?>

<section id="home-target" class="home-section">

	<div class="wrapper">
		<div id="home-content">
			<img src="/wp-content/uploads/2015/01/home.jpg">
			<p>Whether you’re a college student looking to build your resume, a social media all star with a passionate audience and a love for our mission or a loyal passenger who loves referring new people to our product, there’s a place for you on our team.</p> 
	 
			<p>This is your digital hub and news center. Stay up-to-date by following <a href="http://blog.lyft.com/">our blog</a>, use our snazzy Lyft Meme Maker to create and share custom social media images, get schooled at LyftU and even restock on <a href="http://lyft.biz.vistaprint.com/">promotional materials.</a></p> 

			<p>Interested in becoming an ambassador? <a href="http://www.lyft.com/ambassador/apply">Apply here.</a></p>
		</div> 

	</div><!-- /.wrapper -->

</section><!-- /#recent-posts -->