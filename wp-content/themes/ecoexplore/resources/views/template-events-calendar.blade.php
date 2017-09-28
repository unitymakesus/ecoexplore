{{--
  Template Name: Events Template
  This file is the basic wrapper template for all the views if 'Events Template'
  is selected in Events -> Settings -> Template -> Events Template.

  @package TribeEventsCalendar
--}}

@extends('layouts.app')

@section('content')
	<div id="tribe-events-pg-template" class="tribe-event-pg-template">
		{{ tribe_events_before_html() }}
		{{ tribe_get_view() }}
		{{ tribe_events_after_html() }}
	</div> <!-- #tribe-events-pg-template -->
@endsection
