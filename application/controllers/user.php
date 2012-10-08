<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User extends CI_Controller {

	function setting(){

		if($this->input->post()){
			$this->db->where(array('id'=>$this->session->userdata('id')));
			$query = $this->db->get('users');
			$res = $query->result_array();

			if($this->input->post('old_password') AND $this->input->post('new_password')){
				//renew password
				if( hashim($this->input->post('old_password')) == $res[0]['password']) $_POST['password'] = hashim($this->input->post('new_password'));
				else toshout(array('Password lama tidak tepat. Password tidak dikemaskini.Sila cuba sekali lagi.'=>'error'));
			}

			$where = array('id' => $this->session->userdata('id'));

			unset($_POST['old_password']);
			unset($_POST['new_password']);

			$this->db->where($where);
			$this->db->update('users', $this->input->post());

			toshout(array('Maklumat anda telah dikemaskini.'=>'success'));
		}

		$this->load->helper('form');
		$this->db->where(array('id'=>$this->session->userdata('id')));
		$query = $this->db->get('users');

		$res = $query->result_array();

		$_POST = $res[0];
		unset($_POST['password']);

		
		//$_POST['password'] = $this->encrypt->decode($_POST['password'], $_POST['email']);

		$this->load->view('v_user_setting');
	}

	function retrieve(){
		$this->load->helper('form');
		if($this->input->post()){
			//dumper($this->input->post('email'));
			$this->load->helper('email');

			if(valid_email($this->input->post('email'))){
				$query = $this->db->get_where('users', array('email'=>$this->input->post('email')));

				if($query->num_rows() == 1){
					$res = $query->result_array();

					$msg = 'Password anda ialah: '.robot($res[0]['password']).' .Jangan lupa lagi.

Login di: http://pts.com.my/ppsv2 .

Sekian,';
					
					$this->load->library('email');

					$this->email->from('mis@pts.com.my', 'MIS');
					$this->email->to($res[0]['email']); 
					//$this->email->cc('another@another-example.com'); 
					//$this->email->bcc('them@their-example.com'); 

					$this->email->subject('PPS - Password');
					$this->email->message($msg);	

					$this->email->send();

					//send_email($res[0]['email'], 'PPS - Password', $msg);
					//dumper($msg);
					$_POST['email'] = '';
					toshout(array('Password anda telah sistem emailkan. Sila semak email.'=>'success'));
				}else{
					toshout(array('Email tersebut tidak wujud dalam sistem. Sila gunakan email yang betul.'=>'error'));
				}
			}else{
				toshout(array('Email tidak sah. Anda yakin itu adalah email?'=>'error'));
			}
			
		}
		$this->load->view('v_user_retrieve');
	}

	function hr(){
		if($this->input->post()){
			$child_name = $this->input->post('children_name');
			$child_age = $this->input->post('children_age');
			$child_ic = $this->input->post('children_ic');

			for($i = 0; $i < count($child_name) ; $i++){
				if($child_name[$i] != ''){
					$children_data['children_name'][$i] = $child_name[$i];
					$children_data['children_age'][$i] = $child_age[$i];
					$children_data['children_ic'][$i] = $child_ic[$i];
					
				}
			}
			
			$_POST['children_data'] = serialize($children_data);

			unset($_POST['children_name']);
			unset($_POST['children_age']);
			unset($_POST['children_ic']);
			
			//dumper($this->input->post());

			$this->db->where(array('user_id'=>$this->session->userdata('id')));
			$this->db->update('hr_data', $this->input->post());

			
		}

		unset($_POST);
		if($this->uri->segment(3)) $id = $this->uri->segment(3);
		else $id = $this->session->userdata('id');
		$this->db->where(array('user_id'=>$id));
		$query = $this->db->get('hr_data');
		
		$res = $query->result_array();
		$_POST = $res[0];
		
		//dumper(unserialize($this->input->post('children_data')));

		$this->load->view('v_user_hr');
	}

	function author(){

		//kalau post
		if($this->input->post()){
			
			$this->load->library('form_validation');

			$this->form_validation->set_rules('username', 'Username', 'required');
			$this->form_validation->set_rules('fullname', 'name', 'required');
			$this->form_validation->set_rules('hp', 'HP', 'required|numeric');
			$this->form_validation->set_rules('ic', 'IC', 'required|numeric');
			$this->form_validation->set_rules('email', 'Email', 'required|valid_email');

			if ($this->form_validation->run() == FALSE)
			{
				//$this->load->view('myform');
			}
			else
			{
				
				$user_data['hp'] = $this->input->post('hp');
				$user_data['ic'] = $this->input->post('ic');
				$user_data['waris1'] = $this->input->post('waris1');
				$user_data['ic_waris1'] = $this->input->post('ic_waris1');
				$user_data['hp_waris1'] = $this->input->post('hp_waris1');
				$user_data['address_waris1'] = $this->input->post('address_waris1');
				$user_data['waris2'] = $this->input->post('waris2');
				$user_data['ic_waris2'] = $this->input->post('ic_waris2');
				$user_data['hp_waris2'] = $this->input->post('hp_waris2');
				$user_data['address_waris2'] = $this->input->post('address_waris2');

				unset($_POST['hp']);
				unset($_POST['ic']);
				unset($_POST['waris1']);
				unset($_POST['ic_waris1']);
				unset($_POST['hp_waris1']);
				unset($_POST['address_waris1']);
				unset($_POST['waris2']);
				unset($_POST['ic_waris2']);
				unset($_POST['hp_waris2']);
				unset($_POST['address_waris2']);
				

				//kalau insert
				if($this->input->post('update') == FALSE){
					
					unset($_POST['update']);
					$this->db->insert('users', $this->input->post());

					$user_data['user_id'] = $this->db->insert_id();
					$this->db->insert('users_data', $user_data);
					toshout(array('Penulis baru telah berjaya dimasukkan ke dalam sistem PPS.'=>'success'));
				}else{//kalau edit
					unset($_POST['update']);
					$this->db->where(array("id"=>$this->uri->segment(3)));
					$this->db->update('users', $this->input->post());
					
					$this->db->where(array("user_id"=>$this->uri->segment(3)));
					$this->db->update('users_data', $user_data);
					toshout(array('Maklumat Penulis telah berjaya dikemaskini dalam sistem PPS.'=>'success'));
				}

			}
		}

		//kalau edit
		if($this->uri->segment(3)){
			$this->db->where(array('users.id'=>$this->uri->segment(3)));
			$this->db->join('users_data', 'users_data.user_id = users.id');
			$query = $this->db->get('users');
			$res = $query->result_array();
			$_POST = $res[0];


		}

		//sedut and show all authors list
		$this->db->like('tags', '88');
		$query = $this->db->get('users');

		$data['authors'] = $query->result_array();

		$this->load->view('v_user_author', $data);
	}

	function delete_author(){
		$this->db->where(array('id'=>$this->uri->segment(3)));
		$this->db->delete('users');

		$this->db->where(array('user_id'=>$this->uri->segment(3)));
		$this->db->delete('users_data');

		toshout(array('Maklumat penulis tersebut telah berjaya dipadam.'=>'notice'));
		redirect('user/author');
	}


}