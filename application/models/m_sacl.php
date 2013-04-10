<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class M_sacl extends CI_Model {

	public function __construct()
   {
        parent::__construct();

       $this->test_acl(); 

   }

   	//Google Outh Config
	var $g_response_type = 'code';		//	default value - do not change
	var $g_scope = 'https://www.googleapis.com/auth/userinfo.email';	//	Full list at https://code.google.com/oauthplayground/
	//var $g_redirect = 'http://user.pts.com.my/index.php/test';	//	same as configured in Google developer account
	var $g_redirect = '';	//	same as configured in Google developer account
	var $g_clientid = '';	//	same as given by Google in Google developer account
	var $g_clientsecret = '';	//	same as given by Google in Google developer account
	var $g_granttype = 'authorization_code';	//	default value - do not change
	var $g_state = 'ok';	//	any value


	public function google_login_link(){
		
		return 'https://accounts.google.com/o/oauth2/auth?response_type='.$this->g_response_type.'&scope='.$this->g_scope.'&redirect_uri='.$this->g_redirect.'&client_id='.$this->g_clientid.'&state='.$this->g_state;
	}

	public function login()
	{
		//login biasa
		if($this->input->post()){
			$this->load->library('encrypt');
			
			$where = array(
							$this->config->item('sacl_login_column')=>$this->input->post('email'),
							'password'=>hashim($this->input->post('password'))
						);
		}

		//login google
		if($this->input->get('state')==$this->g_state) {
			$this->load->library('Curl');
			$api_call = array(
							'code' => $this->input->get('code'),
							'client_id' => $this->g_clientid,
							'client_secret' => $this->g_clientsecret,
							'redirect_uri' => $this->g_redirect,
							'grant_type' => $this->g_granttype
							  );
			$api_post = $this->curl->simple_post('https://accounts.google.com/o/oauth2/token', $api_call);
			$api = json_decode($api_post);
			$data['token'] = $api->access_token;
			
			$response = $this->curl->simple_get('https://www.googleapis.com/oauth2/v2/userinfo',array('access_token'=>$data['token']));
			
			$user = json_decode($response);
			
			$email = $user->email;
			
			$where = array(
							'email'=>$email,
							//'password'=>hashim($this->input->post('password'))
						);
			
		}


		//kalau 2-2 login attempt wujud pilih username
		if($this->input->post() OR $this->input->get()){
			
			//where organisation_id;
			if($this->input->post('email') !== 'root' AND $this->input->post('email') !== 'superadmin'){
				$org = get_organisation();
				$where['organisation_id'] = $org['id'];
			}

			//$this->db->select('id, username, fullname, email, tags');
			$this->db->from('users');
			$this->db->where($where);
			$query = $this->db->get();

			//dumper($this->db->last_query());

			//check if exists. If not throw error
			if($query->num_rows() == 1){
				$res = $query->result_array();
				
				//create tag
				$tags = unserialize($res[0]['tags']);

				foreach($tags as $tag_id){
					$this->db->or_where(array('id'=>$tag_id));
				}

				$q = $this->db->get('tags');
				foreach($q->result_array() as $tags){
					$tagid[] = $tags['id'];
				}

				$res[0]['tags_id'] = $tagid;

				//get users_data details
				$this->db->where('user_id', $res[0]['id']);
				$query = $this->db->get('users_data');
				$users_data = $query->row_array();

				unset($users_data['id']);
				unset($res[0]['password']);
				$sess = $res[0]+$users_data;
				$this->session->set_userdata($sess); //set session for user details

				return TRUE;

			}else{
				toshout(array('Login Error. Pastikan maklumat anda benar dan tepat.'=>'error'));
			}
		}
	}


	function test_acl(){

		if($this->uri->segment(1)){ //kalau xder site url means home. Home is ok
			if($this->uri->segment(1)) $site_url = $this->uri->segment(1);
			if($this->uri->segment(2)) $site_url .= '/'.$this->uri->segment(2);

			$root = false;

			if($this->session->userdata('tags_id') && $this->session->userdata('id')){ //kalau login
				//kalau tag_id = 0 => developer => bagi semua!
				if(array_search(1, $this->session->userdata('tags_id')) !== FALSE) $root = true;
				//kalau bukan root, filter features by public, private and controlled
				if(!$root){
					foreach($this->session->userdata('tags_id') as $tags_id){
						$where[] = "(`type`='tags' AND `type_id`=".$tags_id.")";
					}
					$where[] = "(`type`='users' AND `type_id`=".$this->session->userdata('id').")";

					$this->db->select('feature_id');
					$this->db->where(implode(' OR ', $where));
					$qcontrolled_id = $this->db->get('access');

					$where = array();
					foreach($qcontrolled_id->result_array() as $ids){
						$this->db->or_where("(`access`=3 AND `id`=".$ids['feature_id'].')');
					}

					//$where = $controlled_id;
					if(!$root) $this->db->or_where("(`access`=2)");
				}
				
			}

			if(!$root) $this->db->or_where("(`access`=1)");

			$qfeatures = $this->db->get('features'); //dapatkan semua yang boleh masuk
			
			$class_match = false;
			$url_match = false;
			//dumper($this->db->last_query());

			foreach($qfeatures->result_array() as $rows){
				$oks[$rows['id']] = $rows['site_url'];
				//if($rows['dashboard']) $dash[$rows['id']] = $rows;
				if($rows['site_url'] == $this->uri->segment(1)) $class_match = TRUE;
				//show on dashboard
				if($rows['dashboard']) $board[$rows['id']] = $rows['site_url'];
			}

			$dash['dashboard'] = $board;

			//check class
			if(array_search($site_url, $oks)) $url_match = TRUE;

			if(!($class_match OR $url_match OR $root)){ //if no match, tendang dia!
				
				//kalau x login suh dia login
				if($this->session->userdata('tags_id') && $this->session->userdata('id')){
					toshout(array("Access denied. Please login:"=>'error'));
					//redirect('login');
				}else{
					//kalau login dia error go 404
					toshout(array("Access denied. Please check your access card."=>'error'));
					//redirect('main/404');
				}
			}else{
				$this->session->set_userdata($dash);
				//show_sess();
			}
		}
	}

	public function hadir(){
		if($this->session->userdata('username')) return TRUE;
		else{ 
			//toshout(array('Tidak hadir'=>'error'));
			return FALSE;
		}
	
	}

	public function get_tags($plain = FALSE){
		$this->db->order_by('id','desc');
		$query = $this->db->get('tags');

		if($plain){
			foreach($query->result_array() as $row){
				$tags[$row['id']] = $row; 
			}
		}
		else{
			foreach($query->result_array() as $row){
				$tags[$row['key']][$row['id']] = $row['value'];
			}
		}

		return $tags;
	}

	public function get_users(){
		$query = $this->db->get('users');
		foreach($query->result_array() as $row){
			$ret[$row['id']] = $row;
		}

		return $ret;

	}

	function is_unique($str,$table,$column){
		$this->db->where(array($column=>$str));
		$query = $this->db->get($table);

		if($query->num_rows == 0) return TRUE;
		else return FALSE;
	}
	

}
