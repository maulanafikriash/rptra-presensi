<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * @property CI_Loader $load
 * @property CI_Form_validation $form_validation
 * @property CI_Session $session
 * @property CI_upload $upload
 * @property CI_DB $db
 * @property CI_Input $input
 * @property Public_model $Public_model
 */

class Auth extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('form_validation');
    }

    public function index()
    {
        if ($this->session->userdata('username')) {
            $role_id = $this->session->userdata('user_role_id'); // Ubah 'role_id' menjadi 'user_role_id'
            switch ($role_id) {
                case 1:
                    redirect('admin');
                    break;
                case 2:
                    redirect('profile');
                    break;
            }
        }
        // Login Page
        $d['title'] = 'Login Page';

        // Form Validation
        $this->form_validation->set_rules('username', 'Username', 'required|trim');
        $this->form_validation->set_rules('password', 'Password', 'required|trim|min_length[6]');

        if ($this->form_validation->run() == false) {
            $this->load->view('templates/auth_header', $d);
            $this->load->view('auth/index');
            $this->load->view('templates/auth_footer');
        } else {
            $this->_login();
        }
    }

    private function _login()
    {
        $username = $this->input->post('username');
        $password = $this->input->post('password');

        $user = $this->db->get_where('user_accounts', ['username' => $username])->row_array(); 
        if ($user) {
            if (password_verify($password, $user['password'])) {
                $data = [
                    'username' => $user['username'],
                    'user_role_id' => $user['user_role_id'] 
                ];
                $this->session->set_userdata($data);
                switch ($user['user_role_id']) { 
                    case 1:
                        redirect('admin');
                        break;
                    case 2:
                        redirect('profile');
                        break;
                }
            } else {
                $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">
                  Wrong password!</div>');
                redirect('auth');
            }
        } else {
            $this->session->set_flashdata('message', '<div class="alert alert-warning" role="alert">
            Username Not Found</div>');
            redirect('auth');
        }

        if ($user) {
        } else {
            $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">Wrong password or Invalid username!</div>');
            redirect('auth');
        }
    }

    public function logout()
    {
        $this->session->unset_userdata('username');
        $this->session->unset_userdata('user_role_id'); 
        $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">Logged Out!</div>');
        redirect('auth');
    }

    public function blocked()
    {
        $d['title'] = 'Access Blocked';
        $this->load->view('auth/blocked', $d);
    }
}
