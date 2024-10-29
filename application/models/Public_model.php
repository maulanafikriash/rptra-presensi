<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Public_model extends CI_Model
{
    public function getAccount($username)
    {
        $account = $this->db->get_where('user_accounts', ['username' => $username])->row_array();
        $e_id = $account['employee_id'];
        $query = "SELECT  employee.employee_id AS `id`,
                          employee.employee_name AS `name`,
                          employee.gender AS `gender`,   
                          employee.shift_id AS `shift`,
                          employee.image AS `image`,
                          employee.birth_date AS `birth_date`,
                          employee.hire_date AS `hire_date`,
                          employee.department_id AS `department_id`
                  FROM employee
              LEFT JOIN department ON employee.department_id = department.department_id
              WHERE employee.employee_id = '$e_id'";

        return $this->db->query($query)->row_array();
    }

    public function get_attendance($start, $end, $dept)
    {
        $query = "SELECT 
                      attendance.attendance_date AS attendance_date,  
                      attendance.shift_id AS shift_id,
                      employee.employee_name AS employee_name,
                      attendance.notes AS notes,
                      attendance.in_status AS in_status,
                      attendance.in_time AS in_time,
                      attendance.out_time AS out_time,
                      attendance.out_status AS out_status
                  FROM 
                      attendance
                  INNER JOIN employee ON attendance.employee_id = employee.employee_id
                  WHERE 
                      attendance.department_id = ? 
                      AND attendance.attendance_date BETWEEN ? AND ? 
                  ORDER BY 
                      attendance.attendance_date ASC";
    
        return $this->db->query($query, [$dept, $start, $end])->result_array();
    }
    

    public function getAllEmployeeData($username)
    {
        // get employee id from user_accounts table
        $data = $this->db->get_where('user_accounts', ['username' => $username])->row_array();
        $e_id = $data['employee_id'];

        // Join Query
        $query = "SELECT  employee.employee_id AS `id`,
                          employee.employee_name AS `name`,
                          employee.email AS `email`,
                          employee.gender AS `gender`,
                          employee.image AS `image`,
                          employee.birth_date AS `birth_date`,
                          employee.hire_date AS `hire_date`,
                          department.department_name AS `department`
                         FROM employee
              LEFT JOIN department ON employee.department_id = department.department_id
              WHERE employee.employee_id = $e_id";
        // get employee data from employee table using employee id and return the row
        return $this->db->query($query)->row_array();
    }

    public function get_employee_department($employee_id)
    {
        $this->db->select('department_id');
        $this->db->from('attendance'); 
        $this->db->where('employee_id', $employee_id);
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            return $query->row()->department_id;
        }

        return null; 
    }

    public function get_employee_shift($employee_id)
    {
        $this->db->select('shift_id');
        $this->db->from('employee'); 
        $this->db->where('employee_id', $employee_id);
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            return $query->row()->shift_id;
        }

        return null; 
    }
}
