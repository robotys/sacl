<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Sacl extends CI_Controller {

	public function sql(){
		ini_set('memory_limit', '-1');

		if($this->input->post('note')){
			//create new sqldump

			$this->load->dbutil();

			$filename =  date('Y-m-d_H.i.s').'_'.$_SERVER['SERVER_NAME'].'_'.str_replace(' ', '_', $this->input->post('note')).'.json';


			//get all tables name
			//get tables content

			if($this->input->post('pick') == false)$tables = ($this->db->list_tables());
			else{
				
				$tables = $this->input->post('pick');

			}

			foreach($tables as $table){
				// //get all metada
				// $fields = $this->db->list_fields();
				// foreach($fields as $field){

				// }
				$ff = array();
				$fields = $this->db->field_data($table);
				foreach($fields as $field){
					$ff[$field->name]['type'] = $field->type;
					$ff[$field->name]['default'] = $field->default;
					$ff[$field->name]['max_length'] = $field->max_length;
					$ff[$field->name]['primary_key'] = $field->primary_key;
					// $ff['']
				}

				$all[$table]['fields'] = $ff;

				//get all datas
				$query = $this->db->get($table);

				$all[$table]['rows'] = $query->result_array();


			}


			$datajson = json_encode($all);
			
			file_put_contents('./migrate/'.$filename, $datajson);
			
			toshout(array('Success'=>'success'));

		} 


		if($this->input->post('filename')){

			$tables = json_decode(file_get_contents('./migrate/'.$this->input->post('filename')), TRUE);

			$this->load->dbforge();
			foreach($tables as $tablename=>$item){
				// dumper($name);
				// dumper($item['fields']);

				//drop table if exist
				if($this->db->table_exists($tablename)){
					$this->dbforge->drop_table($tablename);
				}

				//remap fields
				$fields = array();
				foreach($item['fields'] as $fieldname=>$info){
					if($fieldname != 'changetime' && $fieldname != 'id'){
						$arr = array();
						$arr['type'] = strtoupper($info['type']);

						if($info['max_length'] != '') $arr['constraint'] = $info['max_length'];
						// dumper($fieldname.': '.$info['primary_key']);
						// if($info['primary_key'] === 1){
						// 	$arr['unsigned'] = TRUE;
						// 	$arr['auto_increment'] = TRUE;
						// }

						if($info['default'] != NULL) $arr['default'] = $info['default'];
						else $arr['null'] = TRUE;


						$fields[$fieldname] = $arr;
					}

			
				}

				//init field
				if(array_key_exists('id', $fields) === FALSE) $this->dbforge->add_field('id');
				$this->dbforge->add_key('id');

				$this->dbforge->add_field($fields);
				
				$this->dbforge->add_field('`changetime` TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL');	
				

				//create table
				$this->dbforge->create_table($tablename);
				//insert_batch
				
				$this->db->insert_batch($tablename, $item['rows']);
			}


		}

		//scan migrate dir and send filename array
		$dumps = scandir('./migrate');
		foreach($dumps as $dump){
			if(strpos($dump, '.json') !== false) $files[] = $dump;
		}



		$data['tables'] = $this->db->list_tables();
		$data['dumps'] = $files;

		// dumper($dumps);
		// dumper($files);
		// dumper($data);

		$this->load->view('v_sacl_sql', $data);
	}

// 	public function migrate(){
// 		//delete sql and migration files.
// 		unlink('./migrate.sql');
// 		//dump sql
// 		$this->load->dbutil();
// 		$backup =& $this->dbutil->backup(array('format'=>'txt')); 
// 		$this->load->helper('file');
// 		write_file('./migrate.sql', $backup); 

// 		//crea migration zip
// 		//if migrate all
// 		if($this->uri->segment(3) == 'all'){

// 			unlink('./migrate.zip');
			
// 			zip('./','./migrate.zip');
// 			toshout(array('File Prep done. Download here: <a href="'.base_url('migrate.zip').'">migrate.zip</a>'=>'success'));
// 		}

// 		$this->load->view('v_sacl_migrate');
// 	}

// 	public function email_migration(){
// 		$inputs['email'] = array('type'=>'text', 'display'=>'Receipient Email:', 'rules'=>'required|valid_email');
// 		$inputs['message'] = array('type'=>'textarea', 'display'=>'Some message:', 'rules'=>'required');


// 		if(rbt_valid_post($inputs)){
// 			unlink('./migrate.sql');
// 			//dump sql
// 			$this->load->dbutil();
// 			$backup =& $this->dbutil->backup(array('format'=>'txt')); 
// 			$this->load->helper('file');
// 			write_file('./migrate.sql', $backup); 

// 			//prep zip file
// 			//unlink('./migrate.zip');
// 			zip('./','./migrate.zip');

// 			//attach and email the file
// 			$this->load->library('email');
// 			$this->email->to($this->input->post('email'));
// 		    $this->email->from('system@beresbos.com');
// 		    $this->email->subject('[eLib] Updates & Migration Files');
// 		    $this->email->message($this->input->post('message').'

// Download here: '.base_url('migrate.zip'));
// 		   	//$this->email->attach('./migrate.zip');
// 		    $this->email->send();

// 			toshout(array('File Prep done. Email with download link has been sent to '.$this->input->post('email')=>'success'));
// 		}


// 		$data['inputs'] = $inputs;

// 		$this->load->view('v_sacl_email_migration', $data);
// 	}

// 	public function backup(){

// 		$this->load->dbutil();
// 		$backup =& $this->dbutil->backup();

// 		$filename = date('Y-m-d H_i_s').'.sql';
// 		file_put_contents('./migrate/'.$filename, $backup);
		
// 		toshout(array('Migration file has been saved to ./migrate/'.$filename=>'success'));

// 		redirect($this->input->server('HTTP_REFERER'));
// 	}

	public function login() //library reader
	{
		
		$data['google_login_link'] = $this->m_sacl->google_login_link();
		if($this->m_sacl->login()){ 
			if(!$this->session->userdata('goto')) redirect('main/dashboard');
			else redirect($this->session->userdata('goto'));
			//header('Location: '.$this->session->userdata('goto'));
		}
		$this->load->view('v_sacl_login', $data);
	}

	public function logout(){
		$this->session->sess_destroy();
		redirect();
	}

	public function new_feature(){
		
		//datas -> icons,
		$icons = scandir('./assets/img/big_icon');
		unset($icons[0]);
		unset($icons[1]);

		$data['icons'] = $icons;

		$query = $this->db->get('feature_group');

		$data['feature_group'] = $query->result_array();

		if($this->input->post()){
			$this->db->insert('features', $this->input->post());
			toshout(array('The feature '.$this->input->post('title').' has been creted.'=>'success'));

			redirect('sacl/all_feature');
		}

		$this->load->view('v_sacl_new_feature', $data);

	}

	public function feature_group(){

		$form['display'] = array('type'=>'text', 'display'=>'Display Name:','rules'=>'required');
		$form['description'] = array('type'=>'textarea', 'display'=>'Description:','rules'=>'required');

		$data['inputs'] = $form;
		
		if(rbt_valid_post($form)){
			if(!$this->uri->segment(3)){
				$this->db->insert('feature_group',$this->input->post());
				toshout('Success insert the feature group into DB','success');
			}
			else{
				$this->db->where('id',$this->uri->segment(3));
				$this->db->update('feature_group', $this->input->post());
				toshout('Success update the feature group','success');
			}
			//dumper('yeah');
			
			$_POST = array();
		}

		$query = $this->db->get('feature_group');

		foreach($query->result_array() as $fgg){
			$feat_gr[$fgg['id']] = $fgg;
		}

		if($this->uri->segment(3)){
			$_POST = $feat_gr[$this->uri->segment(3)];
		}

		$data['feature_groups'] = $feat_gr;

		$this->load->view('v_sacl_feature_group', $data);
	}

	public function edit_feature(){
		
		//datas -> icons,
		$icons = scandir('./assets/img/big_icon');
		unset($icons[0]);
		unset($icons[1]);

		$data['icons'] = $icons;

		$query = $this->db->get('feature_group');

		$data['feature_group'] = $query->result_array();

		if($this->input->post()){
			$this->db->where(array('id'=>$this->uri->segment(3)));
			$this->db->update('features', $this->input->post());
			redirect('sacl/all_feature');
		}

		$this->db->where(array('id'=>$this->uri->segment(3)));
		$query = $this->db->get('features');

		if($query->num_rows() == 1){
			$res = $query->result_array();
			$_POST = $res[0];
		}
		$this->load->view('v_sacl_edit_feature', $data);

	}

	public function all_feature(){

		//$this->db->select('site_url, icon, title, id, access');
		$this->db->where('id != 0');
		
		$this->db->order_by('id','desc');
		//$this->db->where('access = 3');
		//$this->db->where('access = 2');
		$query = $this->db->get('features');

		$data['features'] = $query->result_array();

		$this->load->view('v_sacl_all_feature', $data);
	}


	public function add_control(){

		if($this->input->post()){
			
			$data['type_id'] = $this->input->post($this->input->post('type').'_id');
			
			$data['type'] = 'users';
			if($this->input->post('type') != 'users'){ 
				$data['type'] = 'tags';
			}

			$data['feature_id'] = $this->input->post('feature_id');

			//dumper($data);

			$this->db->insert('access', $data);

		}

		$qfeat = $this->db->get('features');
		foreach($qfeat->result_array() as $feat){
			$features[$feat['id']] = $feat;
			$title[$feat['site_url']] = $feat['title'];
		}

		$this->db->order_by('username');
		$qusers = $this->db->get('users');
		foreach($qusers->result_array() as $user){
			$users[$user['id']] = $user;
		}

		$this->db->order_by('value');
		$qtag = $this->db->get('tags');
		foreach($qtag->result_array() as $tag){
			$tags[$tag['id']] = $tag;
		}

		$qacl = $this->db->get('access');
		foreach($qacl->result_array() as $key=>$acl){
			$acls[$key] = $acl;
			if($acl['type'] == 'users') $acls[$key]['verbose'] =   'user:'.$users[$acl['type_id']]['username'];
			if($acl['type'] == 'tags'){ 

				$acls[$key]['verbose'] =   $tags[$acl['type_id']]['key'].':'.$tags[$acl['type_id']]['value'];
			}
			//if($acl['type'] == 'unit') $acls[$key]['verbose'] =   'unit:'.$units[$acl['id']]['name'];
			$acls[$key]['site_url'] = $features[$acl['feature_id']]['site_url'];
			$acls[$key]['icon'] = $features[$acl['feature_id']]['icon'];
		}


		//sort features by site url ... array multisort!
		foreach ($features as $key => $row) {
		    //$fid[$key]  = $row['id'];
		    $furl[$key] = $row['site_url'];
		}	

		array_multisort($furl, SORT_ASC, $features);

		//sort acls by verbose ... array multisort!
		foreach ($acls as $key => $row) {
		    //$fid[$key]  = $row['id'];
		    $verb[$key] = $row['site_url'];
		}	

		array_multisort($verb, SORT_ASC, $acls);

		$data['features'] = $features;
		$data['users'] = $users;

		$data['tags'] = $tags;
		$data['acls'] = $acls;
		$data['titles'] = $title;

		$this->load->view('v_sacl_add_control', $data);

	}

	function delete_acl(){
		$this->db->delete('access', array('id'=>$this->uri->segment(3)));
		redirect('sacl/add_control');
	}

	function delete_feature(){
		$this->db->delete('features', array('id'=>$this->uri->segment(3)));
		redirect('sacl/all_feature');
	}

	function new_staff(){
		$this->load->library('encrypt');

		if($this->input->post()){
			$_POST['pass'] = $this->input->post('password');
			$_POST['password'] = $this->encrypt->encode($this->input->post('password'), $this->input->post('email'));


			//dumper($this->input->post());
			$this->db->insert('users', $this->input->post());
			toshout(array('New user has successfully been registered'=>'success'));
		}


		$qunit = $this->db->get('unit');
		$data['unit'] = $qunit->result_array();

		$qposition = $this->db->get('position');
		$data['position'] = $qposition->result_array();

		$this->load->view('v_sacl_new_staff',$data);
	}

	function users(){
		$this->db->order_by('username');
		$qusers = $this->db->get('users');
		$tags = $this->m_sacl->get_tags(TRUE);
		foreach($qusers->result_array() as $user){
			$t = unserialize($user['tags']);
			foreach($t as $tt){
				$ut[$tt] = $tags[$tt]; 
				$user_by_tags[$tt][] = $user['id']; 
			}

			$user['tags'] = $ut;
			unset($user['password']);
			$users[$user['id']] = $user;

		}

		$data['users'] = $users;
		$data['tags'] = $tags;
		$data['user_by_tags'] = $user_by_tags;

		//dumper($data);

		$this->load->view('v_sacl_users', $data);
	}

	function edit_user(){
		if($this->uri->segment(3)){

			if($this->input->post()){

				$this->db->where(array('id'=>$this->uri->segment(3)));
				$query = $this->db->get('users');
				$q = $query->result_array();

				$this->load->library('form_validation');
				$this->form_validation->set_message('is_unique','The %s value has been used by other user. Please key in other value:');
				$this->form_validation->set_rules('username', 'Username', 'required');
				$this->form_validation->set_rules('tags', 'Tags', 'required');

				if($q[0]['email'] != $this->input->post('email')){
					$this->form_validation->set_rules('email', 'Email', 'required|valid_email|is_unique[users.email]');
				}else{
					$this->form_validation->set_rules('email', 'Email', 'required|valid_email');
				}

				if($this->form_validation->run()){ //run validation, if success run this!
					
					$data = $this->input->post();
					unset($data['old_password']);
					unset($data['new_password']);
					$data['tags'] = serialize($data['tags']);

					if($this->input->post('old_password') && $this->input->post('new_password')){
						$this->db->where(array('password'=>hashim($this->input->post('old_password')),'email'=>$this->input->post('email')));

						$query = $this->db->get('users');
						if($query->num_rows() == 1){
							$data['password'] = hashim($this->input->post('new_password'));
						}else{
							toshout(array('Old password is invalid.'=>'error'));
						}
					}

					$this->db->where(array('id'=>$this->uri->segment(3)));
					$this->db->update('users',$data);

					toshout(array('Success'=>'success'));
				}
				
			}

			$this->db->where(array('id'=>$this->uri->segment(3)));
			$quser = $this->db->get('users');
			$user = $quser->result_array();
			unset($user[0]['password']);
			$_POST = $user[0];
			$_POST['tags'] = unserialize($_POST['tags']);

		}else{
			toshout(array("please select user"=>"error"));
		}

		$this->load->view('v_sacl_edit_user');
	}

	function new_user(){
		
		if($this->input->post()){
			$this->load->library('form_validation');
			$this->form_validation->set_message('is_unique','The %s value has been used by other user. Please key in other value:');
			$this->form_validation->set_rules('username', 'Username', 'required');
			$this->form_validation->set_rules('tags', 'Tags', 'required');
			$this->form_validation->set_rules('password', 'Password', 'required');
			$this->form_validation->set_rules('email', 'Email', 'required|valid_email|is_unique[users.email]');

			if($this->form_validation->run()){ //run validation, if success run this!
				
				
				$data = $this->input->post();
				$data['password'] = hashim($data['password']);
				$data['tags'] = serialize($data['tags']);
				$this->db->where(array('id'=>$this->session->userdata("id")));
				$this->db->insert('users',$data);
				
				toshout(array('Success'=>'success'));
			}
		}

		$this->load->view('v_sacl_new_user');
	}

	function edit_self(){

		$this->db->where(array('id'=>$this->session->userdata('id')));
		$query = $this->db->get('users');
		$q = $query->result_array();

		if($this->input->post()){
			$this->load->library('form_validation');
			$this->form_validation->set_message('is_unique','The %s value has been used by other user. Please key in other value:');
			$this->form_validation->set_rules('username', 'Username', 'required');

			if($q[0]['email'] != $this->input->post('email')){
				$this->form_validation->set_rules('email', 'Email', 'required|valid_email|is_unique[users.email]');
			}else{
				$this->form_validation->set_rules('email', 'Email', 'required|valid_email');
			}

			if($this->form_validation->run()){ //run validation, if success run this!
				
				$data = $this->input->post();
				unset($data['old_password']);
				unset($data['new_password']);
				

				if($this->input->post('old_password') && $this->input->post('new_password')){
					$this->db->where(array('password'=>hashim($this->input->post('old_password')),'email'=>$this->input->post('email')));

					$query = $this->db->get('users');
					if($query->num_rows() == 1){
						$data['password'] = hashim($this->input->post('new_password'));
					}else{
						toshout(array('Old password is invalid.'=>'error'));
					}
				}

				$this->db->where(array('id'=>$this->session->userdata("id")));
				$this->db->update('users',$data);

				toshout(array('Success'=>'success'));
			}
		}

		$this->db->where(array('id'=>$this->session->userdata('id')));
		$query = $this->db->get('users');

		$q = $query->result_array();

		$_POST = $q[0];

		unset($_POST['tags']);
		unset($_POST['password']);

		$this->load->view('v_sacl_edit_self');
	}

	function spoof(){
		//if ada id 
		if($this->uri->segment(3)){
			$id = $this->uri->segment(3);

			//$this->session->sess_destroy();

			$key = sha1(mt_rand());

			$data = array(
						'key'=>$key,
						'username'=>$this->session->userdata('username'),
						'user_id'=>$this->session->userdata('id'),
						'spoof_id'=>$id
					);

			
			$this->db->insert('spoof_log', $data);
			$this->session->sess_destroy();
			redirect('sacl/gospoof/'.$key);

		}

		//display all user list
		$this->db->order_by('username');
		$qusers = $this->db->get('users');
		$tags = $this->m_sacl->get_tags(TRUE);
		foreach($qusers->result_array() as $user){
			$t = unserialize($user['tags']);
			foreach($t as $tt){
				$ut[$tt] = $tags[$tt]; 
				$user_by_tags[$tt][] = $user['id']; 
			}

			$user['tags'] = $ut;
			unset($user['password']);
			$users[$user['id']] = $user;

		}

		$data['users'] = $users;
		$data['tags'] = $tags;
		$data['user_by_tags'] = $user_by_tags;

		//dumper($data);

		$this->load->view('v_sacl_spoof', $data);
	}

	function gospoof(){
		//dumper('testing');
		$query = $this->db->get_where('spoof_log', array('key'=>$this->uri->segment(3)));
		
		if($query->num_rows == 1){
			$res = $query->result_array();
			if($res[0]['access'] == '1'){ //only if given access. This will disable previous key usage. So that each spoof is its own log.
				
				$this->db->select('users.id, users.username, users.fullname, users.email, tags');
				$this->db->from('users');

				$this->db->where(array('users.id'=>$res[0]['spoof_id']));
				$query = $this->db->get();

				//check if exists. If not throw error
				$ros = $query->result_array();
				
				//create tag
				$tags = unserialize($ros[0]['tags']);
				foreach($tags as $tag_id){
					$this->db->or_where(array('id'=>$tag_id));
				}

				$q = $this->db->get('tags');

				foreach($q->result_array() as $tags){
					$tagid[] = $tags['id'];
				}

				$ros[0]['tags_id'] = $tagid;

				$ros[0]['spoof'] = $this->uri->segment(3);
				$this->session->set_userdata($ros[0]);
				redirect('main/dashboard');
			}
		}
	}

	function unspoof(){

		if(!$this->session->userdata('username')){
			$this->db->select('users.id, users.username, users.fullname, users.email, tags');
			$this->db->from('users');
			$this->db->join('spoof_log', 'user_id=users.id');
			$this->db->where(array('spoof_log.key'=>$this->uri->segment(3)));
			$query = $this->db->get();

			//check if exists. If not throw error
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

			//$res[0]['tags'] = unserialize($res[0]['tags']);
			$this->session->set_userdata($res[0]); //set session for user details

			$this->db->where(array('key'=>$this->uri->segment(3)));
			$this->db->update('spoof_log', array('access'=>0));
			redirect('main/dashboard');
		}

		//destroy session
		$this->session->sess_destroy();
		redirect('sacl/unspoof/'.$this->uri->segment(3));
	}

	function tags(){

		if($this->input->post()){
			$this->db->insert('tags', $this->input->post());
			toshout(array('Tags baru sudah selamat disimpan ke dalam sistem'=>'success'));
		}

		if($this->uri->segment(3) == 'delete' AND $this->uri->segment(4)){
			$this->db->where(array('id'=>$this->uri->segment(4)));
			$this->db->delete('tags');
			toshout(array('Tag tersebut telah berjaya dipadam'=>'success'));
		}

		$data['tags'] = $this->m_sacl->get_tags();
		$this->load->view('v_sacl_tags', $data);
	}

	function edit_tag(){

		if($this->input->post()){
			//dumper($this->input->post());
			$this->db->where(array('id'=>$this->uri->segment(3)));
			$this->db->update('tags',$this->input->post());
			//dumper($this->db->last_query());
			toshout(array('Success'=>'success'));
		}

		$this->db->where(array('id'=>$this->uri->segment(3)));
		$query = $this->db->get('tags');
		$res = $query->result_array();
		
		$_POST = $res[0];
		$data['tags'] = $this->m_sacl->get_tags();
		$this->load->view('v_sacl_tags', $data);
	}

	function delete_tag(){

		$this->db->where(array('id'=>$this->uri->segment(3)));
		$this->db->delete('tags');

		toshout(array('Tag tersebut telah berjaya dipadam.'=>'success'));
		redirect('sacl/tags');
	}

	function delete(){

		$seg = $this->uri->segment_array();
		$table = $seg[3];
		$column = $seg[4];
		$key = $seg[5];

		unset($seg[1]);
		unset($seg[2]);
		unset($seg[3]);
		unset($seg[4]);
		unset($seg[5]);
		$red = implode('/',$seg);
		$this->db->where(array($column=>$key));
		$this->db->delete($table);

		toshout('The data has been deleted!','success');
		redirect($this->input->server('HTTP_REFERER'));
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */