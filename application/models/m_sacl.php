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

		//kalau 2-2 login attempt wujud pilih gmail
		if($this->input->post() OR $this->input->get()){
			
			$this->db->select('id, username, fullname, email, tags');
			$this->db->from('users');
			$this->db->where($where);
			$query = $this->db->get();

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

				$this->session->set_userdata($res[0]); //set session for user details

				return TRUE;

			}else{
				toshout(array('Login Error. Pastikan email anda benar dan tepat.'=>'error'));
			}
		}
	}

	public function test_acl(){
		if(!$this->uri->segment(1)) $site_url = '';
		if($this->uri->segment(1)) $site_url = $this->uri->segment(1);
		if($this->uri->segment(2)) $site_url .= '/'.$this->uri->segment(2);
		
		//make list/public, list/private and showable features
		$this->db->where(array('access !='=>0));
		$features = $this->db->get('features');

		foreach($features->result_array() as $feature){
			if($feature['access'] == 1) $feat['public'][$feature['id']] = $feature['site_url'];
			if($feature['access'] == 2) $feat['private'][$feature['id']] = $feature['site_url'];
			if($feature['access'] == 3) $cont[$feature['id']] = $feature['site_url'];

			if($feature['dashboard'] == 1) $show[$feature['id']] = $feature['site_url'];
			$all_feature[$feature['id']] = $feature;
		}

		$public = array_flip($feat['public']);
		$public[''] = 0;

		$public = array_key_exists($site_url, $public);

		if(!$public){
			//kalau session hilang means its auto logout. Redirect to login
			if($this->session->userdata('id') == FALSE && $site_url != 'login'){
				toshout(array('Please login before proceed:'=>'error'));
				redirect('login');
			}

			//calculate the controlled one
			$this->db->where(array('type'=>'users','type_id'=>$this->session->userdata('id')));
			foreach($this->session->userdata('tags_id') as $tags){
				$this->db->or_where(array('type'=>'tags','type_id'=>$tags));
			}
			$acl = $this->db->get('access');

			if($acl->num_rows() > 0){ //kalau ader control, kalau takder create dummy controlled
				
				//dapatkan semua controlled features yang dia boleh access
				foreach($acl->result_array() as $acc){
					$controlled_feats[$acc['feature_id']] = $all_feature[$acc['feature_id']]['site_url'];
				}

				//check kalau only class (site_url takder /), enable all methods
				foreach($controlled_feats as $id=>$url){
					if(strpos($url, '/') === FALSE){
						$this->db->or_like('site_url',$url);
					}
				}
				$qcont=$this->db->get('features');
				foreach($qcont->result_array() as $qs){
					$controlled_feats[$qs['id']] = '';
				}



				$controlled_feats = array_keys($controlled_feats);



				foreach($controlled_feats as $feat_id){
					if(array_key_exists($feat_id, $cont)) $feat['controlled'][$feat_id] = $cont[$feat_id];
				}

				$all = array_key_exists(0, $feat['controlled']);
			}else{
				$feat['controlled'] = array();
				$all = FALSE;
			}	

			//all granted features
			foreach($feat as $type=>$f){
				foreach($f as $id=>$ff){
					$granted[$id] = $ff;
				}
			}

			

			//check if granted can be shown on dashboard
			foreach($show as $fid=>$fe){
				if($all || array_key_exists($fid, $granted)) $board[$fid] =$fe;
			}
			//$dash = $feat;
			$dash['dashboard'] = $board;

			//dumper($dash);

			//set the session for dashboar
			$this->session->set_userdata($dash);

			//Start making the boolean array
			$private = array_flip($feat['private']);
			$controlled = array_flip($feat['controlled']);

			if($site_url == '') $home = TRUE;
			else $home = FALSE;
			
			$private = (array_key_exists($site_url, $private) AND $this->session->userdata('username'));
			$controlled = array_key_exists($site_url, $controlled);

			$access = array(
								'all'=>$all,
								'public'=>$public,
								'private'=>$private,
								'controlled'=>$controlled,
								'home'=>$home
							);

			//dumper($access);

			if(array_search(TRUE, $access)){
				//if access granted
			}else{
				toshout(array('<h3>Access Denied.</h3> <p>Anda tidak mempunyai akses ke atas halaman tersebut. Rujuk pengesahan dengan pihak IT.</p>'=>'error'));
				redirect('main/error_404');
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
