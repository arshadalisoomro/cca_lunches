<div class="container">
	<div class="row">
		<div class="calheader clearfix">
			<table style="width:100%;">
				<tr>
					<td>
						<table class="pull-left">
							<tr>
								<td><img class="navbtn prev" src="<?php echo URL; ?>public/img/back.png" alt="back"></td>
								<td><img class="navbtn next" src="<?php echo URL; ?>public/img/next.png" alt="next"></td>
								<td><div class="weekdesc">This week</div></td>
							</tr>
						</table>
					</td>
					<!--<td><img class="ajaxloader invisible" src="<?php echo URL; ?>public/img/ajax-loader.gif" alt="loader"><div class="ajaxactiontext hide">Updating...</div></td>-->
					<td><img class="ajaxloader invisible" src="<?php echo URL; ?>public/img/ajax-loader.gif" alt="loader"></td>
					<td>
						<table class="pull-right">
							<tr>
								<?php if (Session::get('user_account_type') != ACCOUNT_TYPE_ADMIN): ?>
									<td><div id="amountduetext">Amount<br>Due</div></td>
									<td><div id="amountdue"><?php echo $this->amountdue; ?></div></td>
									<td>
										<a id="paypal" href="<?php echo URL; ?>account/index">
											<img id="paypalimg" src="https://www.paypalobjects.com/en_US/i/btn/x-click-but06.gif" alt="back">
										</a>
									</td>
									<td class="hide">
								<?php else: ?>
									<td>
										<div id="orderlunchfor">Lunch<br>Orders For</div>
									</td>
									<td>
								<?php endif; ?>
									<form>
										<select id="accountnames" class="form-control">
										<?php
											$accid = Session::get('account_id');
											foreach ($this->accountnames as $accountname) {
												if ($accountname->id == $accid)
													echo '<option selected value="'.$accountname->id.'">'.$accountname->account_name.'</option>';
												else
													echo '<option value="'.$accountname->id.'">'.$accountname->account_name.'</option>';
											}
										?>
										</select>
									</form>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</div>
		
		<div id="lunchCarousel" class="carousel slide" data-interval="false" data-wrap="false">
			<div class="carousel-inner">
				<div class="item active">
					<table id="lunchestable" class="table1 table-bordered1">
						<?php echo $this->lunchesTableWeek; ?>
					</table>
				</div>
			</div>
		</div>
		<div class="todaytext">Today is <a class="returntotoday" href="#"><?php echo date('l, F jS, Y') ?></a></div>
	</div>	 
</div>

<div class="modal fade" id="modalOrderLunch" tabindex="-1" role="dialog" data-backdrop="static">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<div id="lunchdate"></div>
				<div id="orderee"></div>
			</div>
			<div class="modal-body">
				<div id="lunchimg"></div>
					<form id="lunchform" role="form">
						<div class="radio">
							<label>
								<input type="radio" id="nlo" name="rb" value="0">No Lunch Ordered
							</label>
						</div>
						<div class="orstyle">– or –</div>
						<div id="lunchchoices"></div>
					</form>
				<div id="lunchincludes"></div>
			</div>
			<div class="modal-footer">
				<button id="btnOrderLunchOK" type="button" class="btn btn-primary btn-sm">OK</button>
				<button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Cancel</button>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript" src="<?php echo URL; ?>public/js/orderlunches.js?n4ht9b"></script>