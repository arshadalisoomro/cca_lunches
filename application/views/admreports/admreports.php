<div class="container page">
    <div style="height: 50px;">
		<h2 style="float:left;margin-top: 12px;">Administrator Reports</h2>
		<div style="float:right;margin-top: 10px;">
			<select id="admreportselect">
				<option value="0">[Select Report]</option>
				<option value="1">Lunch Orders By Provider</option>
				<option value="2">Lunch Orders By Student/Staff</option>
				<option value="3">Account Balances</option>
				<option value="4">Account Details</option>
			</select>
			<select id="admreportdate" class="hide">
				<option value="0">[Select Date]</option>
				<?php
					foreach ($this->lunchdates as $lunchdate) {
						$date = new DateTime($lunchdate->provideDate);
						echo '<option value="'.$date->format('Y-m-d').'">'.$date->format('l, F jS, Y').'</option>';
					}
				?>
			</select>
			<select id="admreportaccount" class="hide">
				<option value="0">[Select Account]</option>
				<?php
					foreach ($this->accounts as $account) {
						echo '<option value="'.$account->account_id.'">'.$account->account_name.'</option>';
					}
				?>
			</select>
		</div>
    </div>
	<hr >
	<div id="admreportdata"></div>
</div>