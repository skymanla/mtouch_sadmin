<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sales_model extends CI_Model{
	function __construct(){
		parent::__construct();
	}
	
	function get_sales_info($store_id,$_data,$type='',$offset='',$plimit=''){//판매내역
		extract($_data);
		if($sale_normal == "1" && $sale_cancel == "1") $sale_all = "1";
		if($cupon_no == "1" && $cupon_comm == "1" && $cupon_manage) $cupon_all = "1";

		$where_order_dt = " and a.order_dt between '".$search_start_dt."' and '".$search_end_dt."'";
		$where_order = "";
		if($sale_all == "1" && $cupon_all == "1"){
			
		}else{
			//둘다 true가 아닌 경우
			//sale
			if($sale_normal == true && $sale_cancel == false){
				$where_order .= " and a.pay_result='1' ";
			}else if($sale_normal == false && $sale_cancel == true){
				$where_order .= " and a.pay_result <> '1' ";
			}
			//coupon
			//쿠폰 미사용은 따로
			if($cupon_no==true){
				$where_order .= " and a.coupon_id='0' ";
			}else if($cupon_no==false){
				$where_order .= " and a.coupon_id <> '0' ";
			}
			//쿠폰 종류에 따라
			if($cupon_comm == true && $cupon_manage == true){
				$where_order .= " and (c.issuer='A' or c.issuer='S') ";
			}else if($cupon_comm == true && $cupon_manage == false){
				$where_order .= " and c.issuer='A' ";
			}else if($cupon_comm == false && $cupon_manage == true){
				$where_order .= " and c.issuer='S' ";
			}else if($cupon_comm == false && $cupon_manage == false){
				//pass
			}
		}
		//업체명 및 검색어 입력
		if($sale_keyword != '') $sale_search = " and a.store_id='".$sale_keyword."'";
		if($search_keyword != '') $search_data = " and b.user_name='".$search_keyword."'";
		
		if($plimit != '' || $offset != ''){
			$limit_query = ' limit '.$offset*$plimit.', '.$plimit;
		}
		if($type=="excel") $limit_query ="";
		$sql = "select a.*, b.ID, b.user_login, b.user_name, c.coupon_name, c.coupon_dc_type, c.coupon_dc_amt, c.coupon_dc_rate, c.issuer, d.mtouch_code
				from 
				tbl_order as a 
				left join 
				tbl_user as b on a.user_id=b.ID
				left join
				tbl_store_coupon as c on a.coupon_id=c.idx
				left join
				tbl_store_users as d on a.store_id = d.ID
				where a.idx <> '' ".$sale_search.$search_data." ".$where_order." "." ".$where_order_dt." order by a.order_time desc ".$limit_query;
		$query = $this->db->query($sql);
		if($type=="count"){
			$result = $query->num_rows();
		}else{
			$result = $query->result();
		}
		return $result;
	}

	function get_company_list($type=''){//검색 : 업체
		$sql = "select store_id, store_name from tbl_order group by store_name order by if(ascii(substring(store_name,1)) < 128, 9, 1) asc, store_name asc";
		$result = $this->db->query($sql)->result();

		return $result;
	}

	function get_company_name($store_id){//업체별 이용내역 -> 업체 검색한 후 엑셀 다운로드 할 때
		$sql = "select store_id, store_name from tbl_order where store_id='".$store_id."'";
		$result = $this->db->query($sql)->result();
		return $result;
	}
	function get_sales_comm_info($store_id,$_data,$type='',$offset='',$limit=''){//업체별 이용내역
		extract($_data);
		$totalcount = 0;
		$fdate = strtotime($search_start_dt."-01");
		$ldate = strtotime($search_end_dt."-01");
		$csdate = date('Y-m-d', strtotime('first day of', $fdate));
		$cedate = date('Y-m-d', strtotime('last day of', $ldate));
		$where = " where order_dt between '".$csdate."' and  '".$cedate."'";
		
		//데이터가 있는 달 group으로 묶어서 뽑아내기
		$date_query = "select date_format(order_dt, '%Y-%m') as sort_date from tbl_order where order_dt between '".$csdate."' and '".$cedate."' group by date_format(order_dt, '%Y-%m') order by order_dt desc";
		
		$date_result = $this->db->query($date_query)->result();
		//sale_keyword 가 있는 경우
		if($sale_keyword != ''){
			$where_keyword_company = " and ptable.store_id='".$sale_keyword."'";
			$where_keyword_avg = " and a.store_id='".$sale_keyword."'";
		}
		//date_query의 결과로 object list 만들기
		for($i=0;$i<count($date_result);$i++){
			$result[$i]->list->sort_date = $date_result[$i]->sort_date;
			$company_sql = "select * from
						(select a.store_id as store_id, date_format(a.order_dt, '%Y-%m') as sort_date, a.store_name as store_name, count(*) as cnt, sum(a.total_amt) as totamt, sum(b.coupon_dc_amt) as coupon_dc_amt
						from tbl_order a
						left join tbl_store_coupon b on
						a.coupon_id = b.idx
						where a.order_dt between '".$csdate."' and '".$cedate."'
						group by date_format(a.order_dt, '%Y-%m'), a.store_name order by if(ascii(substring(a.store_name,1)) < 128, 9, 1) asc, a.store_name asc) as ptable
						where ptable.sort_date='".$result[$i]->list->sort_date."'".$where_keyword_company."
						order by ptable.sort_date desc
						";
			$company_query = $this->db->query($company_sql);
			$result[$i]->list->company_list = $company_query->result();
			//변수 초기화
			$summery_all_store = 0;//업체 판매금액 총합
			$summery_all_cnt = 0;//업체 이용건수 총합
			$summery_all_store_coupon = 0;//업체 본사쿠폰 금액 총합
			for($j=0;$j<count($result[$i]->list->company_list);$j++){
				//업체별 정산 금액 산출
				$amt_sql = "select date_format(a.order_dt, '%Y-%m') as sort_date, a.store_id, a.total_amt, a.coupon_id, b.coupon_name, b.coupon_dc_type, b.coupon_dc_amt, b.coupon_dc_rate, b.issuer
					from 
					tbl_order as a 
					left join
					tbl_store_coupon as b on a.coupon_id=b.idx
					where date_format(a.order_dt, '%Y-%m')='".$date_result[$i]->sort_date."' and a.store_id='".$result[$i]->list->company_list[$j]->store_id."'";
				$amt_result = $this->db->query($amt_sql)->result();
				$amt_count = $this->db->query($amt_sql)->num_rows();
				$total_val = 0;//업체별 각 월 판매금액 합
				$issuer_a_coupon_sum = 0;//업체별 각 월 본사쿠폰 사용 금액 합
				for($k=0;$k<$amt_count;$k++){
					$amt_non_isser = 0;//업체별 각 월 쿠폰 미사용 판매금액
					$amt_issuer_a_val = 0;//업체별 각 월 본사쿠폰 사용 금액
					$amt_issuer_s_val = 0;//업체별 각 월 가맹점 쿠폰 사용 금액
					switch($amt_result[$k]->issuer){
						case 'A'://본사
							if($amt_result[$k]->coupon_dc_type == "R"){//할인율
								$a_rate = $amt_result[$j]->coupon_dc_rate/100;
								$a_rate_total = $amt_result[$k]->total_amt * $a_rate;//할인율로 차감할 금액
								$a_result_val = $amt_result[$k]->total_amt - $a_rate_total;//토탈금액 - 차감금액
								if($a_result_val < 0) $a_result_val = 0;
							}else if($amt_result[$k]->coupon_dc_type == "A"){//지정차감할인
								$a_result_val = $amt_result[$k]->total_amt - $amt_result[$k]->coupon_dc_amt;
								if($a_result_val < 0) $a_result_val = 0;
							}
							$issuer_a_coupon_sum += $a_rate_total + $amt_result[$k]->coupon_dc_amt;//본사 쿠폰 금액 합하기
							$amt_issuer_a_val = $a_result_val;
							break;
						case 'S'://가맹점
							if($amt_result[$k]->coupon_dc_type == "R"){//할인율
								$s_rate = $amt_result[$k]->coupon_dc_rate/100;
								$s_rate_total = $amt_result[$k]->total_amt * $s_rate;//할인율로 차감할 금액
								$s_result_val = $amt_result[$k]->total_amt - $s_rate_total;//토탈금액 - 차감금액
								if($s_result_val < 0) $s_result_val = 0;
							}else if($amt_result[$k]->coupon_dc_type == "A"){//지정차감할인
								$s_result_val = $amt_result[$k]->total_amt - $amt_result[$k]->coupon_dc_amt;
								if($s_result_val < 0) $s_result_val = 0;
							}
							$amt_issuer_s_val = $s_result_val;
							break;
						default:
							$amt_non_isser = $amt_result[$k]->total_amt;
							break;
					}
					$total_val += $amt_issuer_a_val + $amt_issuer_s_val + $amt_non_isser;//업체별 각종 할인된 금액 및 결제 금액 합산
				}
				$result[$i]->list->company_list[$j]->total_val = $total_val;
				$result[$i]->list->company_list[$j]->a_coupon_total = $issuer_a_coupon_sum;
				$summery_all_store += $total_val;
				$summery_all_store_coupon += $issuer_a_coupon_sum;
				$summery_all_cnt += $result[$i]->list->company_list[$j]->cnt;
				$result[$i]->list->summery_data = array("sort_date"=>$date_result[$i]->sort_date,
														"cnt"=>$summery_all_cnt,
														"total_amt"=>$summery_all_store,
														"total_coupon_amt"=>$summery_all_store_coupon);
			}
			
			$result[$i]->list->monthly_count = count($result[$i]->list->company_list);
			$totalcount += $result[$i]->list->monthly_count;		
		}
		$result['0']->total_cnt = $totalcount;
		return $result;
	}
	
	function get_members_info($store_id,$_data,$type='',$offset='',$plimit=''){
		extract($_data);
		$fdate = strtotime($search_start_dt."-01");
		$ldate = strtotime($search_end_dt."-01");
		$csdate = date('Y-m-d', strtotime('first day of', $fdate));
		$cedate = date('Y-m-d', strtotime('last day of', $ldate));
		$where = " where order_dt between '".$csdate."' and  '".$cedate."'";

		if($plimit != '' || $offset != ''){
			$limit_query = ' limit '.$offset*$plimit.', '.$plimit;
		}
		if($mem_keyword != ''){
			$sort_user_data = " and c.ID='".$mem_keyword."'";
		}
        if($type == "excel") $limit_query="";
		$sql = "select * from
				(select date_format(a.order_dt, '%Y-%m') as sort_date, a.store_name as store_name, sum(a.total_amt) as total_amt, c.user_name as user_name, count(*) as cnt
                from 
                tbl_order a 
                left join tbl_store_coupon b on a.coupon_id=b.idx 
                left join tbl_user c on a.user_id=c.ID 
                where a.order_dt between '".$csdate."' and  '".$cedate."' ".$sort_user_data." 
                group by date_format(a.order_dt, '%Y-%m'), a.user_id ) as ptable 
                order by ptable.sort_date desc, if(ascii(substring(ptable.store_name,1)) < 128, 9, 1) asc, ptable.store_name asc".$limit_query;
		$query = $this->db->query($sql);
		if($type=="count"){
			$result = $query->num_rows();
		}else{
			$result = $query->result();
		}
		return $result;
	}
}
?>