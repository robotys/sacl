<?php
	
	function rbt_redirect_back(){
		$CI =&get_instance();
		//dumper($_SERVER['HTTP_REFERER']);
		if($CI->input->server('HTTP_REFERER')) $url = $CI->input->server('HTTP_REFERER');
		else $url = site_url('main/dashboard');

		redirect($url);
	}

	function rbt_maketable($tablename,$inputs){
		$CI=&get_instance();
		$CI->load->dbforge();

		//check if tablename can be used;
		if($CI->db->table_exists($tablename) == FALSE){

			
			foreach($inputs as $name=>$input){
				if($input['type'] == 'text' OR $input['type'] == 'select' OR $input['type'] == 'radio'){
						$fields[$name] = array('type'=>'VARCHAR','constraint'=>'255');
				}elseif($input['type'] == 'textarea'){
						$fields[$name] = array('type'=>'TEXT');
				}

			}

			$CI->dbforge->add_field('id');
			$CI->dbforge->add_field($fields);
			$CI->dbforge->create_table($tablename);

			return TRUE;

		}else{
			toshout(array('Tablename "'.$tablename.'" exists. Do use another tablename'=>'error'));
			return FALSE;
		}

	}
	
	function rbt_valid_post($inputs){
		$CI =&get_instance();
		$CI->load->library('form_validation');

		foreach($inputs as $name=>$input){
			if(array_key_exists('rules', $input)) $CI->form_validation->set_rules($name,$input['display'],$input['rules']);
		}

		if($CI->form_validation->run() != FALSE){
			return TRUE;
		}else{
			return FALSE;
		}

		//return TRUE;
	}

	function rbt_makeform($inputs){
		echo '<form method="post">';
		echo validation_errors('<div class="alert alert-error">','</div>');
		$CI=&get_instance();
		$data = $CI->input->post();
		foreach($inputs as $name=>$input){
			switch($input['type']){
				case 'text':
					echo '<p>'.$input['display'].'<br/>';
					echo '<input type="text" name="'.$name.'" value="'.set_value($name,$data[$name]).'"/></p>';
					break;
				case 'textarea':
					echo '<p>'.$input['display'].'<br/>';
					echo '<textarea name="'.$name.'">'.set_value($name, $data[$name]).'</textarea></p>';
					break;
			}


		}

		echo '<input type="submit" class="btn btn-primary btn-large">';
		echo '</form>';
	}
	
	function curl_post($target, $data){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $target);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, true);

		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		$output = curl_exec($ch);
		$info = curl_getinfo($ch);
		curl_close($ch);

		return $output;
	}

	function roar(){
		echo 'FUS DO RAAAAHHHH!';
	}

	function dumper($multi){
		echo '<pre>';
		var_dump($multi);
		echo '</pre>';
	}

	function show_sess(){
		$CI=&get_instance();
		dumper($CI->session->all_userdata());
	}
	
	function dacl(){
		$CI=&get_instance();
		dumper($CI->session->userdata('debug_acl'));
	}

	function toshout($mixed,$type=NULL){
		$CI=&get_instance();

		if(!is_string($mixed)){
			if($CI->session->userdata('toshout') != FALSE){
				$current = $CI->session->userdata('toshout');
				$new = array_merge($current,$mixed);
			}else{
				$new = $mixed;
			}
		}else{
			if(!isset($type)) $type = 'notice';
			$new[$mixed] = $type;

			//dumper($new);
		}

		$CI->session->set_userdata('toshout', $new);

	}

	function shout(){
		$CI =&get_instance();
		if($CI->session->userdata('toshout') != FALSE){
			echo '<p>';
			foreach($CI->session->userdata('toshout') as $msg=>$class){
				echo '<div class="alert alert-'.$class.'">';
				echo $msg;
				echo '</div>';
			}
			echo '</p>';

			$CI->session->set_userdata('toshout', array());
		}else{
			//roar();
		}
	}

	function hashim($txt){
		//shadow will map the txt
		$m = mapley();
		$xtx = array();
		foreach(str_split($txt) as $x){
			$xtx[] = $m[$x];
		}
		//hide between trees
		$forest = '';
		foreach(str_split(sha1($txt)) as $i=>$tree){
			if(array_key_exists($i, $xtx)) $forest .= $tree.$xtx[$i];
			else $forest .= $tree;
		}

		if(strlen($txt) < 10) $forest.='0'.strlen($txt).'==';
		else $forest.=strlen($txt).'==';
		return $forest;
	}

	function robot($code){
		$m = mapley();
		$raw = str_split($code);
		$t = (int)($raw[count($raw)-4].$raw[count($raw)-3]);
		$point = '';
		for($i=1; $i <= ($t*2) ; $i+=2){
			$point .= $m[$raw[$i]];
		}

		return $point;
	}

	function mapley(){
		$array = '1qaz2wsx3edc4rfv5tgb6yhn7ujm8ik9ol0pQAZWSXEDCRFVTGBYHNUJMIKOLP';
		$arr = str_split($array);
		foreach($arr as $i=>$s){
			$inverse = $arr[(strlen($array) - ($i+1))];
			$m[$s] = $inverse; 
		}

		return $m;
	}

	function send_sms($hp, $message){
		$link = 'http://isms.com.my/isms_send.php?un=pts&pwd=password&dstno=6'.$hp.'&msg='.urlencode($message).'&type=1&sendid=12345';

		file_get_contents($link);
	}

	function test_post(){
		$CI =&get_instance();
		if($CI->input->post()) dumper($CI->input->post());
	}

	function array_escape($to_escape){
		foreach($to_escape as $key=>$value){
			$return_array[$key] = mysql_escape_string($value);
		}
		
		return $return_array;
	}



?>