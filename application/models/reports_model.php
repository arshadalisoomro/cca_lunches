<?php

class ReportsModel {
    public function __construct(Database $db) {
        $this->db = $db;
    }
    /**
     **************************
     */
	public function getUserLunchReport($forHardcopy) {
		$res = '';
		if ($forHardcopy) {
			$res .= '<div style="text-align:center;width:980px;margin: 0 auto;">';
			$res .= '<img style="width:142px;height:40px;" src="'.URL.'public/img/rpthdr.png">';
			
		} else {
			$res .= '<div class="printtoscreen">';
			$res .= '<a data-href="'.URL.'reports/printlunchreport" href="#" class="btn btn-primary btn-print">Print</a>';
			$res .= '<form class="new-tab-opener" method="get" target="_blank"></form>';	
		}
		$res .= '<h2>Upcoming Lunch Orders</h2>';
		
		$sth = $this -> db -> prepare("SELECT account_name FROM los_accounts WHERE id=:account_id");
		$sth -> execute(array(':account_id' => Session::get('account_id')));	
		$account = $sth -> fetch();
		
		$i = strpos($account->account_name, ",");
		if ($i > 0)
			$res .= "<h3 style='margin: 0;'>The ".substr($account->account_name, 0, $i)." Family</h3><br />";
		else
			$res .= "<h3 style='margin: 0;'>The ".$account->account_name." Family</h3><br />";
		
		$res .= '<div><i>as of '.date('l, F jS, Y h:i:s A').'</i></div>';
		$res .= '<br>';
		$res .= '<br>';
		$res .= '<div style="line-height: 22px;">';
		$sth = $this -> db -> prepare("SELECT DATE_FORMAT(orderDate,'%W, %M %D, %Y') AS lunchDate,shortDesc,firstName,lastName,statusCode 
			FROM los_orders o 
			INNER JOIN los_users u ON o.userID=u.ID
			WHERE o.account_id=:account_id
			AND orderDate >= CURDATE()
			ORDER BY orderDate,lastName,firstName");
		$sth -> execute(array(':account_id' => Session::get('account_id')));	
		$orders = $sth -> fetchAll();
		
		$lastReportDate = '';
		$detailType = 1;
		foreach ($orders as $order) {
			$lunchDate = $order->lunchDate;
			$desc = $order->shortDesc;
			$firstName = $order->firstName;
			$lastName = $order->lastName;
			$statusCode = $order->statusCode;
			if ($lastReportDate != $lunchDate) {
				if ($lastReportDate != '') {
					$res .= "</p>";
				}
				$res .= "<p><u><span style='color:#0000FF'>" . $lunchDate . "</span></u><br />";
				$lastReportDate = $lunchDate;
			}

			if (isset($firstName)) {
				$res .= "<span style='color:#990000;'>" . $firstName . " " . $lastName . "</span> - <span style='color:#111111'>" . $desc . "</span>";
				if ($statusCode == 0) {
					$res .= "<i> [scheduled]</i>";
				}
			} else {
				if (isset($desc)) {
					if ($detailType == 4) {
						$res .= " " . $desc;
					} else {
						$res .= " - " . $desc;
					}
				}
			}
			$res .= "<br />";
		}
		$res .= '</div>';
		$res .= '<br>';
		$res .= '<p style="width: 66%;border-top:1px solid #ccc;text-align:center;margin: 0 auto;padding: 8px 0;"></p>';
		$res .= '<em>Note: [scheduled] orders can be changed or canceled.</em></p>';
		if ($forHardcopy)
			$res .= '<div><a href="#" onclick="window.print()">Print this window</a></div>';
		$res .= '</div>';
		$res .= '<br>';
		
		return $res;
	}
}