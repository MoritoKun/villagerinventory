<?php

class M_login extends CI_Model{

  public function insert($tabel,$data){
    $this->db->insert($tabel,$data);
  }

  public function cek_username($tabel,$username){
    return $this->db->select('username')
             ->from($tabel)
             ->where('username',$username)
             ->get()->result();
  }

  public function cek_user($tabel,$username){
    return  $this->db->select('*')
               ->from($tabel)
               ->where('username',$username)
               ->get();
  }

  public function idgambar($username)
  {
    $query = $this->db->select()
                      ->from('user')
                      ->where('username',$username)
                      ->get()->row();
    return $query->id;
  }

  public function edit_user($where, $data)
	{
		$this->db->where($where);
		return $this->db->update('user', $data);
	}

  public function update_password($user_id)
  {
      $data = [
          "password" => htmlspecialchars(password_hash($this->input->post('new_password1'), PASSWORD_DEFAULT))
      ];

      $this->db->where('id_username', $user_id);
      $this->db->update('user', $data);
  }
}
 ?>
