<?php 
defined('BASEPATH') OR exit('No direct script access allowed'); 
?>
<style>
	.excel_table th{background-color:#bcbcbc;}
</style>
<table class="excel_table" style="width:100%;border:1px solid #bcbcbc" border=1 cellspacing="0" cellpadding="0">
	<thead>
						<tr class="active">
							<th class="" rowspan="2">이용월</th>
							<th class="" rowspan="2">업체코드</th>
							<th class="" rowspan="2">업체명</th>
							<th class="" rowspan="2">이용건수</th>
							<th class="" rowspan="2">판매금액</th>
							<th class="" rowspan="2">본사쿠폰금액</th>
							<th class="" rowspan="2">정산금액</th>
							<th class="" rowspan="2">총 이용건수</th>
							<th class="" rowspan="2">정산금액 합계</th>
							<th class="" colspan="3">판매수수료(1%)</th>
							<th class="" rowspan="2">지급금액</th>
						</tr>
						<tr class="active">
							<th class="">공급가액</th>
							<th class="">부가세</th>
							<th class="">합계</th>
						</tr>
					</thead>
					<tbody>
						<?php
						//정렬 기준 월						
						for($i=0;$i<count($search_data);$i++){
							$company_list_count = $search_data[$i]->list->monthly_count;
							$tot_use = $search_data[$i]->list->summery_data['cnt'];
							$tot_amt = $search_data[$i]->list->summery_data['total_amt'];
							$tot_coupon = $search_data[$i]->list->summery_data['total_coupon_amt'];
							$tot_calc = $tot_amt+$tot_coupon;
							$val_supply = $tot_calc*0.01;
							$val_surtax = $val_supply*0.1;
							$val_commission_total = $val_supply+$val_surtax;
							$val_get_pay = $tot_calc-$val_commission_total;

							//조회날짜 처리
							$comm_s_date = date('Y-m-d', strtotime('first day of', strtotime($search_data[$i]->list->sort_date."-01")));
							$comm_e_date = date('Y-m-d', strtotime('last day of', strtotime($search_data[$i]->list->sort_date."-01")));
							if($company_list_count > 0){
						?>
						<tr>
							<td rowspan="<?=$company_list_count?>"><a onclick="link_sale('<?=$comm_s_date?>','<?=$comm_e_date?>','<?=$p->sale_keyword?>')"><?=$search_data[$i]->list->sort_date?></a></td>
							<?php
							//정렬 기준월에 따른 데이터
							//데이터 개수가 1 이상이면
							if($company_list_count > 1){
								for($j=0;$j<$company_list_count;$j++){
									if($j=="0"){
							?>
							<td><a onclick="link_sale('<?=$comm_s_date?>','<?=$comm_e_date?>','<?=$search_data[$i]->list->company_list[$j]->store_id?>')">--</a></td>
							<td><?=$search_data[$i]->list->company_list[$j]->store_name?></td>
							<td><?=$search_data[$i]->list->company_list[$j]->cnt?></td>
							<td><?=number_format($search_data[$i]->list->company_list[$j]->total_val)?></td>
							<td><?=number_format($search_data[$i]->list->company_list[$j]->a_coupon_total)?></td>
							<td><?=number_format($search_data[$i]->list->company_list[$j]->total_val+$search_data[$i]->list->company_list[$j]->a_coupon_total)?></td>
							<td rowspan="<?=$company_list_count?>"><?=$tot_use?></td>
							<td rowspan="<?=$company_list_count?>"><?=number_format($tot_calc)?></td>
							<td rowspan="<?=$company_list_count?>"><?=number_format($val_supply)?></td>
							<td rowspan="<?=$company_list_count?>"><?=number_format($val_surtax)?></td>
							<td rowspan="<?=$company_list_count?>"><?=number_format($val_commission_total)?></td>
							<td rowspan="<?=$company_list_count?>"><?=number_format($val_get_pay)?></td>
						</tr>
							<?php }else{ ?>
						</tr>
						<tr>
							<td><a onclick="link_sale('<?=$comm_s_date?>','<?=$comm_e_date?>','<?=$search_data[$i]->list->company_list[$j]->store_id?>')">--</a></td>
							<td><?=$search_data[$i]->list->company_list[$j]->store_name?></td>
							<td><?=$search_data[$i]->list->company_list[$j]->cnt?></td>
							<td><?=number_format($search_data[$i]->list->company_list[$j]->total_val)?></td>
							<td><?=number_format($search_data[$i]->list->company_list[$j]->a_coupon_total)?></td>
							<td><?=number_format($search_data[$i]->list->company_list[$j]->total_val+$search_data[$i]->list->company_list[$j]->a_coupon_total)?></td>
						</tr>
							<?php 
									}
								}
							}else{
							?>
							<td><a onclick="link_sale('<?=$comm_s_date?>','<?=$comm_e_date?>','<?=$search_data[$i]->list->company_list[$j]->store_id?>')">--</a></td>
							<td><?=$search_data[$i]->list->company_list[0]->store_name?></td>
							<td><?=$search_data[$i]->list->company_list[0]->cnt?></td>
							<td><?=number_format($search_data[$i]->list->company_list[0]->total_val)?></td>
							<td><?=number_format($search_data[$i]->list->company_list[0]->a_coupon_total)?></td>
							<td><?=number_format($search_data[$i]->list->company_list[0]->total_val+$search_data[$i]->list->company_list[0]->a_coupon_total)?></td>
							<td rowspan="<?=$company_list_count?>"><?=$tot_use?></td>
							<td rowspan="<?=$company_list_count?>"><?=number_format($tot_calc)?></td>
							<td rowspan="<?=$company_list_count?>"><?=number_format($val_supply)?></td>
							<td rowspan="<?=$company_list_count?>"><?=number_format($val_surtax)?></td>
							<td rowspan="<?=$company_list_count?>"><?=number_format($val_commission_total)?></td>
							<td rowspan="<?=$company_list_count?>"><?=number_format($val_get_pay)?></td>
						</tr>
						<?php 
								}
							}
						}	
						?>
					</tbody>
</table>