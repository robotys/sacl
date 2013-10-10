<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Sandbox extends CI_Controller {

	public function index(){
		$class = new ReflectionClass('Sandbox');
		foreach($class->getMethods(ReflectionMethod::IS_PUBLIC) as $methods){
			if($methods->class == 'Sandbox') echo '<li><a href="'.site_url('sandbox/'.$methods->name).'">'.$methods->name.'</a></li>';
		}
	}

	public function get_sample_books(){
		$this->load->library('esentral');
		$res = $this->esentral->please('get_sample_books', array());

		if($res['status']){
			$this->db->select('book_id');
			foreach($res['content'] as $row){
				$received[$row['book_id']] = $row;
				
				$this->db->or_where('book_id', $row['book_id']);
			}

			$query = $this->db->get('books');

			foreach($query->result_array() as $from_db){
				$ada[$from_db['book_id']] = $received[$from_db['book_id']];
				unset($received[$from_db['book_id']]);
			}

			// dumper($received);
			// dumper(count($ada));
			// dumper(count($received));
			if(count($ada) > 0) $this->db->update_batch('books', $ada, 'book_id');
			if(count($received) > 0) dumper($this->db->insert_batch('books', $received));
			// dumper(count(array_diff_key($received, $ada)));
		}
	}

	public function update_books(){
		$this->load->library('esentral');
		$res = $this->esentral->please('get_updated_books', array('date'=>date('Y-m-d', strtotime('-6 month'))));


			// dumper($res);

		if($res['status']){
			$this->db->select('book_id');
			foreach($res['content'] as $row){
				$received[$row['book_id']] = $row;
				
				$this->db->or_where('book_id', $row['book_id']);
				
			}

			$query = $this->db->get('books');

			foreach($query->result_array() as $from_db){
				$ada[$from_db['book_id']] = $received[$from_db['book_id']];
				unset($received[$from_db['book_id']]);
			}

			// dumper($received);

			if(count($ada) > 0) $this->db->update_batch('books', $ada, 'book_id');
			if(count($received) > 0) dumper($this->db->insert_batch('books', $received));
			// dumper(count(array_diff_key($received, $ada)));
		}
	}

	public function set_sample_books(){
		if($this->uri->segment(3)){
			// $this->load->model('m_books');
			// $total = $this->m_books->set_sample_books($order = 'random', $titles = 20, $unit = 1, $lib_id = $this->uri->segment(3));
			// get all free ebooks (price 0)
			$this->db->where('price',0);
			$query = $this->db->get('books');
			
			// $org = $this->session->userdata('org');
			// $org_id = $org['id'];
			// unset($org);
			$org_id = $this->uri->segment(3);

			$all_total = 0;
			foreach($query->result_array() as $book){
				$sample['book_id'] = (int)$book['book_id'];
				$sample['price'] = $book['price'];
				$sample['unit'] = $this->config->item('sample_unit');
				$sample['line_total'] = $book['price']*$sample['unit'];
				$all_total += $sample['line_total'];
				$samples[] = $sample;

				for($i=0; $i < $sample['unit']; $i++){
					$to_shelve['book_id'] = (int)$book['book_id']; 
					$to_shelve['organisation_id'] = (int)$org_id;
					$to_shelve['receive_date'] = date('Y-m-d');
					$to_shelve['expire_date'] = date('Y-m-d', strtotime('+99 year'));

					$to_shelves[] = $to_shelve;
				}
			}

			$book_order['rm'] = $all_total;
			$book_order['user_id'] = $this->session->userdata('id');
			$book_order['organisation_id'] = $org_id;
			$book_order['status'] = 'delivered';
			$book_order['items'] = json_encode($samples);


			dumper($this->db->insert('book_order', $book_order));
			dumper($this->db->insert_batch('shelve', $to_shelves));
			
			// dumper($book_order);
			// dumper(count($to_shelves));
			// dumper($all_total);



			// toshout(array('Done add sample books.'=>'success'));
			// redirect('main/dashboard');
		}else{
			echo 'Please choose library to be add sample books: <ul>';

			$goto = $this->uri->segment(1).'/'.$this->uri->segment(2).'/';

			$query = $this->db->get('organisation');
			foreach($query->result_array() as $row){

				echo '<li><a href="'.site_url($goto.$row['id']).'">'.$row['name'].'</a></li>';
			}
			echo '</ul>';
		}
	}

	public function esentral_api(){
		$this->load->library('esentral');

		$new = array('email'=>'yihaa@e-sentral.com',
						'username'=>'yihaaa',
						'password'=>'qwerty123',
						'fullname'=>'Yi Haa'
						);

		$check = array('email'=>'robotys@gmail.com',
						'username'=>'yunlibtest'
						);

		dumper($this->esentral->please('check_registration', $check));
	}

	public function bersih_pts(){
		$this->load->model('m_books');
		$this->db->select('shelve.id');
	    $this->db->join('books', 'shelve.book_id = books.id');
	    $this->db->not_like('books.publisher', 'PTS');
	    $this->db->where('shelve.organisation_id', 8);
	    $query = $this->db->get('shelve');

	    dumper($query->result_array());
	    dumper($this->db->last_query());
	}

	public function testMailer(){
		
		$input = array(
						// 'from'=>array('type'=>'text', 'display'=>'From', 'rules'=>'required|valid_email'),
						// 'reply-to'=>array('type'=>'text', 'display'=>'Reply-to', 'rules'=>'required|valid_email'),
						'to'=>array('type'=>'text', 'display'=>'To', 'rules'=>'required|valid_email'),
						'subject'=>array('type'=>'text', 'display'=>'Subject', 'rules'=>'required'),
						'message'=>array('type'=>'text', 'display'=>'Message', 'rules'=>'required')
					);

		if(rbt_valid_post($input)){
			//load email libraries
			$this->load->library('amazon_ses');
			// dumper($this->amazon_ses->address_is_verified('no-reply@e-sentral.com'));
			$this->amazon_ses->to($this->input->post('to'));
			$this->amazon_ses->subject($this->input->post('subject'));
			$this->amazon_ses->message($this->input->post('message'));
			$this->amazon_ses->debug(TRUE);
			dumper($this->amazon_ses->send());
			// dumper(scandir('./ses-cert'));
		}

		// dumper();
		rbt_make_form($input);
	}

	public function secret(){
		$db['username'] = $this->db->username;
		$db['password'] = $this->db->password;
		$db['database'] = $this->db->database;
		$db['hostname'] = $this->db->hostname;
		echo "DB:";
		dumper($db);
	}

}