<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Sandbox extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -  
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in 
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see http://codeigniter.com/user_guide/general/urls.html
	 */

	public function index(){
		echo '<html><body><h2>Sandbox links:</h2>';

		echo '<ul>';
		echo '<li><a href="'.site_url('sandbox/akademia').'">Test download ebook akademia &raquo;</a></li>';
		echo '<li><a href="'.site_url('sandbox/bc').'">Online Bookcafe Sales &raquo;</a></li>';
		echo '</ul>';

		echo '</body></html>';

		
	}

	public function tac_int(){
		redirect('http://isms.com.my/isms_send.php?un=pts&pwd=password&dstno=6596465848&msg='.urlencode('Cubaan SMS Antarabangsa').'&type=1&sendid=12345');		
	}

	public function bc(){

		//prepare row of data
		////data that we have
		$to = date("Y-m-d");
		$from = date("Y-m", strtotime("-5 month"))."-01";
		$json = json_decode(file_get_contents('http://api.pts.com.my/index.php/bcafe/book_orders/pps/'.$from.'/'.$to), TRUE);


		////clean up the column of months
		for($i=5; $i>=0;$i--){
			$month[] = date('Y-m', strtotime('-'.$i.' month'));
		}

		if($this->uri->segment(3) == ''){
			$monthly[] = '';
			$monthly_total[] = 'total';
			foreach($json as $data){
				//make up for book monthly
				$exp = explode('-',$data['order_date']);
				$mon = $exp[0].'-'.$exp[1];
				if(array_key_exists($data['product_code'], $monthly) == FALSE){
					$monthly[$data['product_code']]['title'] = $data['product'];
					$monthly[$data['product_code']]['isbn'] = $data['product_code'];
					$monthly[$data['product_code']]['srp'] = $data['price'];
				}
				if(array_key_exists($mon, $monthly[$data['product_code']]) == FALSE){
					$monthly[$data['product_code']][$mon]['unit'] = $data['amount'];
					$monthly[$data['product_code']][$mon]['rm'] = $data['amount']*$data['price'];
				}else{
					$monthly[$data['product_code']][$mon]['unit'] += $data['amount'];
					$monthly[$data['product_code']][$mon]['rm'] += $data['amount']*$data['price'];
				}

				if(array_key_exists($mon, $monthly_total) == false){
					$monthly_total[$mon]['unit'] = $data['amount'];
					$monthly_total[$mon]['rm'] = $data['amount']*$data['price'];
				}else{
					$monthly_total[$mon]['unit'] += $data['amount'];
					$monthly_total[$mon]['rm'] += $data['amount']*$data['price'];
				}

			}
			unset($monthly[0]);

			//dumper($monthly);


			foreach($monthly as $isbn=>$cols){
				$row = Array();
				$row[] = '<b>'.$cols['title'].'</b><br/><small>'.$isbn.' / RM '.$cols['srp'].'</small>';
				foreach($month as $mon){
					//if(array_key_exists($mon, $cols) == false) $row[] = array('unit'=>0, 'rm'=>0);
					//else $row[] = array('unit'=>$cols[$mon]['unit'],'rm'=>$cols[$mon]['rm']);
					if(array_key_exists($mon, $cols) == false) $row[] = 0;
					else $row[] = $cols[$mon]['unit'];
				}

				$rows[] = $row;
				unset($monthly[$isbn]);
			}

			//dumper($rows);

		}

		$data['rows'] = $rows;
		$data['month'] = $month;
		$data['total'] = $monthly_total;

		$this->load->view('v_report_on9_bc', $data);
	}

	public function akademia()
	{
		
		//$this->load->view('v_kedai_main');
		$str = json_decode(file_get_contents('http://api.pts.com.my/index.php/portal/books/ebooks/'), TRUE);

		echo '<html><body><h2>Cuba Download Ebook untuk Akademia</h2><ul>';
		// http://pts.com.my/images/uploads/books/
		foreach($str as $book){
			$book['pdf'] = trim($book['pdf']);
			$book['cover'] = $book['cover'];

			echo '<li><a href="'.site_url('sandbox/download/'.$book['entry_id']).'">'.$book['title'].' &raquo;</a></li>';


			$b[] = $book;
		}
		//dumper($b);

		echo '</ul></body></html>';
	}

	function download(){

		$str = json_decode(file_get_contents('http://api.pts.com.my/index.php/portal/books/ebooks/'.$this->uri->segment(3)));
			
		$book['book_title'] = $str[0]->title;
		$book['price'] = $str[0]->price;
		$book['isbn'] = $str[0]->isbn13;
		if($book['isbn'] == '') $book['isbn'] = '-';
		$book['cover'] = $str[0]->cover;
		$book['id'] = $this->uri->segment(3);
		$book['cover_size'] = '200';
		$book['pdf_size'] = '200000';
		$book['pdf'] = $str[0]->pdf;
		$data['book'] = $book;

		//download lah!
		$this->load->library('pdfest');
		
		//dumper('../ppsv2/uploads/'.trim($book['pdf']));
		//dumper(strlen(file_get_contents('../ppsv2/uploads/Fonetik_Fonologi.pdf')));

		$this->pdfest->set_title($book['book_title']);
		$this->pdfest->set_pdf_path('../ppsv2/uploads/'.trim($book['pdf']));
		$this->pdfest->set_cover_path('../images/uploads/books/'.$book['cover']);
		$this->pdfest->set_user_email('test');
		$this->pdfest->set_user_hp('0166666666');
		//$this->pdfest->set_user_pass('0166666666');
		$this->pdfest->set_owner_pass(md5(rand()));

		$this->pdfest->force_download();
	}

	function repass(){
		$query = $this->db->get('users');
		foreach($query->result_array() as $row){
			
			$data[] = array(
						'id'=>$row['id'],
						'password'=> hashim($row['pass'])
					);
		}

		//$this->db->update_batch('users', $data, 'id');
	}

	function tagging(){
		$query = $this->db->get('tags');
		foreach($query->result_array() as $row){
			$keystone[$row['key']][$row['id_lama']] = $row['id'];
		}

		$query = $this->db->get('users');
		foreach($query->result_array() as $row){
			$data[] = array(
						'id'=>$row['id'],
						'tags'=>serialize(array($keystone['position'][$row['position']],$keystone['unit'][$row['unit']]))
					);
		}

		//dumper($data);
		//$this->db->update_batch('users', $data, 'id');

	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */