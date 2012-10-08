<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Main extends CI_Controller {

	function index(){
		redirect('main/dashboard');
	}

	function dashboard(){

		foreach($this->session->userdata('dashboard') as $id=>$site_url){
			$this->db->or_where(array('id'=>$id));
		}

		$query = $this->db->get('features');
		$data['dashboard'] = $query->result_array();
		//dumper($this->db->last_query());

		/*
		$dash = array(
						'id'=>0,
						'site_url'=>'https://sites.google.com/a/pts.com.my/general-information/',
						'title'=>'Link HRDA',
						'access'=>'3',
						'icon'=>'diary.png',
						'dashboard'=>'1',
						'description'=>'Link ke laman web HRDA',
						'status'=>'on',
					);
		$data['dashboard'][] = $dash;
		*/
		//sent the features details
		$this->load->view('v_main_dashboard', $data);
		
	}

	function error_404(){
		$this->load->view('v_main_404');
	}

}