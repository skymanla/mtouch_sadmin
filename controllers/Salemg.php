<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Salemg extends MT_Controller {

	public $pagedir = "salemg";
	//public $method = $_SERVER['REQUEST_METHOD'];

	/**
	 * 헬퍼를 로딩합니다
	 */
	protected $helpers = array('form', 'array');

	function __construct()
	{
		parent::__construct();

		/**
		 * 라이브러리를 로딩합니다
		 */
		$this->load->library(array('session','loginchk','apibycurl','pagination'));
		$this->loginchk->thischk($this->session->userdata());
		$this->load->model('sales_model');
	}
	public function index()
	{
		echo "<script>alert('잘못된 접근입니다.');location.replace('/');</script>";
	}
	public function done()
	{	
		$method = $_SERVER['REQUEST_METHOD'];
		$store_id = $this->session->userdata("S_ID");
		$skinbody = "done";

		//업체명
		$data['company_list'] = $this->sales_model->get_company_list();

		switch ($method) {
			case 'GET':
				$search_data = $_GET;
				if(empty($search_data)){
					//pass
				}else{
					if(count(explode($_GET['search_start_dt'],"-"))==2){
						$fdate = strtotime($_GET['search_start_dt']."-01");
						$ldate = strtotime($_GET['search_start_dt']."-01");
						$_GET['search_start_dt'] = date('Y-m-d', strtotime('first day of', $fdate));
						$_GET['search_end_dt'] = date('Y-m-d', strtotime('last day of', $ldate));
					}
					$getdata = (object)[
									'limit' => $this->input->get('limit', true, 30),
									'page' => $this->input->get('per_page', true, 1),
									'search_start_dt' => $this->input->get('search_start_dt', true, date('YYYY-MM-DD')),
									'search_end_dt' => $this->input->get('search_end_dt', true, date('YYYY-MM-DD')),
									'sale_all' => $this->input->get('sale_all', true),
									'sale_normal' => $this->input->get('sale_normal', true),
									'sale_cancel' => $this->input->get('sale_cancel', true),
									'sale_keyword' => $this->input->get('sale_keyword', true),
									'cupon_all' => $this->input->get('cupon_all', true),
									'cupon_no' => $this->input->get('cupon_no', true),
									'cupon_comm' => $this->input->get('cupon_comm', true),
									'cupon_manage' => $this->input->get('cupon_manage', true),
									'search_keyword' => $this->input->get('search_keyword', true)
								];

					$data['p'] = $getdata;
					$data['totalcount'] = $this->sales_model->get_sales_info($store_id,$search_data,'count');
					if(empty($getdata->limit)) $getdata->limit = 30;
					$data['paging'] = get_paging($getdata->page, $data['totalcount'], $getdata->limit);

					$config['base_url'] = '/'.$this->pagedir."/".$skinbody;
					if($getdata->limit != ''){
						$config['per_page'] = $getdata->limit;
					}else{
						$config['per_page'] = 30;
					}
					
					$config['uri_segment'] = 5;
					$config['total_rows'] = $data['totalcount'];
					$config['page_query_string'] = true;
					$config['reuse_query_string'] = true;

					$this->pagination->initialize($config);
					$data['pagination'] = $this->pagination->create_links();
					//$page = $this->uri->segment($getdata->page,1);
					if($getdata->page != ''){
						$page = $getdata->page;
					}else{
						$page = $this->uri->segment(30,1);
					}
					if($page > 1){
						$start = (($page / $config['per_page'])) * $config['per_page'];
					}else{
						$start = ($page - 1) * $config['per_page'];
					}

					$plimit = $config['per_page'];
					$data['search_data'] = $this->sales_model->get_sales_info($store_id,$search_data,'',$start,$plimit);
				}
				break;
			case 'POST':
				//All
				//default
				$search_data = $_POST;
				$search_data = $this->sales_model->get_sales_info($store_id,$search_data);
				break;
		}

		$this->load->view('_layout/header.php');
		$this->load->view('_layout/navigation.php');
		$this->load->view('/'.$this->pagedir.'/'.$skinbody.".php", $data);


		$this->load->view('_layout/footer.php');
	}

	public function export_sale_all(){
		$store_id = $this->session->userdata("S_ID");
		$search_data = $_GET;
		$skinbody = "export_sale_all";
		$getdata = (object)[
						'search_start_dt' => $this->input->get('search_start_dt', true, date('YYYY-MM')),
						'search_end_dt' => $this->input->get('search_end_dt', true, date('YYYY-MM')),
						'sale_all' => $this->input->get('sale_all', true),
						'sale_normal' => $this->input->get('sale_normal', true),
						'sale_cancel' => $this->input->get('sale_cancel', true),
						'sale_keyword' => $this->input->get('sale_keyword', true),
						'cupon_all' => $this->input->get('cupon_all', true),
						'cupon_no' => $this->input->get('cupon_no', true),
						'cupon_comm' => $this->input->get('cupon_comm', true),
						'cupon_manage' => $this->input->get('cupon_manage', true),
						'cupon_keyword' => $this->input->get('cupon_keyword', true)
					];

		$data['p'] = $getdata;
		$data['totalcount'] = $this->sales_model->get_sales_info($store_id,$search_data,'count');
		
		$search_data = $this->sales_model->get_sales_info($store_id,$search_data,'excel');
		$data['search_data'] = $search_data;
		header( "Content-type: application/vnd.ms-excel" );
		header( "Content-Disposition: attachment; filename=판매정산.xls" );
		header( "Content-Description: PHP5 Generated Data" );
		print("<meta http-equiv=\"Content-Type\" content=\"application/vnd.ms-excel;charset=utf-8\">");
		$this->load->view('/'.$this->pagedir.'/'.$skinbody.".php", $data);		
	}

	public function comm()
	{	
		$method = $_SERVER['REQUEST_METHOD'];
		$store_id = $this->session->userdata("S_ID");
		$skinbody = "comm";
		//업체명
		$data['company_list'] = $this->sales_model->get_company_list();
		switch ($method) {
			case 'GET':
				$search_data = $_GET;
				if(empty($search_data)){
					//pass
				}else{
					$getdata = (object)[
									'limit' => $this->input->get('limit', true, 30),
									'page' => $this->input->get('per_page', true, 1),
									'search_start_dt' => $this->input->get('search_start_dt', true, date('YYYY-MM')),
									'search_end_dt' => $this->input->get('search_end_dt', true, date('YYYY-MM')),
									'sale_keyword' => $this->input->get('sale_keyword', true)
								];
					$data['p'] = $getdata;
					
					$search_data = $this->sales_model->get_sales_comm_info($store_id,$search_data,'',$start,$plimit);
					$data['search_data'] = $search_data;
				}
				break;
			case 'POST':
				//All
				//default
				break;
		}
		
		$this->load->view('_layout/header.php');
		$this->load->view('_layout/navigation.php');
		$this->load->view('/'.$this->pagedir.'/'.$skinbody.".php", $data);


		$this->load->view('_layout/footer.php');
	}

	public function export_comm_all(){
		$store_id = $this->session->userdata("S_ID");
		$search_data = $_GET;
		$skinbody = "export_comm_all";
		$getdata = (object)[
						'search_start_dt' => $this->input->get('search_start_dt', true, date('YYYY-MM')),
						'search_end_dt' => $this->input->get('search_end_dt', true, date('YYYY-MM')),
						'sale_keyword' => $this->input->get('sale_keyword', true)
					];

		$data['p'] = $getdata;
			
		$search_data = $this->sales_model->get_sales_comm_info($store_id,$search_data,'excel');
		$data['search_data'] = $search_data;
		header( "Content-type: application/vnd.ms-excel" );
		if($getdata->sale_keyword != ''){
			$store_info = $this->sales_model->get_company_name($data['p']->sale_keyword);
			header( "Content-Disposition: attachment; filename=".$store_info['0']->store_name."이용내역.xls" );
		}else{
			header( "Content-Disposition: attachment; filename=업체별 이용내역.xls" );
		}
		header( "Content-Description: PHP5 Generated Data" );
		print("<meta http-equiv=\"Content-Type\" content=\"application/vnd.ms-excel;charset=utf-8\">");
		$this->load->view('/'.$this->pagedir.'/'.$skinbody.".php", $data);		
	}

	public function membership()
	{	
		$method = $_SERVER['REQUEST_METHOD'];
		$store_id = $this->session->userdata("S_ID");
		$skinbody = "membership";
		switch ($method) {
			case 'GET':
				$search_data = $_GET;
				if(empty($search_data)){
					//pass
				}else{
					$getdata = (object)[
									'limit' => $this->input->get('limit', true, 30),
									'page' => $this->input->get('per_page', true, 1),
									'search_start_dt' => $this->input->get('search_start_dt', true, date('YYYY-MM')),
									'search_end_dt' => $this->input->get('search_end_dt', true, date('YYYY-MM')),
									'mem_keyword' => $this->input->get('mem_keyword', true)
								];

					$data['p'] = $getdata;
					$data['totalcount'] = $this->sales_model->get_members_info($store_id,$search_data,'count');
					if(empty($getdata->limit)) $getdata->limit = 30;
					$data['paging'] = get_paging($getdata->page, $data['totalcount'], $getdata->limit);

					$config['base_url'] = '/'.$this->pagedir."/".$skinbody;
					if($getdata->limit != ''){
						$config['per_page'] = $getdata->limit;
					}else{
						$config['per_page'] = 30;
					}
					
					$config['uri_segment'] = 5;
					$config['total_rows'] = $data['totalcount'];
					$config['page_query_string'] = true;
					$config['reuse_query_string'] = true;

					$this->pagination->initialize($config);
					$data['pagination'] = $this->pagination->create_links();
					//$page = $this->uri->segment($getdata->page,1);
					if($getdata->page != ''){
						$page = $getdata->page;
					}else{
						$page = $this->uri->segment(30,1);
					}
					if($page > 1){
						$start = (($page / $config['per_page'])) * $config['per_page'];
					}else{
						$start = ($page - 1) * $config['per_page'];
					}
					
					$plimit = $config['per_page'];
					$search_data = $this->sales_model->get_members_info($store_id,$search_data,'',$start,$plimit);

					$data['search_data'] = $search_data;
				}
				break;
			case 'POST':
				//All
				//default
				break;
		}

		$this->load->view('_layout/header.php');
		$this->load->view('_layout/navigation.php');
		$this->load->view('/'.$this->pagedir.'/'.$skinbody.".php", $data);


		$this->load->view('_layout/footer.php');
	}

	public function export_membership()
	{	
		$method = $_SERVER['REQUEST_METHOD'];
		$store_id = $this->session->userdata("S_ID");
		$skinbody = "export_membership";
		switch ($method) {
			case 'GET':
				$search_data = $_GET;
				if(empty($search_data)){
					//pass
				}else{
					$search_data = $this->sales_model->get_members_info($store_id,$search_data,'excel','','');

					$data['search_data'] = $search_data;

					header( "Content-type: application/vnd.ms-excel" );
					header( "Content-Disposition: attachment; filename=회원별 이용내역.xls" );
					header( "Content-Description: PHP5 Generated Data" );
					print("<meta http-equiv=\"Content-Type\" content=\"application/vnd.ms-excel;charset=utf-8\">");
				}
				break;
			case 'POST':
				//All
				//default
				break;
		}
		$this->load->view('/'.$this->pagedir.'/'.$skinbody.".php", $data);
	}
}
