<?php
if ( ! defined( 'ABSPATH' ) ) exit;
/**
 * Footer Template
 *
 * Here we setup all logic and XHTML that is required for the footer section of all screens.
 *
 * @package WooFramework
 * @subpackage Template
 */
	global $woo_options;

?>

	<div id="footer-wrapper">

<?php

	$total = 4;
	if ( isset( $woo_options['woo_footer_sidebars'] ) && ( '' != $woo_options['woo_footer_sidebars'] ) ) {
		$total = $woo_options['woo_footer_sidebars'];
	}

	if ( ( woo_active_sidebar( 'footer-1' ) ||
		   woo_active_sidebar( 'footer-2' ) ||
		   woo_active_sidebar( 'footer-3' ) ||
		   woo_active_sidebar( 'footer-4' ) ) && $total > 0 ) {

?>

		<?php woo_footer_before(); ?>

		<section id="footer-widgets">

			<div class="wrapper col-<?php echo esc_attr( $total ); ?> fix">

				<?php $i = 0; while ( $i < $total ) { $i++; ?>
					<?php if ( woo_active_sidebar( 'footer-' . $i ) ) { ?>

				<div class="block footer-widget-<?php echo $i; ?>">
		        	<?php woo_sidebar( 'footer-' . $i ); ?>
				</div>

			        <?php } ?>
				<?php } // End WHILE Loop ?>

			</div><!-- /.wrapper -->

		</section><!-- /#footer-widgets  -->
<?php } // End IF Statement ?>
		<footer id="footer">

			<div class="wrapper">

				<div id="copyright">
				<?php if( isset( $woo_options['woo_footer_top'] ) && $woo_options['woo_footer_top'] == 'true' ) {
						echo wpautop( stripslashes( $woo_options['woo_footer_top_text'] ) );
				} else { ?>
					<p><?php bloginfo(); ?> &copy; <?php echo date( 'Y' ); ?>. <?php _e( 'All Rights Reserved.', 'woothemes' ); ?></p>
				<?php } ?>
				</div>

				<div id="credit">
		        <?php if( isset( $woo_options['woo_footer_bottom'] ) && $woo_options['woo_footer_bottom'] == 'true' ) {
		        	echo wpautop( stripslashes( $woo_options['woo_footer_bottom_text'] ) );
				} else { ?>
					<p><a href="<?php echo esc_url( 'http://www.woothemes.com/' ); ?>"><img src="<?php echo esc_url( get_template_directory_uri() . '/images/woothemes.png' ); ?>" alt="WooThemes" /></a></p>
				<?php } ?>
				</div>

			</div><!-- /.wrapper -->
			</main><!-- end 3d nav container -->
			<!-- 3d navigation -->
			<nav class="cd-3d-nav-container">
				<ul class="cd-3d-nav">
					<li>
						<a href="lyftu"><i class="fa fa-university fa-2x"></i><br>LyftU</a>
					</li>

					<li>
						<a href="meme-maker"><i class="fa fa-paint-brush fa-2x"></i><br>Meme Maker</a>
					</li>

					<li>
						<a href="http://lyft.biz.vistaprint.com"><i class="fa fa-shopping-cart fa-2x"></i><br>Store</a>
					</li>

					<li>
						<a href="/#testimonials-holder"><i class="fa fa-trophy fa-2x"></i><br>Praise</a>
					</li>

					<li>
						<a href="http://blog.lyft.com/"><i class="fa fa-comments fa-2x"></i><br>Blog</a>
					</li>

					<li>
						<a href="contact"><i class="fa fa-paper-plane fa-2x"></i><br>Contact</a>
					</li>
				</ul> <!-- .cd-3d-nav -->

				<span class="cd-marker color-1"></span>	
			</nav> <!-- .cd-3d-nav-container -->

		</footer><!-- /#footer  -->

	</div><!-- /#footer-wrapper -->

	</div><!-- /#inner-wrapper -->
</div><!-- /#wrapper -->
<?php wp_footer(); ?>
<?php woo_foot(); ?>
<script src="<?php echo get_stylesheet_directory_uri(); ?>/js/main.js"></script> <!-- Resource jQuery -->
</body>
</html>