<?php
/**
 * 
 * Render Callback For Breadcrumb
 * 
 */

function wptravel_block_breadcrumb_render( $attributes ) {
	ob_start();
	
?>
	<style>
		<?php if( !empty( $attributes['textColor'] ) || !empty( $attributes['textAlign'] ) || !empty( $attributes['textSize'] ) ){ ?>
			#wptravel-breadcrumb-block{
				<?php 
					if( !empty( $attributes['textColor'] ) ){
				?>
					color: <?php echo esc_attr( $attributes['textColor'] ); ?>;
				<?php }	?>
				<?php 
					if( !empty( $attributes['textAlign'] ) ){
				?>
					text-align: <?php echo esc_attr( $attributes['textAlign'] ); ?>;
				<?php }	?>
				<?php 
					if( !empty( $attributes['textSize'] ) ){
				?>
					font-size: <?php echo esc_attr( $attributes['textSize'] ); ?>px;
				<?php }	?>
			}
		<?php }	?>
		<?php if( ! empty( $attributes['linkColor'] ) ){ ?>
			#wptravel-breadcrumb-block a{			
				color: <?php echo esc_attr( $attributes['linkColor'] ); ?>!important;			
			}
		<?php }	?>
		<?php 
			if( ! empty( $attributes['separatorColor'] ) ){
		?>	
			#wptravel-breadcrumb-block .trail-items li.trail-item::after {			
				color: <?php echo esc_attr( $attributes['separatorColor'] ); ?>!important;	
			}
		<?php }	?>	

		#wptravel-breadcrumb-block .trail-items li.trail-item::after {
			content: "<?php echo esc_attr( $attributes['seperator'] ); ?>";
		}
	
	</style>
	<?php if( get_the_id() ): ?>
		<div id="wptravel-breadcrumb-block" class="wptravel-block-wrapper">
			<?php
				if( function_exists('wp_travel_blocks_breadcrumb_trail') ){
					wp_travel_blocks_breadcrumb_trail(); 
				} 			
			?>
		</div>
		<?php else: ?>
		<div id="wptravel-breadcrumb-block" class="wptravel-block-wrapper">
			<nav role="navigation" aria-label="Breadcrumbs" class="breadcrumb-trail breadcrumbs" itemprop="breadcrumb">
				<ul class="trail-items" itemscope="" itemtype="http://schema.org/BreadcrumbList">
					<meta name="numberOfItems" content="3">
					<meta name="itemListOrder" content="Ascending">
					<li itemprop="itemListElement" itemscope="" itemtype="http://schema.org/ListItem" class="trail-item trail-begin">
						<a href="http://main.local/" rel="home" itemprop="item"><span itemprop="name"><?php echo esc_html__( 'Home', 'wp-travel-blocks' ); ?></span></a>
						<meta itemprop="position" content="1">
					</li>
					<li itemprop="itemListElement" itemscope="" itemtype="http://schema.org/ListItem" class="trail-item">
						<a href="http://main.local/itinerary/" itemprop="item"><span itemprop="name"><?php echo esc_html__( 'Blog', 'wp-travel-blocks' ); ?></span></a>
						<meta itemprop="position" content="2">
					</li>
					<li itemprop="itemListElement" itemscope="" itemtype="http://schema.org/ListItem" class="trail-item trail-end">
						<span itemprop="item"><span itemprop="name"><?php echo esc_html__( 'Post Title', 'wp-travel-blocks' ); ?></span></span>
						<meta itemprop="position" content="3">
					</li>
				</ul>
			</nav>
		</div>
	<?php
	endif;
	$html = ob_get_clean();

	return $html;
}
