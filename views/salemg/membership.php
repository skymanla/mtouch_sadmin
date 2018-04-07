<?php 
defined('BASEPATH') OR exit('No direct script access allowed'); 
?>

<div id="page-wrapper">
	<div class="container-fluid">
		<!-- Page Heading -->
		<div class="row">
			<div class="col-lg-12">
				<h1 class="page-header">
					회원별 이용내역
				</h1>
				<ol class="breadcrumb">
					<li>
						<i class="fa fa-dashboard"></i> <a href="/">메인페이지</a>
					</li>
			        <li class="active">판매관리</li>
					<li class="active">회원별 이용내역</li>
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
					회원코드/이름
					<input type="text" id="mem_keyword" name="mem_keyword" value="<?=$p->mem_keyword?>" class="form-control" style="width: 200px;" />
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
					<label class="control-label">리스트 수:<?=$totalcount ?></label>
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
				<table class="table table-bordered table-hover salemenu_table">
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
							$comm_s_date = date('Y-m-d', strtotime('first day of', strtotime($v->sort_date."-01")));
							$comm_e_date = date('Y-m-d', strtotime('last day of', strtotime($v->sort_date."-01")));
						?>
						<tr>
							<td><?=$v->sort_date?></td>
							<td>--</td>
							<td><?=$v->store_name?></td>
							<td>--</td>
							<td><?=$v->user_name?></td>
							<td><a onclick="link_sale('<?=$comm_s_date?>','<?=$comm_e_date?>','<?=$v->user_name?>')"><?=$v->cnt?></a></td>
							<td><?=number_format($v->total_amt)?></td>
						</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>
		</div>

		<div class="row text-center">
			<div class="col-md-12 table-responsive">
				<?=$pagination?>
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
			$('#mem_keyword').val('');
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
				.attr('action', 'export_membership')
				.attr('onsubmit', true)
				.submit();
		} finally {
			$btn.button('reset');
		}
	});
});

function link_sale(sdate, ldate, sidx){
	location.href="/salemg/done?limit=&search_start_dt="+sdate+"&search_end_dt="+ldate+"&sale_all=1&cupon_all=1&search_keyword="+sidx;
}

//]]>
</script>
