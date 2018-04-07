<?php 
defined('BASEPATH') OR exit('No direct script access allowed'); 
?>
<style>
	.excel_table th{background-color:#bcbcbc;}
</style>
<table class="excel_table" style="width:100%;border:1px solid #bcbcbc" border=1 cellspacing="0" cellpadding="0">
	<thead>
		<tr>
			<th rowspan="2">NO</th>
			<th rowspan="2">판매일시</th>
			<th rowspan="2">업체코드</th>
			<th rowspan="2">업체명</th>
			<th rowspan="2">회원코드</th>
			<th rowspan="2">회원명</th>
			<th rowspan="2">상품</th>
			<th rowspan="2">주문금액</th>
			<th colspan="2">가맹점쿠폰</th>
			<th colspan="2">본사쿠폰</th>
			<th rowspan="2">결제금액</th>
			<th rowspan="2">정산금액</th>
		</tr>
		<tr>
			<th>금액</th>
			<th>코드</th>
			<th>금액</th>
			<th>코드</th>
		</tr>
	</thead>
	<tbody>
	<?php
		foreach($search_data as $k => $v):
		//주문코드 맞추기
		$order_dt_crob = str_replace("-","",$v->order_dt);
		$f_data = substr($order_dt_crob, 2, 8);
		$len_s_no = strlen($v->order_no);
		switch($len_s_no){
			case 1:
				$s_no = '000'.$v->order_no;
				break;
			case 2:
				$s_no = '00'.$v->order_no;
				break;
			case 3:
				$s_no = '0'.$v->order_no;
				break;
			default:
				$s_no = $v->order_no;
				break;
		}
		$order_no = $f_data.$s_no;
		//결제처리결과
		$pay_status = $v->pay_result;
		switch($pay_status){
			case 1:
				$pay_result = "정상";
				break;
			case 2:
				$pay_result = "취소";
			 default:
			 	$pay_result = "기타";
				break;
		}
		//쿠폰발주처 + 정산금액
		switch($v->issuer){
			case A://본사
				//할인율 쿠폰 사용일 경우 
				if($v->coupon_dc_type == "R"){//할인율
					$rate = $v->coupon_dc_rate/100;
					$rate_total = $v->total_amt * $rate;
					//결제금액
					$pay_money = $v->total_amt - $rate_total;
					if($pay_money < 0) $pay_money = 0;
					$coupon_comm = number_format($rate_total);
				}else if($v->coupon_dc_type == "A"){
					$pay_money = $v->total_amt - $v->coupon_dc_amt;
					if($pay_money < 0) $pay_money = 0;
					$coupon_comm = number_format($v->coupon_dc_amt);
				}
				$coupon_manage = "-";
				$calc_result = $pay_money + $v->coupon_dc_amt;
				break;
			case S://가맹점
				$coupon_comm = "-";
				if($v->coupon_dc_type == "R"){//할인율
					$rate = $v->coupon_dc_rate/100;
					$rate_total = $v->total_amt * $rate;
					//결제금액
					$pay_money = $v->total_amt - $rate_total;
				if($pay_money < 0) $pay_money = 0;
					$coupon_manage = number_format($rate_total);
				}else if($v->coupon_dc_type == "A"){
					$pay_money = $v->total_amt - $v->coupon_dc_amt;
					if($pay_money < 0) $pay_money = 0;
					$coupon_manage = number_format($v->coupon_dc_amt);
				}
				$calc_result = $pay_money;
				break;
			default:
				$pay_money = $v->total_amt;
				if($pay_money < 0) $pay_money = 0;
				$coupon_comm = "-";
				$coupon_manage = "-";
				$calc_result = $v->total_amt;
				break;
		}
	?>
		<tr>
			<td><?=$totalcount ?></td>
			<td><?=$v->order_time ?></td>
			<td><?=$v->mtouch_code?></td>
			<td><?=$v->store_name ?></td>
			<td>--</td>
			<td><?=$v->user_name?></td>
			<td><?=$v->goods_desc?></td>
			<td><?=number_format($v->total_amt)?></td>
			<td><?=$coupon_manage?></td>
			<td>-</td>
			<td><?=$coupon_comm?></td>
			<td>-</td>
			<td><?=number_format($pay_money)?></td>
			<td><?=number_format($calc_result)?></td>
		</tr>
	<?php
		$totalcount--;
		endforeach;
	?>
	</tbody>
</table>