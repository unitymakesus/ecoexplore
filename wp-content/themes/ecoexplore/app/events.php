<?php

namespace App;

if ( function_exists( 'tribe_get_events' ) ) {
	/**
	 * Adds tribe_events_cat terms to body class on single-event views.
	 *
	 * @since Nov 9 2015
	 *
	 * @link http://theeventscalendar.com/?p=1023168
	 */
	function tribe_support_1023168( $classes ) {
		global $wp_query;
		$event_id = $wp_query->get_queried_object_id();
		if ( ! tribe_is_event( $event_id ) || ! is_singular( 'tribe_events' ) )
			return $classes;
		$event_cats = tribe_get_event_cat_slugs( $event_id );
		if ( ! is_array( $event_cats ) || empty( $event_cats ) )
			return $classes;
		foreach ( $event_cats as $key => $slug ) {
			$classes[] = sprintf( 'tribe-events-category-%s', sanitize_html_class( $slug ) );
		}

		return $classes;
	}
	add_filter( 'body_class', __NAMESPACE__ . '\\tribe_support_1023168' );
}
