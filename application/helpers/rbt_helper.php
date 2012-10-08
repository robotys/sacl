<?php
	
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

	function toshout($array){
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