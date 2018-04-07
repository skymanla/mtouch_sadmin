<?php defined('BASEPATH') OR exit('No direct script access allowed');?>
<div id="page-wrapper">

			<div class="container-fluid">

				<!-- Page Heading -->
				<div class="row">
					<div class="col-lg-12">
						<h1 class="page-header">
							주문관리
							<small>취소내역</small>
						</h1>
						<ol class="breadcrumb">
							<li>
								<i class="fa fa-dashboard"></i> <a href="/">메인페이지</a>
							</li>
							<li class="active">
								<i class="fa fa-home"></i> 주문관리
							</li>
							 <li class="active">
								<i class="fa fa-cancle"></i> 취소내역
							</li>
						</ol>
					</div>
				</div>
				<!-- /.row -->

			<form name="search" method="post" action="">
				<input type=hidden name="url" value="order-search">
				<div class="row">
					<div class="col-md-2">
						<div class="form-group">
							<label>선택검색</label>
						</div>
					</div>
					<div class="col-md-2">
						<div class="form-group ">
							<select id="option" class="form-control" name="search_type">
								<option value="goods_name">상품명</option>
								<option value="order_id">주문번호</option>
<!-- 								<option value="user_id">주문자ID</option> -->
							</select>
						</div>
					</div>
					<div class="col-md-4">
						<div class="form-group ">
							<input class="form-control col-xs-4" id="keyword" name="keyword" type="text" placeholder="검색어입력">
						</div>
					</div>
					<div class="col-md-4">
						<div class="form-group ">
							<select name="pay_method" class="form-control">
								<option value="">결제수단전체</option>
								<option value="CARD">신용카드</option>
<!-- 								<option value="BANK">계좌이체</option> -->
								<option value="PHON">휴대폰</option>
								<option value="TOSS">간편결제</option>
								<option value="ETC">기타</option>
							</select>
						</div>
					</div>
				</div>
				<!-- /.row -->
				<div class="row">
					<div class="col-md-2">
						<div class="form-group">
							<label>기간설정</label>
						</div>
					</div>
					<div class="col-md-2">
						<div class="form-group ">
							<input class="form-control" id="sdate" name="start_dt" type="text" value="<?php echo ($_POST['start_dt'])?$_POST['start_dt']:date('Y-m-d')?>">
						</div>
					</div>
					<div class="col-md-2">
						<div class="form-group ">
							<input class="form-control" id="edate" name="end_dt" type="text" value="<?php echo $_POST['end_dt']?$_POST['end_dt']:date('Y-m-d')?>">
						</div>
					</div>
					<div class="col-md-1">
						<div class="form-group ">
							<button type="submit" id="searchbtn" class="btn btn-sm btn-primary">검색</button>
						</div>
					</div>
				</div>
				</form>
				<!-- /.row -->
				<hr>
				<div class="row">
					<div class="col-md-2">
						<div class="form-group">
							<label>취소내역 : <?php echo $totalcount;?>개</label>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-lg-12 table-responsive">
						<table class="table table-bordered table-hover table-striped">
							<thead>
								<tr>
									<th>주문번호</th>
									<th>상품명</th>
									<th>상품가격</th>
									<th>주문자ID</th>
									<th>결제방법</th>
									<th>주문일</th>
									<th>취소일</th>
								</tr>
							</thead>
							<tbody>
							<?php foreach($list as $item):

							$goodsname = array("","");
							if ($item['p_goods_name']) $goodsname[0] = $item['p_goods_name'];
							else unset($goodsname[0]);
							if ($item['d_goods_name']) $goodsname[1] = $item['d_goods_name'];
							else unset($goodsname[1]);

							?>
								<tr>
									<td><a href="/order/detail/<?php echo $item['user_id'];?>/<?php echo $this->session->userdata("S_ID");?>/<?php echo $item['idx'];?>/<?php echo mktime($item['paid_time']);?>"><?php echo $item['idx'];?></a></td>
									<td><?php echo implode(" / ", $goodsname);?></td>
									<td><?php echo number_format($item['total_amt']);?></td>
									<td><?php echo $item['user_login'];?></td>
									<td><?php echo $item['pay_method'];?></td>
									<td><?php echo $item['paid_time'];?></td>
									<td><?php echo $item['cancel_time'];?></td>
								</tr>
							<?php endforeach;?>
							</tbody>
						</table>
					</div>

				</div>
				<!-- /.row -->
				<div class="row text-center" id="pagination">
					<div class="col-lg-12 table-responsive">
						<?php echo $this->pagination->create_links();?>
					</div>
				</div>
				<!-- /.row -->
			</div>


			<!-- /.container-fluid -->

		</div>

		<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
		<script type="text/javascript">
		<!--
			$("#option").on("change", function () {
				$("#keyword").attr('name', $("#option option:selected").val());
			});
			$(document).ready(function(){
				$( "#sdate" ).datepicker({
					dateFormat:'yy-mm-dd',
					maxDate : '0D',
				});
				$( "#edate" ).datepicker({
					dateFormat:'yy-mm-dd',
					maxDate : '0D',
				});
				// pagination
				$('#pagination').on('click', 'a',function(e) {
					e.preventDefault();
					var cur_page = $(this).data('ci-pagination-page');
					$('form[name=search]').attr('action','/order/cancel/'+cur_page);
					$('form[name=search]').submit()
					return false
				});
			});
		//-->
		</script>