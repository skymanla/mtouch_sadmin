<?php 
defined('BASEPATH') OR exit('No direct script access allowed'); 
//최초 로딩시 checkbox checked 설정
if(empty($_GET)) $get_checked = "1";
if($get_checked == true){
	$p->sale_all = "1";
	$p->sale_normal = "1";
	$p->sale_cancel = "1";
	$p->cupon_all = "1";
	$p->cupon_no = "1";
	$p->cupon_comm = "1";
	$p->cupon_manage = "1";
}
?>

<div id="page-wrapper">
	<div class="container-fluid">
		<!-- Page Heading -->
		<div class="row">
			<div class="col-lg-12">
				<h1 class="page-header">
					판매내역
				</h1>
				<ol class="breadcrumb">
					<li>
						<i class="fa fa-dashboard"></i> <a href="/">메인페이지</a>
					</li>
			        <li class="active">판매관리</li>
					<li class="active">판매내역</li>
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
						<input type="text" id="search_start_dt" name="search_start_dt" value="<?=$p->search_start_dt ?>" class="form-control date" style="width:120px;" placeholder="YYYY-MM-DD" />
						<span class="input-group-addon">
							<span class="glyphicon glyphicon-calendar"></span>
						</span>
					</div>
					~
					<div class="input-group" id='datetimepicker2'>
						<input type="text" id="search_end_dt" name="search_end_dt" value="<?=$p->search_end_dt ?>" class="form-control date" style="width:120px;" placeholder="YYYY-MM-DD" />
						<span class="input-group-addon">
							<span class="glyphicon glyphicon-calendar"></span>
						</span>
					</div>
				</div>
			</div>
			<div class="row form-group">
				<div class="col-md-12 form-inline">
					<label class="mg-r-20">판매상태</label>
					<label for="sale_all" class="label-control"><input type="checkbox" id="sale_all" name="sale_all" value="1" <?=$p->sale_all == '1' ? 'checked' : '' ?> /> 전체 </label> &nbsp;|&nbsp;
					<label for="sale_normal"><input type="checkbox" id="sale_normal" name="sale_normal" value="1" <?=$p->sale_normal == '1' ? 'checked' : '' ?> /> 정상</label> &nbsp;|&nbsp;
					<label for="sale_cancel"><input type="checkbox" id="sale_cancel" name="sale_cancel" value="1" <?=$p->sale_cancel == '1' ? 'checked' : '' ?> /> 취소</label> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
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
				</div>
			</div>

			<div class="row form-group">
				<div class="col-md-12 form-inline">
					<label class="mg-r-20">쿠폰사용</label>
					<label for="cupon_all" class="label-control"><input type="checkbox" id="cupon_all" name="cupon_all" value="1" <?=$p->cupon_all == '1' ? 'checked' : '' ?> /> 전체 </label> &nbsp;|&nbsp;
					<label for="cupon_no"><input type="checkbox" id="cupon_no" name="cupon_no" value="1" <?=$p->cupon_no == '1' ? 'checked' : '' ?> /> 미사용</label> &nbsp;|&nbsp;
					<label for="cupon_comm"><input type="checkbox" id="cupon_comm" name="cupon_comm" value="1" <?=$p->cupon_comm == '1' ? 'checked' : '' ?> /> 본사</label> &nbsp;|&nbsp;
					<label for="cupon_manage"><input type="checkbox" id="cupon_manage" name="cupon_manage" value="1" <?=$p->cupon_manage == '1' ? 'checked' : '' ?> /> 가맹점</label>&nbsp;
					&nbsp;&nbsp;&nbsp;
					검색어
					<input type="text" id="search_keyword" name="search_keyword" value="<?=$p->search_keyword?>" class="form-control" style="width: 200px;" />
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
							<th class="">No.</th>
							<th class="">판매일시</th>
							<th class="">주문코드</th>
							<th class="">업체코드</th>
							<th class="">업체명</th>
							<th class="">회원코드</th>
							<th class="">회원명</th>
							<th class="">상품</th>
							<th class="">주문금액</th>
							<th class="">가맹점쿠폰</th>
							<th class="">본사쿠폰</th>
							<th class="">결제금액</th>
							<th class="">판매상태</th>
							<th class="">정산금액</th>
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
										if($pay_money < 0) $pay_money = 0; //할인율로 마이너스가 나올리는 없지만...
										$coupon_comm = number_format($rate_total);
									}else if($v->coupon_dc_type == "A"){
										$pay_money = $v->total_amt - $v->coupon_dc_amt;
										if($pay_money < 0) $pay_money = 0;//결제금액이 할인금액보다 작을 경우 마이너스
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
										if($pay_money < 0) $pay_money = 0;//할인율로 마이너스가 나올리는 없지만...
										$coupon_manage = number_format($rate_total);
									}else if($v->coupon_dc_type == "A"){
										$pay_money = $v->total_amt - $v->coupon_dc_amt;
										if($pay_money < 0) $pay_money = 0;//결제금액이 할인금액보다 작을 경우 마이너스
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
							<td><?=$paging['no'] ?></td>
							<td><?=$v->order_time ?></td>
							<td><?=$order_no ?></td>
							<td><a onclick="link_comm(<?=$v->store_id?>);">--</a></td>
							<td><?=$v->store_name ?></td>
							<td><a onclick="link_member(<?=$v->user_id?>);">--</a></td>
							<td><?=$v->user_name?></td>
							<td><?=$v->goods_desc?></td>
							<td><?=number_format($v->total_amt)?></td>
							<td><?=$coupon_manage?></td>
							<td><?=$coupon_comm?></td>
							<td><?=number_format($pay_money)?></td>
							<td><?=$pay_result?></td>
							<td><?=number_format($calc_result)?></td>
						</tr>
						<?php
							$paging['no']--;
						endforeach;
						?>
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
		format: 'YYYY-MM-DD',
		locale: 'ko',
		showTodayButton:true,
		allowInputToggle:true,
		defaultDate: new Date()
	});	//.data('DateTimePicker').minDate(new Date());
  $('#datetimepicker2').datetimepicker({
		format: 'YYYY-MM-DD',
		locale: 'ko',
		showTodayButton:true,
		allowInputToggle:true,
		defaultDate:new Date()
	});	//.data('DateTimePicker').minDate(new Date());

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
			$('#search_start_dt').val(moment().format('YYYY-MM-DD'));
			$('#search_end_dt').val(moment().format('YYYY-MM-DD'));
			$('input[type=checkbox]').prop('checked', true);
			$('#sale_keyword').val('');
			$('#search_keyword').val('');
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
				.attr('action', 'export_sale_all')
				.attr('onsubmit', true)
				.submit();
		} finally {
			$btn.button('reset');
		}
	});
	$('#sale_all').change(function(){
		if($('#sale_all').is(":checked")){
			$('input[name=sale_normal]').prop("checked", true);
			$('input[name=sale_cancel]').prop("checked", true);
		}
	});
	$('#cupon_all').change(function(){
		if($('#cupon_all').is(":checked")){
			$('input[name=cupon_no]').prop("checked", true);
			$('input[name=cupon_comm]').prop("checked", true);
			$('input[name=cupon_manage]').prop("checked", true);
		}
	});
});

function link_comm(idx){
	var get_sdate = "<?=$p->search_start_dt?>";
	var get_sdate = get_sdate.substring(0, 7);
	var get_edate = "<?=$p->search_end_dt?>";
	var get_edate = get_edate.substring(0, 7);
	var get_sidx = idx;
	location.href="/salemg/comm/?limit=&search_start_dt="+get_sdate+"&search_end_dt="+get_edate+"&sale_keyword="+idx;
}

function link_member(idx){
	var get_sdate = "<?=$p->search_start_dt?>";
	var get_sdate = get_sdate.substring(0, 7);
	var get_edate = "<?=$p->search_end_dt?>";
	var get_edate = get_edate.substring(0, 7);
	var get_sidx = idx;
	location.href="/salemg/membership/?limit=&search_start_dt="+get_sdate+"&search_end_dt="+get_edate+"&mem_keyword="+idx;
}
//]]>
</script>
