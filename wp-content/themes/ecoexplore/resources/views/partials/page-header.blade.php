@if (!is_page('field-season'))
  @php ($banner = get_the_post_thumbnail_url(get_the_id(), 'full'))

  <div class="page-header" style="background-image: url('{{ $banner }}')"></div>
@endif
