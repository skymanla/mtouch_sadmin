<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
//print_r($search_data);
?>
<div id="page-wrapper">
	<div class="container-fluid">
		<!-- Page Heading -->
		<div class="row">
			<div class="col-lg-12">
				<h1 class="page-header">
					업체별 이용내역
				</h1>
				<ol class="breadcrumb">
					<li>
						<i class="fa fa-dashboard"></i> <a href="/">메인페이지</a>
					</li>
			        <li class="active">판매관리</li>
					<li class="active">업체별 이용내역</li>
				</ol>
			</div>
		</div>
		<!-- /.row -->
		<!-- get -->
		<form method="get" id="frmSearch" name="frmSearch" action="" onsubmit="return false;" class="form-horizontal">
			<input type="hidden" id="limit" name="limit" value="<?=$p->limit ?>" />
			<div class="row form-group">
				<div class="col-md-12 form-inline">
					<label class="mg-r-20">기간조회</label>
					<div class="input-group" id='datetimepicker1'>
						<input type="text" id="search_start_dt" name="search_start_dt" value="<?=$p->search_start_dt ?>" class="form-control date" style="width:120px;" placeholder="YYYY-MM" />
						<span class="input-group-addon">
							<span class="glyphicon glyphicon-calendar"></span>
						</span>
					</div>
					~
					<div class="input-group" id='datetimepicker2'>
						<input type="text" id="search_end_dt" name="search_end_dt" value="<?=$p->search_end_dt ?>" class="form-control date" style="width:120px;" placeholder="YYYY-MM" />
						<span class="input-group-addon">
							<span class="glyphicon glyphicon-calendar"></span>
						</span>
					</div>

					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					업체명
					<select name="sale_keyword" id="sale_keyword" class="form-control" style="width:200px">
						<option value="">선택안함</option>
						<?php 
							foreach($company_list as $k=>$v):
						?>
						<option value="<?=$v->store_id?>" <?php if($v->store_id == $p->sale_keyword) echo 'selected'; else echo ""; ?>>
							<?=$v->store_name?>
						</option>
						<?php endforeach; ?>
					</select>
					&nbsp;&nbsp;&nbsp;
					<button type="button" id="btn_search" name="btn_search" class="btn btn-primary" autocomplete="off">검색</button>
					<button type="button" id="btn_search_reset" name="btn_search_reset" class="btn btn-default" autocomplete="off">초기화</button>
				</div>
			</div>
		</form>
		<hr />

		<br />

		<div class="form-horizontal">
			<div class="form-group">
				<div class="col-md-2">
					<label class="control-label">리스트 수:<?=$search_data['0']->total_cnt ?></label>
				</div>
				<div class="col-md-10 text-right form-inline">
					리스트출력
					<select id="rowcount" name="rowcount" class="form-control" style="width: 100px;">
						<option value="30" <?=$p->limit == '30' ? 'selected' : '' ?> >30</option>
						<option value="50" <?=$p->limit == '50' ? 'selected' : '' ?> >50</option>
						<option value="100" <?=$p->limit == '100' ? 'selected' : '' ?> >100</option>
						<option value="200" <?=$p->limit == '200' ? 'selected' : '' ?> >200</option>
					</select>
					<button type="button" id="btn_export_excel" name="btn_export_excel" class="btn btn-default" autocomplete="off">엑셀 다운로드</button>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-md-12 table-responsive">
				<table class="table table-bordered table-hover salemenu_table" id="salemenu_table">
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
			</div>
		</div>

		<div class="row text-center">
			<div class="col-md-12 table-responsive">				
				<div id="nav-pagination">
					
				</div>
			</div>
		</div>

	</div>
</div>


<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css" />
<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.min.js"></script>
<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment-with-locales.min.js"></script>
<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>

<script type="text/javascript">
//<![CDATA[
$(function() {
	// 캘린더
 $('#datetimepicker1').datetimepicker({
		format: 'YYYY-MM',
		locale: 'ko',
		allowInputToggle:true,
		defaultDate:new Date()
	});//.data('DateTimePicker').maxDate(new Date());

	$('#datetimepicker2').datetimepicker({
		format: 'YYYY-MM',
		locale: 'ko',
		allowInputToggle:true,
		defaultDate:new Date()
	});//.data('DateTimePicker').minDate(new Date())
	

	$("#datetimepicker1").on('dp.change', function(){
		$('#datetimepicker2').data('DateTimePicker').minDate($('#datetimepicker1').data('DateTimePicker').date());
	});

	$("#datetimepicker2").on('dp.change', function(){
		$('#datetimepicker1').data('DateTimePicker').maxDate($('#datetimepicker2').data('DateTimePicker').date());
	});

	// 검색
	$('#btn_search').on('click', function() {
		$('#frmSearch')
			//.attr('action', 'list')
			.attr('onsubmit', true)
			.submit()
			.loading(this);
	});

	// 초기화
	$('#btn_search_reset').on('click', function() {
		var $btn = $(this);
		$btn.loading();
		try {
			$('#search_start_dt').val(moment().format('YYYY-MM'));
			$('#search_end_dt').val(moment().format('YYYY-MM'));
			$('#sale_keyword').val('');
		} finally {
			$btn.button('reset');
		}
	});

	// 레코드 갯수
	$('#rowcount').on('change', function() {
		$('#limit').val($(this).val());
		$('#btn_search').trigger('click');
	});

	$('#btn_export_excel').on('click', function() {
		var $btn = $(this);
		$btn.loading();
		try {
			$('#frmSearch')
				.attr('action', 'export_comm_all')
				.attr('onsubmit', true)
				.submit();
		} finally {
			$btn.button('reset');
		}
	});

	//javascript 페이징 처리
	var rowPerPage = <?=$p->limit ? $p->limit : 30 ?> * 1;
	var $product = $('#salemenu_table');
	var $tr = $($product).find('tbody tr');
	var rowTotals = $tr.length;

	var pageTotal = Math.ceil(rowTotals/rowPerPage);
	var i = 0;
	for(i=0;i<pageTotal;i++){
		$('<a href="#"></a>').attr('rel', i).html(i+1).appendTo('#nav-pagination');
	}
	$tr.addClass('off-screen').slice(0, rowPerPage).removeClass('off-screen');

	var $pagingLink = $('#nav-pagination a');
	$pagingLink.on('click', function(evt){
		evt.preventDefault();
		var $this = $(this);
		if($this.hasClass('active')){
			return;
		}
		$pagingLink.removeClass('active');
       	$this.addClass('active');
       	var currPage = $this.attr('rel');
        var startItem = currPage * rowPerPage;
        var endItem = startItem + rowPerPage;

        $tr.css('opacity', '0.0')
                .addClass('off-screen')
                .slice(startItem, endItem)
                .removeClass('off-screen')
                .animate({opacity: 1}, 300);
	});
	$pagingLink.filter(':first').addClass('active');
});

function link_sale(sdate, ldate, sidx){
	location.href="/salemg/done?limit=&search_start_dt="+sdate+"&search_end_dt="+ldate+"&sale_all=1&cupon_all=1&sale_keyword="+sidx;
}
//]]>
</script>
