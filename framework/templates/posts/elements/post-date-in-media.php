<?php
/**
 * Post Date
 *
 * @package Wolmart WordPress Framework
 * @since 1.0
 * @version 1.0
 */
?>
<div class="post-calendar">
	<span class="post-day"><?php echo esc_html( get_the_time( 'd', get_the_ID() ) ); ?></span>
	<span class="post-month"><?php echo esc_html( get_the_time( 'M', get_the_ID() ) ); ?></span>
</div>
