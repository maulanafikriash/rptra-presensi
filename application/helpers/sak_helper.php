<?php

function is_logged_in()
{
  $ci = get_instance();
  if (!$ci->session->userdata('username')) {
    redirect('auth');
  } else {
    $role_id = $ci->session->userdata('user_role_id'); // Ubah menjadi user_role_id
    $menu = $ci->uri->segment(1);

    $queryMenu = $ci->db->get_where('user_menu', ['menu' => $menu])->row_array();
    $menu_id = $queryMenu['user_menu_id']; // Ubah id menjadi user_menu_id
    $userAccess = $ci->db->get_where('user_access', ['user_role_id' => $role_id, 'user_menu_id' => $menu_id]); // Ubah role_id dan menu_id

    if ($userAccess->num_rows() < 1) {
      redirect('auth/blocked');
    }
  }
}

function is_weekends()
{
  date_default_timezone_set('Asia/Jakarta');
  $today = date('l', time());
  $weekends = ['Saturday', 'Sunday'];
  return in_array($today, $weekends);
}

function is_checked_in()
{
  date_default_timezone_set('Asia/Jakarta');
  $ci = get_instance();
  $username = $ci->session->userdata('username');
  $today = date('Y-m-d', time());

  $query = "SELECT attendance.in_time 
              FROM attendance
              INNER JOIN user_accounts ON attendance.username = user_accounts.username 
              WHERE user_accounts.username = '$username'
                AND attendance.attendance_date = '$today'";

  $ci->db->query($query);
  $rows = $ci->db->affected_rows();

  return $rows > 0; // Ubah untuk menggunakan return langsung
}

function is_checked_out()
{
  date_default_timezone_set('Asia/Jakarta');
  $ci = get_instance();
  $username = $ci->session->userdata('username');
  $today = date('Y-m-d', time());

  $query = "SELECT * 
              FROM attendance
              INNER JOIN user_accounts ON attendance.username = user_accounts.username 
              WHERE (attendance.out_time IS NOT NULL)
                AND (attendance.out_status IS NOT NULL OR attendance.out_status != '')
                AND (user_accounts.username = '$username')
                AND (attendance.attendance_date = '$today')";

  $ci->db->query($query);
  $rows = $ci->db->affected_rows();

  return $rows > 0; // Ubah untuk menggunakan return langsung
}
