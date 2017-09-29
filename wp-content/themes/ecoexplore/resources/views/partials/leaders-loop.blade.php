<table class="striped">
  <tbody>
    @php
      $leaders_query = new WP_User_Query([
        'number' => 10,
        'role' => 'Subscriber',
        'meta_key' => 'total_points',
        'orderby' => 'meta_value_num',
        'order' => 'DESC'
      ]);

      $leaders = $leaders_query->get_results();

      $i = 1;
    @endphp

    @if (!empty($leaders))
      @foreach ($leaders as $leader)
        @php ($user = get_userdata($leader->ID))
        <tr>
          <td class="rank">{{ $i }}</td>
          <td class="name">{{ $user->display_name }}</td>
          <td class="score">{{ get_user_meta($leader->ID, 'total_points', true) }}</td>
        </tr>
        @php ($i++)
      @endforeach
    @endif
  </tbody>
</table>
