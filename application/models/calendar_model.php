<?php

class CalendarModel {
    public function __construct(Database $db) {
        $this->db = $db;
    }
    /**
     **************************
     */
	public function getAmountDue() {
		$sth = $this->db-> prepare("SELECT total_debits,confirmed_credits FROM los_accounts WHERE id=:account_id");
		$sth->execute(array(':account_id' => Session::get('account_id')));
		$account = $sth->fetch();
		$amountDue = intval($account->total_debits) - intval($account->confirmed_credits);
		if ($amountDue < 0 )
			$amountDue = 0;
		return '$'.number_format($amountDue/100,2);
	}
    /**
     **************************
     */
	public function getAccountNames() {
		if (Session::get('user_account_type') == ACCOUNT_TYPE_ADMIN) {
			$sth = $this->db-> prepare("SELECT id,account_name 
				FROM los_accounts
				WHERE user_active=1 
				AND id IN (SELECT DISTINCT account_id FROM los_users WHERE allowedToOrder=1)
				ORDER BY account_name");
			$sth->execute();
		} else {
			$sth = $this->db->prepare("SELECT id,account_name FROM los_accounts WHERE id=:account_id");
			$sth->execute(array(':account_id' => Session::get('account_id')));	
		}
		return $sth->fetchAll();
	}
    /**
     **************************
     */
	public function getOrderDetails($userID,$dateYMD) {
		$sth = $this->db->prepare("SELECT menuItemID FROM los_orderdetails WHERE orderID=(SELECT id FROM los_orders WHERE orderDate=:dateYMD AND userID=:userID)");
		$sth->execute(array(':dateYMD' => $dateYMD,':userID' => $userID));
		return json_encode($sth->fetchAll());
	}
    /**
     **************************
    */
	private function getOrderCellHTML($lunchdates,$nles,$user,$orders,$provID,
		$todayYMD,$todayTimestamp,$curDateYMD) {
		
		$curTimestamp = strtotime($curDateYMD);
		$haveLunchDate = false;
		$allowOrders = false;
		$ordersPlaced = false;
		$addltxt = null;
		$extcare = null;
		$nleReason = null;
		$nleDesc = null;
		$orderLunchText = null;
		$orderPrice = null;
		$orderID = 0;
				
		foreach ($lunchdates as $lunchdate) { //loop thru all lunch dates; must be defined for anything to show on schedule
			if (date("Y-m-d",strtotime($lunchdate->provideDate)) == $curDateYMD) {
				$haveLunchDate = true;
				$addltxt = trim($lunchdate->additionalText);
				$extcare = trim($lunchdate->extendedCareText);
				$allowOrders = $lunchdate->allowOrders > 0;
				$ordersPlaced = !empty($lunchdate->ordersPlaced);
				break;
			}
		}
				
		if (!$haveLunchDate) {
			return '<td class="spacer"></td>';
		}
				
		foreach ($nles as $nle) { //check for exception
			if (date("Y-m-d",strtotime($nle->exceptionDate)) == $curDateYMD) {
				if (($user->teacherID == $nle->teacherID) || ($user->gradeID == $nle->gradeID)) {
					$nleReason = trim($nle->reason);
					$nleDesc = trim($nle->description);
					break;
				}
			}
		}
				
		if (empty($nleReason)) { //check for order
			foreach ($orders as $order) {
				if ((date("Y-m-d",strtotime($order->orderDate)) == $curDateYMD) &&
					($user->id == $order->userID)) {		
						$orderLunchText = trim($order->shortDesc);
						$orderPrice = '$'.number_format($order->totalPrice/100,2);
						$orderID = $order->orderID;
					break;
				}
			}
		}
		
		$body = '';
		$editable = false;
		if (!empty($nleReason)) {	
			$body .= '<div class="nlereason">'.$nleReason.'</div>';
			if (!empty($nleDesc))	
				$body .= '<div class="nledesc">'.$nleDesc.'</div>';
		} else if (!empty($orderLunchText)) {
			$lt = '<div class="lunchtext">'.$orderLunchText.'</div>';
			//$lt .= '<div class="price">'.$orderPrice.'</div>';
			$editable = ($curTimestamp > $todayTimestamp && !$ordersPlaced);
			if ($editable)
				$lt = '<div class="clickable">'.$lt.'<div class="glyphicon glyphicon-edit"></div></div>';
			$body .= $lt;
		} else if ($curTimestamp > $todayTimestamp && !$ordersPlaced && $allowOrders) {
			$body .= '<div class="clickable">';
			$body .= '<div class="glyphicon glyphicon-plus-sign"></div>';
			$body .= '<div class="ordertext">Order</div>';
			$body .= '</div>';
			$editable = true;
		} else if ($allowOrders){
			$body .= '<div class="nlo">No Lunch<br />Ordered</div>';
		}
		if (!empty($addltxt)) {	
			$body .= '<div class="addltxt">'.$addltxt.'</div>';
		}
		if (!empty($extcare)) {	
			$body .= '<div class="extcare">'.$extcare.'</div>';
		}
				
		$classes = '';
		$data = '';
		if ($curDateYMD == $todayYMD)
			$classes = 'today';
		if ($curTimestamp > $todayTimestamp) {
			if ($editable) {
				$classes .= ' editable';
				$data = 'data-provid="'.$provID.'" data-dateymd="'.$curDateYMD.'" data-orderid="'.$orderID.'"';
			}
		} else {
			$classes .= ' past';
		}
		
		if (!empty($classes))
			$classes = ' class="'.trim($classes).'"';
			
		return '<td '.$data.$classes.'>'.$body.'</td>';
	}		
	/**
     **************************
    */
	public function getLunchesTableWeek($account_id,$startDateYMD,$endDateYMD) {
		$today = new DateTime();
		$todayYMD = $today->format("Y-m-d");
		$todayTimestamp = strtotime($todayYMD);
		
		$sth = $this->db->prepare("SELECT ld.id AS lunchDateID,lp.id AS providerID,providerName,providerClass,imageName,allowOrders,
		 		provideDate,ordersPlaced,additionalText,extendedCareText,url
			FROM los_lunchdates ld
			INNER JOIN los_providers lp ON lp.id=ld.providerID
			WHERE provideDate >= :startDateYMD 
			AND provideDate <= :endDateYMD
			ORDER BY provideDate");	
		$sth->execute(array(':startDateYMD' => $startDateYMD,':endDateYMD' => $endDateYMD));
		$lunchdates = $sth->fetchAll();
		
		$sth = $this->db->prepare("SELECT id,exceptionDate,reason,description,gradeID,teacherID
			FROM los_nolunchexceptions 
			WHERE exceptionDate >= :startDateYMD
			AND exceptionDate <= :endDateYMD");
		$sth->execute(array(':startDateYMD' => $startDateYMD,':endDateYMD' => $endDateYMD));
		$nles = $sth->fetchAll();
		
		$sth = $this->db->prepare("SELECT id,firstName,lastName,allowedToOrder,teacherID,gradeID,type
		 	FROM los_users lu
		 	WHERE account_id=:account_id
		 	ORDER BY lastName,firstName");
		$sth->execute(array(':account_id' => $account_id));
		$users = $sth->fetchAll();
		
		$sth = $this->db->prepare("SELECT id AS orderID,userID,shortDesc,orderDate,totalPrice,statusCode
			FROM los_orders lo
			WHERE orderDate >= :startDateYMD
			AND orderDate <= :endDateYMD
			AND account_id = :account_id
			ORDER BY orderDate");
		$sth -> execute(array(':startDateYMD' => $startDateYMD,':endDateYMD' => $endDateYMD,':account_id' => $account_id));
		$orders = $sth->fetchAll();
		
		$providers_row = '';
		$lunchdates_row = '';
		$providerIDs = array(0,0,0,0,0);
		$loopDate = new DateTime($startDateYMD);
		for ($i=1;$i<=5;$i++) {
			$loopDate->modify('+1 day');
			$loopDateYMD = $loopDate->format("Y-m-d");
			$providers_row .= '<th>';
			foreach ($lunchdates as $lunchdate) {
				if (date("Y-m-d",strtotime($lunchdate->provideDate)) == $loopDateYMD) {
					$providers_row .= '<a data-provimgid="'.$lunchdate->providerID.'" target="_blank" href="'.$lunchdate->url.'"><img src="'.URL.'public/img/'.$lunchdate->imageName.'" alt="'.$lunchdate->providerClass.'" title="'.$lunchdate->providerName.'"></a>';
					$providerIDs[$i-1] = $lunchdate->providerID;
					break;
				}
			}
			if ($providerIDs[$i-1] == 0)
				$providers_row .= '<img src="'.URL.'public/img/nolunches.png" alt="No Lunches Scheduled" title="No Lunches Scheduled">';
			$providers_row .= '</th>';
			
			if ($loopDateYMD == $todayYMD)
				$lunchdates_row .= '<th class="today">'.$loopDate->format("D M j").'</th>';
			else
				$lunchdates_row .= '<th>'.$loopDate->format("D M j").'</th>';
		}
		$res = '<thead>';
		$res .= '<tr class="providers"><th class="usercol"></th>'.$providers_row.'</tr>';
		$res .= '<tr class="lunchdates"><th class="usercol">Name</th>'.$lunchdates_row.'</tr>';	
		$res .= '</thead>';
		$res .= '<tbody>';
		
		foreach ($users as $user) {
			$res .= '<tr><th colspan="6" class="userrow"><div class="username">'.$user->firstName.' '.$user->lastName.'</div></th></div></tr>';
			$res .= '<tr>';
			$data = 'data-username="'.$user->firstName.' '.$user->lastName.'" data-userid="'.$user->id.'"';
			$res .= '<td class="usercol" '.$data.'>'.$user->firstName.'<br />'.$user->lastName.'</div></td>';
			
			$loopDate = new DateTime($startDateYMD);
			for ($i=1;$i<=5;$i++) {
				$loopDate->modify('+1 day');
				$loopDateYMD = $loopDate->format("Y-m-d");
				$res .= $this->getOrderCellHTML($lunchdates,$nles,$user,$orders,$providerIDs[$i-1],$todayYMD,$todayTimestamp,$loopDateYMD);
			}
			$res .= '</tr>';
		}
		
		if (count($users) == 1) {
			$res .= '<tr><td class="usercol"></td>';
			$res .= '<td class="spacer"></td><td></td><td></td><td></td><td></td>';
			$res .= '</tr>';
		}				
		return $res.'</tbody><tfoot><tr><td colspan="6" class="emptyrow">&nbsp;</td></tr></tfoot>';
	}
	/**
     **************************
     */
	public function getOrderCell($userID,$dateYMD) {
		$today = new DateTime();
		$todayYMD = $today->format("Y-m-d");
		$todayTimestamp = strtotime($todayYMD);
		
		$sth = $this->db->prepare("SELECT ld.id AS lunchDateID,lp.id AS providerID,providerName,providerClass,allowOrders,
		 		provideDate,ordersPlaced,additionalText,extendedCareText,url
			FROM los_lunchdates ld
			INNER JOIN los_providers lp ON lp.id=ld.providerID
			WHERE provideDate = :dateYMD");	
		$sth->execute(array(':dateYMD' => $dateYMD));
		$lunchdates = $sth->fetchAll();
		
		$sth = $this->db->prepare("SELECT id,firstName,lastName,allowedToOrder,teacherID,gradeID,type
		 	FROM los_users lu
		 	WHERE id=:userID");
		$sth->execute(array(':userID' => $userID));
		$user = $sth->fetch();
		
		$sth = $this->db->prepare("SELECT lo.id AS orderID,lo.userID,shortDesc,orderDate,totalPrice,statusCode
			FROM los_orders lo
			WHERE orderDate = :dateYMD
			AND userID = :userID");
		$sth->execute(array(':dateYMD' => $dateYMD,':userID' => $userID));
		$orders = $sth->fetchAll();
			
		$sth = $this->db->prepare("SELECT id,exceptionDate,reason,description,gradeID,teacherID
			FROM los_nolunchexceptions 
			WHERE exceptionDate = :dateYMD");
		$sth->execute(array(':dateYMD' => $dateYMD));
		$nles = $sth->fetchAll();
		
		return $this->getOrderCellHTML($lunchdates,$nles,$user,$orders,$lunchdates[0]->providerID,$todayYMD,$todayTimestamp,$dateYMD);
	}

    /**
     **************************
     */
	public function saveOrder($rb,$chk,$dateYMD,$userID,$orderID,$account_id) {
		//orders placed?  too late to make changes.
		$sth = $this->db->prepare("SELECT ordersPlaced 
			FROM los_lunchdates 
			WHERE provideDate=:dateYMD 
			AND ordersPlaced IS NOT NULL");
		$sth->execute(array(':dateYMD' => $dateYMD));
		if ($sth->rowCount() > 0)
			return true;
		
		//backup existing?
		
		//delete existing order.  
		if ($orderID > 0) {
			$sth = $this->db->prepare("DELETE FROM los_orderdetails WHERE orderID=(SELECT id FROM los_orders WHERE userID=:userID AND orderDate=:dateYMD)");
			$sth->execute(array(':userID' => $userID,':dateYMD' => $dateYMD));
			$sth = $this->db->prepare("DELETE FROM los_orders WHERE userID=:userID AND orderDate=:dateYMD");
			$sth->execute(array(':userID' => $userID,':dateYMD' => $dateYMD));
		}
		
		if ($rb == 0)
			return true;
		
		//build order details
		$shortDesc = '';
		$totalPrice = 0;
		$sql = "SELECT id,itemName,price FROM los_menuitems WHERE id IN (".$rb.",".$chk.")";
		$sth = $this->db->prepare($sql);
		$sth->execute();
		$menuitems = $sth->fetchAll();
		foreach ($menuitems as $menuitem) {
			$shortDesc .= $menuitem->itemName.', ';
			$totalPrice += $menuitem->price;
		}
		$shortDesc = substr($shortDesc,0,strlen($shortDesc)-2); 
		
		//insert new order
		$sth = $this->db->prepare("INSERT INTO los_orders (userID,account_id,entered_by_account_id,shortDesc,orderDate,totalPrice,created) 
			VALUES (:userID,:account_id,:entered_by_account_id,:shortDesc,:dateYMD,:totalPrice,NOW())");
		$sth->execute(array(':userID'=>$userID,':account_id'=>$account_id,':entered_by_account_id'=>Session::get('account_id'),
			':shortDesc'=>$shortDesc,':dateYMD'=>$dateYMD,':totalPrice'=>$totalPrice));
		$newOrderID = $this->db->lastInsertId('id');
		if (!empty($newOrderID) && $newOrderID > 0) {
			foreach ($menuitems as $menuitem) {
				$sth = $this->db->prepare("INSERT INTO los_orderdetails (orderID,menuItemID,qty,account_id,price) VALUES (:orderID,:menuItemID,1,:account_id,:price)");
				$sth->execute(array(':orderID'=>$newOrderID,':menuItemID'=>$menuitem->id,':account_id'=>$account_id,':price'=>$menuitem->price));
			}
		} else {
			return false;
		}
	}
}