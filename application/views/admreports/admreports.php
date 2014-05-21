<div class="container page">
    <div style="height: 50px;">
		<h2 style="float:left;">Administrator Reports</h2>
		<table class="pull-right">
		<tr><td>
			<select id="admreportselect" class="form-control">
				<option value="0">[Select Report]</option>
				<option value="1">Lunch Orders By Provider</option>
				<option value="2">Lunch Orders By Student/Staff</option>
				<option value="3">Account Balances</option>
				<option value="4">Account Details</option>
			</select>
			</td>
			<td>&nbsp;&nbsp;</td>
			<td>
			<select id="admreportdate" class="hide form-control">
				<option value="0">[Select Date]</option>
				<?php
					foreach ($this->lunchdates as $lunchdate) {
						$date = new DateTime($lunchdate->provideDate);
						echo '<option value="'.$date->format('Y-m-d').'">'.$date->format('l, F jS, Y').'</option>';
					}
				?>
			</select>
			</td>
			<td>
			<select id="admreportaccount" class="hide form-control">
				<option value="0">[Select Account]</option>
				<?php
					foreach ($this->accounts as $account) {
						echo '<option value="'.$account->account_id.'">'.$account->account_name.'</option>';
					}
				?>
			</select>
			</td>
		</tr>
		</table>
    </div>
	<hr >
	<div id="admreportdata"></div>
</div>

<script type="text/javascript">
	if (typeof $ != 'undefined') {
		$(document).ready(function() {
			"use strict";
			$(document).on('click', '.btn-print', function (e) {
				var f = $('.new-tab-opener');
				f.attr('action', $(this).attr('data-href'));
				f.submit();
			});
		});
	}
</script>