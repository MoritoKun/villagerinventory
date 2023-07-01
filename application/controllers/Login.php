<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {

	public function __construct(){
		parent::__construct();
		$this->load->model('M_login');
	}
	public function index()
	{
		    $data['token_generate'] = $this->token_generate();
			$this->session->set_userdata($data);
			$this->load->view('login/login',$data);
	}

	public function token_generate(){
		return $tokens = md5(uniqid(rand(), true));
	}

	public function register(){
		$this->load->view('login/register');
	}

	public function proses_login(){
		$this->form_validation->set_rules('username','Username','required');
		$this->form_validation->set_rules('password','Password','required');

		if($this->form_validation->run() == TRUE){
			$username = $this->input->post('username',TRUE);
			$password = $this->input->post('password',TRUE);

		if($this->session->userdata('token_generate') === $this->input->post('token'))
		{
			$cek =  $this->M_login->cek_user('user',$username);
			if( $cek->num_rows() != 1){
				$this->session->set_flashdata('msg','Username dan Password Salah');
				redirect(base_url());
			}else {

				$isi = $cek->row();
				if(password_verify($password,$isi->password) === TRUE){
					$data_session = array(
									'id' => $isi->id,
									'name' => $username,
									'email' => $isi->email,
									'status' => 'login',
									'role' => $isi->role,
									'last_login' => $isi->last_login
					);

					$this->session->set_userdata($data_session);

					$this->M_login->edit_user(['username' => $username],['last_login' => date('d-m-Y G:i')]);

						if($isi->role == 1){
							redirect(base_url('admin'));
						}else {
							redirect(base_url('user'));
						}

				}else {
					$this->session->set_flashdata('msg','Username Dan Password Salah');
					redirect(base_url());
				}
			}
		}else {
			redirect(base_url());
		}
	}
}

	public function ganti_password()
    {
        $data['user'] = $this->db->get_where('user', ['username' => $this->session->userdata('username')])->row_array();
        $user_id = $data['user']['id_username'];
        $user_password = $data['user']['password'];
        $data['title'] = 'Ganti Password';
        $data['nama'] = $data['user']['namaUsaha'];

        $this->form_validation->set_rules(
            'old_password',
            'Password',
            'required|trim',
            [
                'required' => 'Password Lama Tidak Boleh Kosong',
            ]
        );

        $this->form_validation->set_rules(
            'new_password1',
            'Password',
            'required|trim|matches[new_password2]',
            [
                'required' => 'Password Baru Tidak Boleh Kosong',
                'matches' => 'Konfirmasi Password Tidak Sesuai',
            ]
        );

        $this->form_validation->set_rules(
            'new_password2',
            'Password',
            'required|trim'
        );
        $password = $this->input->post('old_password');
        if ($this->form_validation->run() == false) {
            $this->load->view('template/header', $data);
            $this->load->view('template/sidebar', $data);
            $this->load->view('profil/gantipass', $data);
            $this->load->view('template/footer');
        } else {
            if (password_verify($password, $user_password)) {
                $this->Model_Profil->update_password($user_id);
                $this->session->set_flashdata('pesan', 'Update Password');
                redirect('profil');
            } else {
                $this->session->set_flashdata('old_password', 'Password Lama Tidak Sesuai');
                redirect('profil/ganti_password');
            }
        }
    }
}