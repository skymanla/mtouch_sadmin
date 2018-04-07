<?php 
defined('BASEPATH') OR exit('No direct script access allowed'); 
?>
<style>
	.excel_table th{background-color:#bcbcbc;}
</style>
<table class="excel_table" style="width:100%;border:1px solid #bcbcbc" border=1 cellspacing="0" cellpadding="0">
	<thead>
						<tr class="active">
							<th class="">이용월</th>
							<th class="">업체코드</th>
							<th class="">업체명</th>
							<th class="">회원코드</th>
							<th class="">회원명</th>
							<th class="">이용건수</th>
							<th class="">이용금액</th>
						</tr>
					</thead>
	<tbody>
						<?php
						//정렬 기준 월
						foreach($search_data as $k => $v):
							$sort_date = explode("-",$v->sort_date);
							$sort_date = $sort_date[0]."년 ".$sort_date[1]."월";
						?>
						<tr>
							<td><?=$sort_date?></td>
							<td>업체코드</td>
							<td><?=$v->store_name?></td>
							<td>회원코드</td>
							<td><?=$v->user_name?></td>
							<td><?=$v->cnt?></td>
							<td><?=number_format($v->total_amt)?></td>
						</tr>
						<?php endforeach; ?>
					</tbody>
</table>