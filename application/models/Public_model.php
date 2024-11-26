<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Public_model extends CI_Model
{
    public function getAccount($username)
    {
        // Ambil data akun berdasarkan username
        $account = $this->db->get_where('user_accounts', ['username' => $username])->row_array();
        if (!$account) {
            return null; // Mengembalikan null jika akun tidak ditemukan
        }

        $e_id = $account['employee_id'];

        $this->db->select('employee.employee_id AS id,
                           employee.employee_name AS name,
                           employee.gender AS gender,
                           employee.shift_id AS shift,
                           employee.image AS image,
                           employee.birth_date AS birth_date,
                           employee.hire_date AS hire_date,
                           employee.department_id AS department_id');
        $this->db->from('employee');
        $this->db->join('department', 'employee.department_id = department.department_id', 'left');
        $this->db->where('employee.employee_id', $e_id);

        return $this->db->get()->row_array();
    }

    public function get_attendance($start, $end, $dept)
    {
        $this->db->select('attendance.attendance_date AS attendance_date,
                           attendance.shift_id AS shift_id,
                           employee.employee_name AS employee_name,
                           attendance.in_status AS in_status,
                           attendance.in_time AS in_time,
                           attendance.out_time AS out_time,
                           attendance.out_status AS out_status,
                           shift.start_time AS shift_start,
                           shift.end_time AS shift_end');
        $this->db->from('attendance');
        $this->db->join('employee', 'attendance.employee_id = employee.employee_id');
        $this->db->join('shift', 'attendance.shift_id = shift.shift_id');
        $this->db->where('attendance.department_id', $dept);
        $this->db->where('attendance.attendance_date >=', $start);
        $this->db->where('attendance.attendance_date <=', $end);
        $this->db->where('attendance.presence_status', 1);
        $this->db->order_by('attendance.attendance_date', 'ASC');

        $attendance = $this->db->get()->result_array();

        return $attendance;
    }


    public function getAllEmployeeData($username)
    {
        // Ambil data akun berdasarkan username
        $data = $this->db->get_where('user_accounts', ['username' => $username])->row_array();
        if (!$data) {
            return null; // Mengembalikan null jika akun tidak ditemukan
        }

        $e_id = $data['employee_id'];

        $this->db->select('employee.employee_id AS id,
                           employee.employee_name AS name,
                           employee.email AS email,
                           employee.gender AS gender,
                           employee.image AS image,
                           employee.birth_date AS birth_date,
                           employee.hire_date AS hire_date,
                           department.department_name AS department');
        $this->db->from('employee');
        $this->db->join('department', 'employee.department_id = department.department_id', 'left');
        $this->db->where('employee.employee_id', $e_id);

        return $this->db->get()->row_array();
    }

    public function get_employee_department($employee_id)
    {
        $this->db->select('department_id');
        $this->db->from('attendance');
        $this->db->where('employee_id', $employee_id);

        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->row()->department_id : null;
    }

    public function get_employee_shift($employee_id)
    {
        $this->db->select('shift_id');
        $this->db->from('employee');
        $this->db->where('employee_id', $employee_id);

        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->row()->shift_id : null;
    }
}
