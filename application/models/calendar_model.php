<?php

class CalendarModel {
    public function __construct(Database $db) {
        $this->db = $db;
    }
    /**
     **************************
     */
	public function getAmountDue() {
		$sth = $this -> db -> prepare("SELECT total_debits,confirmed_credits FROM los_accounts WHERE id=:account_id");
		$sth -> execute(array(':account_id' => Session::get('account_id')));
		$account = $sth -> fetch();
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
			$sth = $this -> db -> prepare("SELECT id,account_name 
				FROM los_accounts
				WHERE user_active=1 
				AND id IN (SELECT DISTINCT account_id FROM los_users WHERE allowedToOrder=1)
				ORDER BY account_name");
			$sth -> execute();
		} else {
			$sth = $this -> db -> prepare("SELECT id,account_name FROM los_accounts WHERE id=:account_id");
			$sth -> execute(array(':account_id' => Session::get('account_id')));	
		}
		return $sth -> fetchAll();
	}
    /**
     **************************
     */
	public function getOrderDetails($userID,$dateYMD) {
		$sth = $this -> db -> prepare("SELECT menuItemID FROM los_orderdetails WHERE orderID=(SELECT id FROM los_orders WHERE orderDate=:dateYMD AND userID=:userID)");
		$sth -> execute(array(':dateYMD' => $dateYMD,':userID' => $userID));
		return json_encode($sth->fetchAll());
	}
    /**
     **************************
    */
	public function getLunchesTableWeek($account_id,$startDateYMD,$endDateYMD) {
		$today = new DateTime();
		$todayYMD = $today->format("Y-m-d");
		$todayTimestamp = strtotime($todayYMD);
		
		$sth = $this -> db -> prepare("SELECT ld.id AS lunchDateID,lp.id AS providerID,providerName,providerClass,allowOrders,
		 		provideDate,ordersPlaced,additionalText,extendedCareText,url
			FROM los_lunchdates ld
			INNER JOIN los_providers lp ON lp.id=ld.providerID
			WHERE provideDate >= :startDateYMD 
			AND provideDate <= :endDateYMD
			ORDER BY provideDate");	
		$sth -> execute(array(':startDateYMD' => $startDateYMD,':endDateYMD' => $endDateYMD));
		$lunchdates = $sth -> fetchAll();
		
		$sth = $this -> db -> prepare("SELECT id,exceptionDate,reason,description,gradeID,teacherID
			FROM los_nolunchexceptions 
			WHERE exceptionDate >= :startDateYMD
			AND exceptionDate <= :endDateYMD");
		$sth -> execute(array(':startDateYMD' => $startDateYMD,':endDateYMD' => $endDateYMD));
		$nles = $sth -> fetchAll();
		
		$sth = $this -> db -> prepare("SELECT id,firstName,lastName,allowedToOrder,teacherID,gradeID,type
		 	FROM los_users lu
		 	WHERE account_id=:account_id
		 	ORDER BY lastName,firstName");
		$sth -> execute(array(':account_id' => $account_id));
		$users = $sth -> fetchAll();
		
		$sth = $this -> db -> prepare("SELECT id AS orderID,userID,shortDesc,orderDate,totalPrice,statusCode
			FROM los_orders lo
			WHERE orderDate >= :startDateYMD
			AND orderDate <= :endDateYMD
			AND account_id = :account_id
			ORDER BY orderDate");
		$sth -> execute(array(':startDateYMD' => $startDateYMD,':endDateYMD' => $endDateYMD,':account_id' => $account_id));
		$orders = $sth -> fetchAll();
		
		$res = '<div class="lunchslide"><table id="lunchestable" class="table"><thead><tr><th></th>';
		$providerIDs = array(0,0,0,0,0);
		
		$loopDate = new DateTime($startDateYMD);
		for ($i=1;$i<6;$i++) {
			$loopDate->modify('+1 day');
			$loopDateYMD = $loopDate->format("Y-m-d");
			$res .= '<th>';
			foreach ($lunchdates as $lunchdate) {
				if (date("Y-m-d",strtotime($lunchdate->provideDate)) == $loopDateYMD) {
					$res .= '<a target="_blank" href="'.$lunchdate->url.'"><div class="provimg '.$lunchdate->providerClass.'"></div></a>';
					$providerIDs[$i-1] = $lunchdate->providerID;
					break;
				}
			}
			if ($providerIDs[$i-1] == 0)
				$res .= '<div style="height: 66px;">&nbsp;</div>';
			$res .= '</th>';
		}

		$res .= '</tr><tr class="titles"><td class="nameheader">Name</td>';
		
		$loopDate = new DateTime($startDateYMD);
		for ($i=1;$i<6;$i++) {
			$loopDate->modify('+1 day');
			if ($loopDate->format("Y-m-d") == $todayYMD)
				$res .= '<td class="today">'.$loopDate->format("D M j").'</td>';
			else
				$res .= '<td>'.$loopDate->format("D M j").'</td>';
		} 
		$res .= '</tr></thead><tbody>';
		
		foreach ($users as $user) {
			$res .= '<tr><td>'.$user->firstName.'<br />'.$user->lastName.'</td>';
			
			$loopDate = new DateTime($startDateYMD);
			for ($i=1;$i<6;$i++) {
				$loopDate->modify('+1 day');
				$loopDateYMD = $loopDate->format("Y-m-d");
				$loopTimestamp = strtotime($loopDateYMD);
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
					if (date("Y-m-d",strtotime($lunchdate->provideDate)) == $loopDateYMD) {
						$haveLunchDate = true;
						$addltxt = $lunchdate->additionalText;
						$extcare = $lunchdate->extendedCareText;
						$allowOrders = $lunchdate->allowOrders > 0;
						$ordersPlaced = !is_null($lunchdate->ordersPlaced);
						break;
					}
				}
				
				if (!$haveLunchDate) {
					$res .= '<td><div class="spacer">&nbsp;</div><div class="spacer">&nbsp;</div></td>';
					continue;
				}
				
				foreach ($nles as $nle) { //check for exception
					if (date("Y-m-d",strtotime($nle->exceptionDate)) == $loopDateYMD) {
						if (($user->teacherID == $nle->teacherID) || ($user->gradeID == $nle->gradeID)) {
							$nleReason = $nle->reason;
							$nleDesc = $nle->description;
							break;
						}
					}
				}
					
				if (is_null($nleReason)) { //check for order
					foreach ($orders as $order) {
						if ((date("Y-m-d",strtotime($order->orderDate)) == $loopDateYMD) &&
							($user->id == $order->userID)) {		
								$orderLunchText = $order->shortDesc;
								$orderPrice = '$'.number_format($order->totalPrice/100,2);
								$orderID = $order->orderID;
							break;
						}
					}
				}
					
				$res .= '<td';
				if ($loopDateYMD == $todayYMD)
					$res .= ' class="today"'; //never editable
				else if (($loopTimestamp > $todayTimestamp) && (!$ordersPlaced) && is_null($nleReason) && $allowOrders)
					$res .= ' class="editable" data-userid="'.$user->id.'" data-orderid="'.$orderID.'" data-dateymd="'.$loopDateYMD.
						'" data-name="'.$user->firstName.' '.$user->lastName.'" data-provid="'.$providerIDs[$i-1].'"';
				else if ($loopTimestamp > $todayTimestamp)
					$res .= ' class="active"';
				$res .= '>';
				
				$added = false;
				if (!is_null($nleReason)) {	
					$res .= '<div class="nlereason">'.$nleReason.'</div>';
					if (!is_null($nleDesc))	
						$res .= '<div class="nledesc">'.$nleDesc.'</div>';
					$added = true;
				} else if (!is_null($orderLunchText)) {
					$res .= '<div class="lunchtext">'.$orderLunchText.'</div>';
					$res .= '<div class="price">'.$orderPrice.'</div>';
					$added = true;
				} else if (($loopTimestamp > $todayTimestamp) && (!$ordersPlaced) && $allowOrders) {
					$res .= '<div class="addbtn"></div><div class="ordertext">Order</div>';
					$added = true;
				} else if ($allowOrders){
					$res .= '<div class="nlo">No Lunch<br />Ordered</div>';
					$added = true;
				}	
				
				if (!$added)
					$res .= '<div class="spacer">&nbsp;</div>';
				if (!is_null($addltxt))	
					$res .= '<div class="addltxt">'.$addltxt.'</div>';
				if (!is_null($extcare))	
					$res .= '<div class="extcare">'.$extcare.'</div>';
				if (!$added)
					$res .= '<div class="spacer">&nbsp;</div>';
				$res .= '</td>';		
			}
			$res .= '</tr>';
		}
		return $res.'</tbody><tfoot><tr><td></td><td></td><td></td><td></td><td></td><td></td></tr></tfoot></table></div>';
	}
    /**
     **************************
     */
	public function getOrderCell($userID,$dateYMD,&$orderID) {
		$today = new DateTime();
		$todayYMD = $today->format("Y-m-d");
		$todayTimestamp = strtotime($todayYMD);
		
		$sth = $this -> db -> prepare("SELECT ld.id AS lunchDateID,lp.id AS providerID,providerName,providerClass,allowOrders,
		 		provideDate,ordersPlaced,additionalText,extendedCareText,url
			FROM los_lunchdates ld
			INNER JOIN los_providers lp ON lp.id=ld.providerID
			WHERE provideDate = :dateYMD");	
		$sth -> execute(array(':dateYMD' => $dateYMD));
		$lunchdate = $sth -> fetch();
		
		$sth = $this -> db -> prepare("SELECT id,firstName,lastName,allowedToOrder,teacherID,gradeID,type
		 	FROM los_users lu
		 	WHERE id=:userID");
		$sth -> execute(array(':userID' => $userID));
		$user = $sth -> fetch();
		
		$sth = $this -> db -> prepare("SELECT lo.id AS orderID,lo.userID,shortDesc,orderDate,totalPrice,statusCode
			FROM los_orders lo
			WHERE orderDate = :dateYMD
			AND userID = :userID");
		$sth -> execute(array(':dateYMD' => $dateYMD,':userID' => $userID));
		$order = $sth -> fetch();
		
		$sth = $this -> db -> prepare("SELECT id,exceptionDate,reason,description,gradeID,teacherID
			FROM los_nolunchexceptions 
			WHERE exceptionDate = :dateYMD");
		$sth -> execute(array(':dateYMD' => $dateYMD));
		$nles = $sth -> fetchAll();
		
		$res = '';
		$theDate = new DateTime($dateYMD);
		$theDateYMD = $theDate->format("Y-m-d");
		$theTimestamp = strtotime($theDateYMD);
		
		$haveLunchDate = false;
		$allowOrders = false;
		$ordersPlaced = false;
		$addltxt = null;
		$extcare = null;
		$nleReason = null;
		$nleDesc = null;
		$orderLunchText = null;
		$orderPrice = null;
		
		if (date("Y-m-d",strtotime($lunchdate->provideDate)) == $theDateYMD) {
			$haveLunchDate = true;
			$addltxt = $lunchdate->additionalText;
			$extcare = $lunchdate->extendedCareText;
			$allowOrders = $lunchdate->allowOrders > 0;
			$ordersPlaced = !is_null($lunchdate->ordersPlaced);
		}
		foreach ($nles as $nle) {
			if (date("Y-m-d",strtotime($nle->exceptionDate)) == $theDateYMD) {
				if (($user->teacherID == $nle->teacherID) || ($user->gradeID == $nle->gradeID)) {
					$nleReason = $nle->reason;
					$nleDesc = $nle->description;
					break;
				}
			}
		}
		if (is_null($nleReason)) {
			if ($order && $order->shortDesc) {
				$orderLunchText = $order->shortDesc;
				$orderPrice = '$'.number_format($order->totalPrice/100,2);
				$orderID = $order->orderID;
			}
		}
			
		$added = false;
		if (!is_null($nleReason)) {	
			$res .= '<div class="nlereason">'.$nleReason.'</div>';
			if (!is_null($nleDesc))	
				$res .= '<div class="nledesc">'.$nleDesc.'</div>';
			$added = true;
		} else if (!is_null($orderLunchText)) {
			$res .= '<div class="lunchtext">'.$orderLunchText.'</div>';
			$res .= '<div class="price">'.$orderPrice.'</div>';
			$added = true;
		} else if (($theTimestamp > $todayTimestamp) && (!$ordersPlaced) && $allowOrders) {
			$res .= '<div class="addbtn"></div><div class="ordertext">Order</div>';
			$added = true;
		} else if ($allowOrders){
			$res .= '<div class="nlo">No Lunch<br />Ordered</div>';
			$added = true;
		}	
				
		if (!$added)
			$res .= '<div class="spacer">&nbsp;</div>';
		if (!is_null($addltxt))	
			$res .= '<div class="addltxt">'.$addltxt.'</div>';
		if (!is_null($extcare))	
			$res .= '<div class="extcare">'.$extcare.'</div>';
		if (!$added)
			$res .= '<div class="spacer">&nbsp;</div>';
		return $res;
	}

    /**
     **************************
     */
	public function saveOrder($rb,$chk,$dateYMD,$userID,$orderID,$account_id) {
		//orders placed?  too late to make changes.
		$sth = $this -> db -> prepare("SELECT ordersPlaced 
			FROM los_lunchdates 
			WHERE provideDate=:dateYMD 
			AND ordersPlaced IS NOT NULL");
		$sth->execute(array(':dateYMD' => $dateYMD));
		if ($sth->rowCount() > 0 )
			return true;
		
		//backup existing?
		
		//delete existing order.  
		if ($orderID > 0) {
			$sth = $this -> db -> prepare("DELETE FROM los_orderdetails WHERE orderID=(SELECT id FROM los_orders WHERE userID=:userID AND orderDate=:dateYMD)");
			$sth->execute(array(':userID' => $userID,':dateYMD' => $dateYMD));
			$sth = $this -> db -> prepare("DELETE FROM los_orders WHERE userID=:userID AND orderDate=:dateYMD");
			$sth->execute(array(':userID' => $userID,':dateYMD' => $dateYMD));
		}
		
		if ($rb == 0)
			return;
		
		//build order details
		$shortDesc = '';
		$totalPrice = 0;
		$sql = "SELECT id,itemName,price FROM los_menuitems WHERE id IN (".$rb.",".$chk.")";
		$sth = $this->db-> prepare($sql);
		$sth->execute();
		$menuitems = $sth -> fetchAll();
		foreach ($menuitems as $menuitem) {
			$shortDesc .= $menuitem->itemName.', ';
			$totalPrice += $menuitem->price;
		}
		$shortDesc = substr($shortDesc,0,strlen($shortDesc)-2); 
		
		//insert new order
		$sth = $this -> db -> prepare("INSERT INTO los_orders (userID,account_id,entered_by_account_id,shortDesc,orderDate,totalPrice,created) 
			VALUES (:userID,:account_id,:entered_by_account_id,:shortDesc,:dateYMD,:totalPrice,NOW())");
		$sth->execute(array(':userID'=>$userID,':account_id'=>$account_id,':entered_by_account_id'=>Session::get('account_id'),
			':shortDesc'=>$shortDesc,':dateYMD'=>$dateYMD,':totalPrice'=>$totalPrice));
		$newOrderID = $this->db->lastInsertId('id');
		if (!empty($newOrderID) && $newOrderID > 0) {
			foreach ($menuitems as $menuitem) {
				$sth = $this -> db -> prepare("INSERT INTO los_orderdetails (orderID,menuItemID,qty,account_id,price) VALUES (:orderID,:menuItemID,1,:account_id,:price)");
				$sth->execute(array(':orderID'=>$newOrderID,':menuItemID'=>$menuitem->id,':account_id'=>$account_id,':price'=>$menuitem->price));
			}
		} else {
			//show alert!
		}
	}
}