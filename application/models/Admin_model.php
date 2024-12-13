<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Admin_model extends CI_Model
{
    public function getAdmin($username)
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
                           employee.hire_date AS hire_date');
        $this->db->from('employee');
        $this->db->where('employee.employee_id', $e_id);
        
        return $this->db->get()->row_array();
    }

    public function getDataForDashboard()
    {
        // Ambil data shift, employee, department, dan user
        $d['shift'] = $this->db->get('shift')->result_array();
        $d['c_shift'] = $this->db->get('shift')->num_rows();
        $d['employee'] = $this->db->get('employee')->result_array();
        $d['c_employee'] = $this->db->get('employee')->num_rows();
        $d['department'] = $this->db->get('department')->result_array();
        $d['c_department'] = $this->db->get('department')->num_rows();
        $d['users'] = $this->db->get('user_accounts')->result_array(); 
        $d['c_users'] = $this->db->get('user_accounts')->num_rows();

        return $d;
    }

    public function getDepartment()
    {
        // Mengambil data jumlah karyawan per departemen dengan LEFT JOIN
        $this->db->select('department.department_name AS d_name,
                           department.department_id AS d_id,
                           COUNT(attendance.employee_id) AS d_quantity');
        $this->db->from('department');
        $this->db->join('attendance', 'department.department_id = attendance.department_id', 'left');
        $this->db->group_by('d_name');

        return $this->db->get()->result_array();
    }

    public function getDepartmentEmployees($d_id)
    {
        $this->db->select('attendance.employee_id AS e_id,
                           employee.employee_name AS e_name,
                           employee.image AS e_image,
                           employee.hire_date AS e_hdate');
        $this->db->from('attendance');
        $this->db->join('employee', 'attendance.employee_id = employee.employee_id');
        $this->db->where('attendance.department_id', $d_id);

        return $this->db->get()->result_array();
    }
}
