<?php

class AdmReportsModel {
    public function __construct(Database $db) {
        $this->db = $db;
    }
    /**
     **************************
     */
	public function getLunchDates() {
		$sth = $this->db->prepare("SELECT provideDate 
			FROM los_lunchdates 
			WHERE ordersPlaced IS NOT NULL 
			ORDER BY provideDate DESC");	
		$sth -> execute();
		$lunchdates = $sth -> fetchAll();
		return $lunchdates;
	}
    /**
     **************************
     */
	public function getAccounts() {
		$sth = $this->db->prepare("SELECT DISTINCT account_id,a.account_name
			FROM los_orders o
			INNER JOIN los_accounts a ON o.account_id=a.id
			ORDER BY a.account_name");	
		$sth -> execute();
		$accounts = $sth -> fetchAll();
		return $accounts;
	}
    /**
     **************************
     */
	public function getLunchOrdersReportByProvider($dateYMD,$forHardcopy) {
		$date = new DateTime($dateYMD);
		$res = '';
		
		if ($forHardcopy) {
			$res .= '<!DOCTYPE html><head><style>h1,h2,h3,h4,h5,h6{margin: 5px 0;}</style></head><body style="text-align:center;width:980px;margin: 20px auto;" onload="window.print();">';
			$res .= '<img style="width:142px;height:40px;" src="'.URL.'public/img/rpthdr.png"><br /><br />';
		} else {
			$res .= '<div class="printtoscreen admreport">';
			$res .= '<a data-href="'.URL.'admreports/printadmreport/1/'.$date->format('Y-m-d').'" href="#" class="btn btn-primary btn-print">Print</a>';
			$res .= '<form class="new-tab-opener" method="get" target="_blank"></form>';	
		}
		
		$res .= '<h2>Lunch Orders</h2>';
		$res .= '<h6>for</h6>';
		$res .= '<h4>'.$date->format('l, F jS, Y').'</h4>';
		
		$sth = $this -> db -> prepare("SELECT providerName,providerClass
			FROM los_lunchdates ld
			INNER JOIN los_providers lp ON lp.id=ld.providerID
			WHERE provideDate = :dateYMD");	
		$sth -> execute(array(':dateYMD' => $dateYMD));
		$provider = $sth -> fetch();
		$res .= '<br />';
		if ($forHardcopy)
			$res .= '<h3>'.$provider->providerName.'</h3>';
		else
			$res .= '<div class="provimg '.$provider->providerClass.'"></div>';
		
		$sth = $this->db->prepare("SELECT COUNT(od.menuItemID) AS itemCount,mi.itemName, SUM(od.price) AS totalPrice
			FROM los_orders o
			INNER JOIN los_orderdetails od ON o.id = od.orderID
			INNER JOIN los_menuitems mi ON od.menuItemID = mi.id
			WHERE o.orderDate = :dateYMD
			GROUP BY od.menuItemID");
		$sth -> execute(array(':dateYMD'=>$dateYMD));	
		$reportdetails = $sth -> fetchAll();
		
		$res .= '<br /><br />';
		foreach ($reportdetails as $reportdetail) {
	    	$res .= '<div>( '.$reportdetail->itemCount.' ) '.$reportdetail->itemName.'</div>';
		}
		$res .= '<br /><br /><br /><br /><br />';
		
		if ($forHardcopy) {
			$res .= '<p style="width: 500px;border-top:1px solid #ccc;text-align:center;margin: 0 auto;padding: 8px 0;"></p>';
			$res .= '<div><a href="#" onclick="window.print()">Print this window</a></div></body></html>';
		}
		return $res;
	}
    /**
     **************************
     */
	public function getLunchOrdersReportByStudentStaff($dateYMD,$forHardcopy) {
		$date = new DateTime($dateYMD);
		$res = '';
		
		if ($forHardcopy) {
			$res .= '<!DOCTYPE html><head><style>h1,h2,h3,h4,h5,h6{margin: 5px 0;}</style></head><body style="text-align:center;width:980px;margin: 20px auto;" onload="window.print();">';
			$res .= '<img style="width:142px;height:40px;" src="'.URL.'public/img/rpthdr.png"><br /><br />';
		} else {
			$res .= '<div class="printtoscreen admreport">';
			$res .= '<a data-href="'.URL.'admreports/printadmreport/2/'.$date->format('Y-m-d').'" href="#" class="btn btn-primary btn-print">Print</a>';
			$res .= '<form class="new-tab-opener" method="get" target="_blank"></form>';	
		}
		
		$res .= '<h2>Lunch Orders by Student/Staff</h2>';
		$res .= '<h6>for</h6>';
		$res .= '<h4>'.$date->format('l, F jS, Y').'</h4>';
		
		$sth = $this -> db -> prepare("SELECT providerName,providerClass
			FROM los_lunchdates ld
			INNER JOIN los_providers lp ON lp.id=ld.providerID
			WHERE provideDate = :dateYMD");	
		$sth -> execute(array(':dateYMD' => $dateYMD));
		$provider = $sth -> fetch();
		$res .= '<br />';
		if ($forHardcopy)
			$res .= '<h3>'.$provider->providerName.'</h3>';
		else
			$res .= '<div class="provimg '.$provider->providerClass.'"></div>';
		$res .= '<br />';
			
		$sth = $this -> db -> prepare("SELECT u.firstName,u.lastName,o.shortDesc,t.firstName AS teacherFirstName,t.lastName AS teacherLastName
			FROM los_orders o
			INNER JOIN los_users u ON o.userID=u.id
			INNER JOIN los_gradelevels gl ON gl.id=u.gradeID
			INNER JOIN los_users t ON u.teacherID=t.id
			WHERE orderDate=:dateYMD
			AND statusCode < 2 
			AND t.firstname != '".'(unassigned)'."'
			ORDER BY reportOrder,u.lastName");	
		$sth -> execute(array(':dateYMD' => $dateYMD));
		$rows = $sth -> fetchAll();
		
		$curTeacherName='';	
		foreach ($rows as $row) {	
			if ($curTeacherName != $row->teacherFirstName.' '.$row->teacherLastName) {
				$res .= '<br /><div style="text-decoration:underline;">'.$row->teacherFirstName.' '.$row->teacherLastName.'</div>';
				$curTeacherName = $row->teacherFirstName.' '.$row->teacherLastName;
			}
			$res .= '[ ] '.$row->firstName.' '.$row->lastName.' &middot; '.$row->shortDesc.'<br />';
		}

		$sth = $this -> db -> prepare("SELECT u.firstName,u.lastName,o.shortDesc,t.firstName AS teacherFirstName
			FROM los_orders o
			INNER JOIN los_users u ON o.userID=u.id
			INNER JOIN los_gradelevels gl ON gl.id=u.gradeID
			INNER JOIN los_users t ON u.teacherID=t.id
			WHERE orderDate=:dateYMD
			AND statusCode < 2 
			AND t.firstname = '".'(unassigned)'."'
			ORDER BY reportOrder,u.lastName");	
		$sth -> execute(array(':dateYMD' => $dateYMD));
		$rows = $sth -> fetchAll();
		
		$first = true;
		foreach ($rows as $row) {
			if ($first) {
				$res .= '<br /><div style="text-decoration:underline;">Staff</div>';
				$first = false;
			}
			$res .= '[ ] '.$row->firstName.' '.$row->lastName.' &middot; '.$row->shortDesc.'<br />';
		}

		$res .= '<br /><br />';
		
		if ($forHardcopy) {
			$res .= '<p style="width: 500px;border-top:1px solid #ccc;text-align:center;margin: 0 auto;padding: 8px 0;"></p>';
			$res .= '<div><a href="#" onclick="window.print()">Print this window</a></div></body></html>';
		}
		return $res;
	}
    /**
     **************************
     */
	public function getAccountBalanceReport($forHardcopy) {
		$date = new DateTime();
		$res = '';
		
		if ($forHardcopy) {
			$res .= '<!DOCTYPE html><head><style>h1,h2,h3,h4,h5,h6{margin: 5px 0;}</style></head><body style="width:500px;text-align:center;margin: 20px auto;" onload="window.print();">';
			$res .= '<img style="width:142px;height:40px;" src="'.URL.'public/img/rpthdr.png"><br /><br />';
		} else {
			$res .= '<div class="printtoscreen admreport">';
			$res .= '<a data-href="'.URL.'admreports/printadmreport/3/'.Session::get('local_tzo').'" href="#" class="btn btn-primary btn-print">Print</a>';
			$res .= '<form class="new-tab-opener" method="get" target="_blank"></form>';	
		}
		
		$gmdate = gmdate("l, F d, Y g:i:s A", time()-(Session::get('local_tzo')*60*60));

		$res .= '<h2>Lunch Account Balances</h2>';
		$res .= '<div><i>as of</i></div>';
		$res .= '<h4>'.$gmdate.'</h4>';
		
		$sth = $this->db-> prepare("SELECT account_name,confirmed_credits,confirmed_debits,total_debits FROM los_accounts WHERE total_debits > 0 ORDER BY account_name");
		$sth->execute();
		$accounts = $sth->fetchAll();
		
		$res .='<br />';
		$res .='<table class="table table-condensed table-striped table-bordered table-header" style="background-color:#fff;width: 500px;text-align:right;margin: 0 auto;">';
		
		$res .='<thead><tr><th style="text-align:left;">Account</th><th>Debits</th><th>Credits</th><th>Balance</th></tr></thead>';
		foreach ($accounts as $account) {
			$res .='<tr>';
			$res .='<td style="text-align:left;">'.$account->account_name.'</td>';
			
			//$res .='<td>'.$account->confirmedDebits.'</td>';
			$res .='<td>$'.number_format($account->total_debits/100, 2, '.', '').'</td>';
			$res .='<td>$'.number_format($account->confirmed_credits/100, 2, '.', '').'</td>';
			$bal = ($account->total_debits-$account->confirmed_credits)/100;
			//if ($bal > 0)
				//$res .='<td>-$'.number_format(abs($bal), 2, '.', '').'</td>';
			//else
				$res .='<td>$'.number_format($bal, 2, '.', '').'</td>';
			$res .='</tr>';
		}

		$res .='</table>';
		
		if ($forHardcopy) {
			$res .= '<br /><div><a href="#" onclick="window.print()">Print this window</a></div></body></html>';
		}
		return $res;
	}
    /**
     **************************
     */
	public function getAccountDetailsReport($account_id,$forHardcopy) {
		
		$sth = $this->db-> prepare("SELECT account_name,confirmed_credits,confirmed_debits,total_debits FROM los_accounts WHERE id=:account_id");
		$sth -> execute(array(':account_id' => $account_id));
		$account = $sth->fetch();
		
		$sth = $this->db-> prepare("SELECT payMethod,creditAmt,creditDate,creditDesc
			FROM los_payments 
			WHERE account_id=:account_id
			AND deleted=0
			ORDER BY creditDate");	
		$sth -> execute(array(':account_id' => $account_id));
		$payments = $sth->fetchAll();
		
		$sth = $this->db-> prepare("SELECT firstName,lastName,shortDesc,orderDate,totalPrice,statusCode
			FROM los_orders o
			INNER JOIN los_users u ON o.userID=u.id
			WHERE o.account_id=:account_id
			ORDER BY orderDate,lastName,firstName");	
		$sth -> execute(array(':account_id' => $account_id));
		$orders = $sth->fetchAll();
		
		$res = '';
		
		if ($forHardcopy) {
			$res .= '<!DOCTYPE html><head><style>h1,h2,h3,h4,h5,h6{margin: 5px 0;}table{width:100%;}</style></head><body style="width:600px;text-align:center;margin: 20px auto;" onload="window.print();">';
			$res .= '<img style="width:142px;height:40px;" src="'.URL.'public/img/rpthdr.png"><br /><br />';
		} else {
			$res .= '<div class="printtoscreen admreport">';
			$res .= '<a data-href="'.URL.'admreports/printadmreport/4/'.$account_id.'" href="#" class="btn btn-primary btn-print">Print</a>';
			$res .= '<form class="new-tab-opener" method="get" target="_blank"></form>';	
		}
		
		$res .= '<h2>Lunch Account Details</h2>';
		$res .= '<h3>'.$account->account_name.'</h3>';
		$res .= '<h6>as of '.gmdate("l, F d, Y g:i:s A", time()-(Session::get('local_tzo')*60*60)).'</h6>';
		
		$res .= '<br /><h4>Summary</h4>';
		if ($forHardcopy)
			$res .= '<hr />';
		$res .='<table class="table table-condensed table-striped table-bordered table-header" style="background-color:#fff;margin: 0 auto;">';
		$res .='<thead><tr><th>Credits</th><th>Debits-To-Date</th><th>Total Debits</th><th>Balance</th></tr></thead>';
		$res .='<tr>';
		$res .='<td>$'.number_format($account->confirmed_credits/100, 2, '.', '').'</td>';
		$res .='<td>$'.number_format($account->confirmed_debits/100, 2, '.', '').'</td>';
		$res .='<td>$'.number_format($account->total_debits/100, 2, '.', '').'</td>';
		$bal = ($account->total_debits-$account->confirmed_credits)/100;
		//if ($bal > 0)
			//$res .='<td>-$'.number_format(abs($bal), 2, '.', '').'</td>';
		//else
			$res .='<td>$'.number_format($bal, 2, '.', '').'</td>';
		$res .='</tr>';
		$res .='</table>';
		
		$res .= '<br /><h4>Payments</h4>';
		if ($forHardcopy)
			$res .= '<hr />';
		$res .='<table class="table table-condensed table-striped table-bordered table-header" style="background-color:#fff;margin: 0 auto;">';
		$res .='<thead><tr><th>Type</th><th>Description</th><th>Amount</th><th>Received On</th></tr></thead>';
		
		foreach ($payments as $payment) {
			$res .='<tr>';
				switch ($payment->payMethod) {
					case PAY_METHOD_CASH: $res .='<td>Cash</td>';break;
					case PAY_METHOD_CHECK: $res .='<td>Check</td>'; break;
					case PAY_METHOD_PAYPAL: $res .='<td>PayPal</td>';break;
					case PAY_METHOD_ADJ: $res .='<td>Adjustment</td>';break;		
				}
				$res .='<td>'.$payment->creditDesc.'</td>';
				$res .='<td>$'.number_format($payment->creditAmt/100, 2, '.', '').'</td>';
				$res .='<td>'.date("Y-m-d",strtotime($payment->creditDate)).'</td>';
			$res .='</tr>';
		}
		$res .='</table>';
		
		$res .= '<br /><h4>Orders</h4>';
		if ($forHardcopy)
			$res .= '<hr />';
		$res .='<table class="table table-condensed table-striped table-bordered table-header" style="background-color:#fff;margin: 0 auto;">';
		$res .='<thead><tr><th>Name</th><th>Order</th><th>Date</th><th>Price</th><th>Status</th></tr></thead>';
		foreach ($orders as $order) {
			$res .='<tr>';
			$res .='<td>'.$order->lastName.', '.$order->firstName.'</td>';
			$res .='<td>'.$order->shortDesc.'</td>';
			$res .='<td>'.$order->orderDate.'</td>';
			$res .='<td>$'.number_format($order->totalPrice/100, 2, '.', '').'</td>';
			if ($order->statusCode == 1)
				$res .='<td>Ordered</td>';
			else
				$res .='<td>Scheduled</td>';
			$res .='</tr>';
		}
		$res .='</table>';
		
		if ($forHardcopy) {
			$res .= '<br /><div><a href="#" onclick="window.print()">Print this window</a></div></body></html>';
		}
		return $res;
	}
}