<?php

	function set_redirect($flag){
		$CI =& get_instance();
		if($flag == 'to_here'){
			$CI->session->set_userdata('goto', $CI->uri->uri_string());
		}
	}

	function redirector($else){
		$CI =& get_instance();
		if($CI->session->userdata('goto')){
			$goto = $CI->session->userdata('goto');
			$CI->session->unset_userdata('goto');

			redirect($CI->session->userdata('goto'));

		}else{
			redirect($else);
		}
	}

	function get_organisation(){
		//get subdomain:
		$host = str_replace('http://', '', base_url());
		$exp = explode('.', $host);
		$sub_domain = $exp[0];

		$CI =& get_instance();
		$CI->db->where('sub_domain', $sub_domain);
		$query = $CI->db->get('organisation');

		if($query->num_rows() == 1){
			return $query->row_array();
		}else{
			return false;
		}
	}
	
	function zip($source, $destination)
	{
	    if (!extension_loaded('zip') || !file_exists($source)) {
	        return false;
	    }

	    $zip = new ZipArchive();
	    if (!$zip->open($destination, ZIPARCHIVE::CREATE)) {
	        return false;
	    }

	    $source = str_replace('\\', '/', realpath($source));

	    if (is_dir($source) === true)
	    {
	        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source), RecursiveIteratorIterator::SELF_FIRST);

	        foreach ($files as $file)
	        {
	            $file = str_replace('\\', '/', $file);

	            // Ignore "." and ".." folders
	            if( in_array(substr($file, strrpos($file, '/')+1), array('.', '..')) )
	                continue;

	            $file = realpath($file);

	            if (is_dir($file) === true)
	            {
	                $zip->addEmptyDir(str_replace($source . '/', '', $file . '/'));
	            }
	            else if (is_file($file) === true)
	            {
	                $zip->addFromString(str_replace($source . '/', '', $file), file_get_contents($file));
	            }
	        }
	    }
	    else if (is_file($source) === true)
	    {
	        $zip->addFromString(basename($source), file_get_contents($source));
	    }

	    return $zip->close();
	}
	function unzip($file_path, $to_dir){
		$zipArchive = new ZipArchive();
		$result = $zipArchive->open($file_path);
		if ($result === TRUE) {
		    $zipArchive ->extractTo($to_dir);
		    $zipArchive ->close();
		    // Do something else on success
		    return TRUE;
		} else {
		    // Do something on error
		    return FALSE;
		}
	}
	
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
				}elseif($input['type'] == 'id'){
						$fields[$name] = array('type'=>'INT', 'constraint'=>'11', 'auto_increment'=>TRUE, 'unsigned'=>TRUE);
				}elseif($input['type'] == 'integer'){
						$fields[$name] = array('type'=>'INT', 'constraint'=>'11');
				}

			}
			
			//$fields['timestamp'] = array('type'=>'TIMESTAMP','default'=>CURRENT_TIMESTAMP);

			if(array_key_exists('id', $fields) === FALSE) $CI->dbforge->add_field('id');
			$CI->dbforge->add_field($fields);
			$CI->dbforge->add_field('`changetime` TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL');
			//add timestamps
			$CI->dbforge->add_key('id');
			$CI->dbforge->create_table($tablename);

			return TRUE;

		}else{
			toshout(array('Tablename "'.$tablename.'" exists. Do use another tablename'=>'error'));
			return FALSE;
		}

	}
	
	function rbt_move_upload($inputs){
		//dumper($_FILES);
		foreach($inputs as $name=>$input){
			if($input['type'] == 'upload' && $_FILES[$name]["error"] == FALSE){
		
		    	$uploads_dir = $input['folder'];
		        $tmp_name = $_FILES[$name]["tmp_name"];
		        $name = $_FILES[$name]["name"];

		        /*
		        dumper($uploads_dir);
		        dumper($tmp_name);
		        dumper($name);
		        */
		        move_uploaded_file($tmp_name, "./$uploads_dir/$name");	
			}
		}
	}

	function rbt_valid_post($inputs){
		//dumper($inputs);
		/***********************************
		Self-Notes: 
		Next thing to do here ->
		Need to make sure if only upload form
		is there, the validation still can be
		run as usual. Right now need to make
		workaroud by adding hidden input form
		with nonsense value.
		***********************************/

		$CI =&get_instance();
		$CI->load->library('form_validation');

		//only on post
		if($CI->input->post()){

			foreach($inputs as $name=>$input){
				//prep and test for normal input fields
				if(array_key_exists('rules', $input) && $input['type']!='upload') $CI->form_validation->set_rules($name,$input['display'],$input['rules']);

				//prepare and test for uploads data from upload fields
				if($input['type'] == 'upload'){
					$uploads[$name] = $input;
				}
			}

			///Validate all form inputs data except uploads form
			if($CI->form_validation->run() != FALSE){
				$ret_form = TRUE;
			}else{
				$ret_form = FALSE;
			}


			$ret_upload = TRUE;
			//check validation for uploads
			if(count($_FILES)>0){
				foreach($uploads as $name=>$upload){
					if($uploads[$name]['size'] > 0){
						//create rules
						$rules_raw = explode('|',($upload['rules']));
						foreach ($rules_raw as $value) {
							$exp = explode(':',$value);
							if(count($exp) == 1) $to_check[$exp[0]] = TRUE;
							else{
								$upload_config[$exp[0]] = str_replace(',', '|', $exp[1]);
							}
						}
						//dumper($upload_config);
						$CI->load->library('upload', $upload_config);
						if(!$CI->upload->do_upload($name)){ // if error
							$upload_error = $CI->upload->display_errors();
							$ret_upload = FALSE;
							toshout(array($upload_error =>'error'));
						}else{//if success
							//set $_POST to filename
							$data = $CI->upload->data();
							$_POST[$name] = $data['file_name'];
						}
					}
				}
			}
			
			if(count($_FILES)>0) $ret = ($ret_form AND $ret_upload);
			else $ret = $ret_form;
			return $ret;
		}
	}


	////support for older SACL rbt_makeform
	function rbt_makeform($inputs, $default = array(), $clear_form = false){
		//rbt_open_form();
		rbt_make_form($inputs, $default,$clear_form);
		//rbt_close_form();
	}


	function rbt_make_form($inputs, $default = array(), $clear_form = false){
		rbt_open_form();
		rbt_make_inputs($inputs, $default,$clear_form);
		rbt_close_form();
	}

	function rbt_make_inputs($inputs, $default = array(), $clear_form = false){
		shout_dev();
		echo validation_errors('<div class="alert alert-error">','</div>');
		//$CI=&get_instance();
		//$data = $CI->input->post();
		$datepicker = FALSE;
		foreach($inputs as $name=>$input){

			$default_value = '';
			if(array_key_exists($name, $default)) $default_value = $default[$name];

			if($input['type'] == 'text'){
				echo '<p>'.$input['display'].'<br/>';
				if(!$clear_form) echo '<input type="text" name="'.$name.'" value="'.set_value($name, $default_value).'" id="'.$input['id'].'" class="'.$input['class'].'"/></p>';
				if($clear_form) echo '<input type="text" name="'.$name.'" value="" id="'.$input['id'].'" class="'.$input['class'].'"/></p>';
			}
			elseif($input['type'] == 'datetime' OR $input['type'] == 'date'){
					echo '<p>'.$input['display'].'<br/>';
					if(!$clear_form) echo '<input type="text" name="'.$name.'" value="'.set_value($name, $default_value).'" id="'.$input['id'].'" class="'.$input['class'].' datepicker"/></p>';
					if($clear_form) echo '<input type="text" name="'.$name.'" value="" id="'.$input['id'].'" class="'.$input['class'].' datepicker"/></p>';
			}
			elseif($input['type'] == 'password'){
					echo '<p>'.$input['display'].'<br/>';
					if(!$clear_form) echo '<input type="password" name="'.$name.'" value="'.set_value($name, $default_value).'" id="'.$input['id'].'" class="'.$input['class'].'"/></p>';
					if($clear_form) echo '<input type="password" name="'.$name.'" value="" id="'.$input['id'].'" class="'.$input['class'].'"/></p>';
			}
			elseif($input['type'] == 'textarea'){
					echo '<p>'.$input['display'].'<br/>';
					if(!$clear_form) echo '<textarea name="'.$name.'" id="'.$input['id'].'" class="'.$input['class'].'">'.set_value($name, $default_value).'</textarea></p>';
					if($clear_form) echo '<textarea name="'.$name.'" id="'.$input['id'].'" class="'.$input['class'].'"></textarea></p>';
			}
			elseif($input['type'] == 'upload'){
					echo '<p>'.$input['display'].'<br/>';
					if(!$clear_form){
						//get file path
						$exp = explode('|', $input['rules']);
						foreach($exp as $ex){
							if(strpos($ex, 'path') !== FALSE){
								$path = str_replace('upload_path:./', '', $ex);
								$path = trim($path,'/').'/'.$default_value;
								$src = base_url($path);

								if($default_value != NULL) echo '<img class="upload_default '.$name.'" src="'.$src.'"/><br/>';
							}
						}
					}
					echo '<input type="file" name="'.$name.'" id="'.$input['id'].'" class="'.$input['class'].'"/></p>';
			}
			elseif($input['type'] == 'hidden'){
					//echo '<p>'.$input['display'].'<br/>';
					echo '<input type="hidden" name="'.$name.'" value="'.$input['value'].'" id="'.$input['id'].'" class="'.$input['class'].'"/></p>';
			}
			elseif($input['type'] == 'radio'){
					echo '<p>'.$input['display'].': ';

					foreach($input['options'] as $disp=>$value){
						echo '<input type="radio" name="'.$name.'" value="'.$value.'" id="'.$input['id'].'" class="'.$input['class'].'" '.set_radio($name, $value).'> '.ucfirst($disp).' &nbsp;';
					}
					echo '</p>';
					//echo '<input type="hidden" name="'.$name.'" value="'.$input['value'].'"/></p>';
			}
			elseif($input['type'] == 'select'){
					echo '<p>'.$input['display'].': ';
					echo '<select name="'.$name.'" id="'.$input['id'].'" class="'.$input['class'].'">';
					foreach($input['options'] as $disp=>$value){
						echo '<option value="'.$value.'"';
						if(!$clear_form && $value == $default_value) echo 'selected="selected"'; 
						echo '> '.ucfirst($disp).'</option>';
					}
					echo '</select></p>';
					//echo '<input type="hidden" name="'.$name.'" value="'.$input['value'].'"/></p>';
			}

			if($input['id'] == 'datepicker' && $datepicker == FALSE) $datepicker = TRUE;

		}


		if($datepicker){
			echo '<style type="text/css">@import url('.base_url('assets/css/smoothness/jquery-ui-1.8.10.custom.css').')</style>';
			echo '<script src="'.base_url('assets/js/jquery-ui-1.8.10.custom.min.js').'"></script>';
			echo '<script type="text/javascript">$("#datepicker").datepicker()</script>';
			//echo 'DatePicker On!';
		}else{
			//echo 'WTF!!';
		}
	}

	function rbt_open_form($id = "", $class=""){
		echo '<form method="post" enctype="multipart/form-data" id="'.$id.'" class="'.$class.'">';
	}

	function rbt_close_form($no_button=FALSE, $button_value = "submit"){
		echo '<input type="submit" class="btn btn-primary btn-large" value="'.$button_value.'">';
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

	function curl_get($target){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $target);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		//curl_setopt($ch, CURLOPT_POST, true);

		//curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		$output = curl_exec($ch);
		$info = curl_getinfo($ch);
		curl_close($ch);

		return $output;
	}

	function curl_get_https($url){
		$ch = curl_init();
	    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	    curl_setopt($ch, CURLOPT_HEADER, false);
	    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	    curl_setopt($ch, CURLOPT_URL, $url);
	    //curl_setopt($ch, CURLOPT_REFERER, $url);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	    $result = curl_exec($ch);
	    curl_close($ch);
	    return $result;
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

	function toshout_dev($array){
		if(gettype($array) == 'string') $array = array($array=>'success');

		$CI=&get_instance();
		if($CI->config->item('toshout') != FALSE){
			$current = $CI->config->item('toshout');
			$new = array_merge($current,$array);
		}else{
			$new = $array;
		}

		$CI->config->set_item('toshout', $new);

	}

	function shout_dev(){
		$CI =&get_instance();
		$message = $CI->config->item('toshout');
		$CI->config->set_item('toshout', array());

		if($message != FALSE){
			echo '<p>';
			foreach($message as $msg=>$class){
				echo '<div class="alert alert-'.$class.'">';
				echo $msg;
				echo '</div>';
			}
			echo '</p>';

			
		}else{
			//roar();
		}
	}

	function toshout($array){
		if(gettype($array) == 'string') $array = array($array=>'success');

		$CI=&get_instance();
		if($CI->session->userdata('toshout') != FALSE){
			$current = $CI->session->userdata('toshout');
			$new = array_merge($current,$array);
		}else{
			$new = $array;
		}

		$CI->session->set_userdata('toshout', $new);

	}

	function shout(){
		$CI =&get_instance();
		$message = $CI->session->userdata('toshout');
		$CI->session->set_userdata('toshout', array());

		if($message != FALSE){
			echo '<p>';
			foreach($message as $msg=>$class){
				echo '<div class="alert alert-'.$class.'">';
				echo $msg;
				echo '</div>';
			}
			echo '</p>';

			
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
