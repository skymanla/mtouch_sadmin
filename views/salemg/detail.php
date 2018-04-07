<?php
defined('BASEPATH') OR exit('No direct script access allowed');


?>
<div id="page-wrapper">

			<div class="container-fluid">

				<!-- Page Heading -->
				<div class="row">
					<div class="col-lg-12">
						<h1 class="page-header">
							주문관리
							<small>상세정보</small>
						</h1>
						<ol class="breadcrumb">
							<li>
								<i class="fa fa-dashboard"></i>  <a href="/">메인페이지</a>
							</li>
							<li class="active">
								<i class="fa fa-home"></i> 주문관리
							</li>
							 <li class="active">
								<i class="fa fa-cancle"></i> 상세정보
							</li>
						</ol>
					</div>
				</div>
				<!-- /.row -->
				<div class="row">
					<div class="col-lg-12">
						<div class="panel panel-success">
							<div class="panel-heading">
								<h3 class="panel-title"><?php echo $store_name;?></h3>
							</div>
							<div class="panel-body">
								<table width=100%>
								<tr>
									<td>주문번호 : <?php echo $idx;?></td>
									<td>주문일 : <?php echo $order_time;?></td>
									<td>주문자ID : <?php echo $user_login;?></td>
									<td>결제수단 : <?php echo $pay_method;?></td>
									<td>
									<?php
										if ($order_p_status == "D" || $order_d_status == "D") {
											?>
											완료일 : <?php echo $order_time;?>
									<?php
										}
										else {
										?>
										취소일 : <?php echo $cancel_time;?>
									<?php
										}
									?>
									</td>
								</tr>
								</table>

							</div>
						</div>
					</div>
				</div>
				<!-- /.row -->
				<div class="row">
					<div class="col-lg-12">
						<h2>주문상품</h2>
						<div class="table-responsive">
							<table class="table table-hover table-striped">
								<tbody>
								<?php
// 								var_export($list);
									foreach($list as $item):
// 										if ($item["item_type"] == "G") {
								?>
									<tr>
										<td><img src="<?php echo $item["goods_img1"]?>" class="img-thumbnail" style="width:100px;height:100px;"></td>
										<td>
											<div class="panel panel-default">
												<div class="panel-heading">
													<h3 class="panel-title"><?php echo $item["goods_name"]?> : <?php echo number_format($item["unit_price"]);?>원</h3>
												</div>
												<div class="panel-body">
													<?php
													$optiondata = json_decode($item["option_json"], true);
													foreach($optiondata as $item2):
														?>
														<table width=100%>
														<tr>
															<td><?php echo $item2['name']; ?> : <?php echo $item2['key']; ?></td>
															<td>금액 : <?php echo number_format($item2['price']); ?>원</td>
														</tr>
														</table>

													<?php
													endforeach;
													?>

												</div>
											</div>
										</td>

										<td style="vertical-align:middle;text-align:center;">총 결제금액 <br><?php echo number_format($item["total_amt"])?>원</td>
									</tr>
								<?php
// 								}
								endforeach;?>
								</tbody>
							</table>
						</div>
					</div>
				</div>
				<!-- /.row -->
				</div>
				<!-- /.row -->
				<div class="row">
					<div class="col-lg-12">

					</div>
				</div>
				<!-- /.row -->
			</div>
			<!-- /.container-fluid -->

		</div>