<div class="container pad5">

    <?php $this->renderFeedbackMessages(); ?>

    <div id="calheader">
        <div class="calnavbuttons">
	        <div class='navbtn prev'></div>
		    <div class='navbtn next'></div>
        </div>

		<span id='weekdesc'>This week</span>

		<?php
			if (Session::get('user_account_type') != ACCOUNT_TYPE_ADMIN) {
				echo '<div class="accountbaldiv">';
				echo '<span id="amountdue">Amount<br />Due</span>';
				echo '<span id="balance">'.$this->amountdue.'</span>';
				echo '<a id="paypal" href="'.URL.'account/index"><span class="paypalbtn"></span></a>';
				echo '</div>';
				echo '<div class="hide">';
			}
					
			echo '<select id="accountnames">';
			$accid = Session::get('account_id');
			foreach ($this->accountnames as $accountname) {
				if ($accountname->id == $accid)
					echo '<option selected value="'.$accountname->id.'">'.$accountname->account_name.'</option>';
				else
					echo '<option value="'.$accountname->id.'">'.$accountname->account_name.'</option>';
			}
			echo '</select>';
			echo '<div id="orderlunchfor">Lunch<br />Orders For</div>';
			if (Session::get('user_account_type') != ACCOUNT_TYPE_ADMIN)
				echo '</div>';
		?>
        <span id='loadinggif' class="hide"></span>
        <span id='loadingtext' class="hide">Loading...</span>
	</div>
	<div id="lunchgallery">
		<div id="lunchslider">
			<?php echo $this->lunchesTableWeek; ?>
		</div>
	</div>
			
	<div class="todaytext">Today is <a class="returntocurrent" href="#"><?php echo date('l, F jS, Y') ?></a></div>
			 
	<div class="modal fade" id="modalOrderLunch" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<div id="lunchdate"></div>
					<div id="orderee"></div>
				</div>
				<div class="modal-body">
					<div id="lunchimg"></div>
					<form id="lunchform">
						<div class="radio">
							<label>
								<input type="radio" id="nlo" name="rb" value="0">No Lunch Ordered
							</label>
						</div>
						<div class="orstyle">– or –</div>
						<table id="lunchchoices" class="table table-nopadding">
						</table>
					</form>
					<div id="lunchincludes"></div>
				</div>
				<div class="modal-footer">
					<button id="btnOK" type="button" class="btn btn-primary btn-sm">OK</button>
					<button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Cancel</button>
				</div>
			</div>
		</div>
	</div>
</div>