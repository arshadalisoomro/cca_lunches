<?php

class ScheduleModel {
    public function __construct(Database $db) {
        $this->db = $db;
    }
    /**
     **************************
     */
	private function buildCellClass($dayofweek,$dateTS,$todayTS,$dateYMD,$provID,$ordercount) {
		$res = '<td';
		if (($dateTS > $todayTS) && ($dayofweek > 0) && ($dayofweek < 6)) {
			$res .= ' class="enabled" data-dateymd="'.$dateYMD.'" data-provid="'.$provID.'" data-orders="'.$ordercount.'"';
		}
		return $res.'>';
	}
    /**
     **************************
     */
	private function buildCellContents($dayofweek,$dateTS,$todayTS,$oLunchDate,$ordercount,$provID,$nles,$loopDateYMD) {
		$res = '';	
		if ($dayofweek == 0 || $dayofweek == 6) {
			$res = '<div class="spacer"></div>';
			return $res;
		}
		if ($oLunchDate) {
			$enabled = (($dateTS > $todayTS) && (is_null($oLunchDate->ordersPlaced)));
			
			if ($enabled)
				$res .= '<div class="provimg '.$oLunchDate->providerClass.'"></div>';
			else
				$res .= '<div class="provimg disabled '.$oLunchDate->providerClass.'"></div>';
			
			if (!is_null($oLunchDate->additionalText))
				$res .= '<div class="addltxt">'.$oLunchDate->additionalText.'</div>';
			if (!is_null($oLunchDate->extendedCareText))
				$res .= '<div class="extcare">'.$oLunchDate->extendedCareText.'</div>';
			
			foreach ($nles as $nle) {	
				if (date("Y-m-d",strtotime($nle->exceptionDate)) == $loopDateYMD) {
					if ($enabled) {
						$res .= '<div class="nle" data-nleid="'.$nle->nleID.'">';
						$res .= '<a href="#">['.$nle->gradeDesc.': '.$nle->reason.']</a></div>';
					} else
						$res .= $nle->gradeDesc.': '.$nle->reason.'</div><br />';
				}
			}
			if ($enabled && $provID > 2 && $ordercount == 0)
				$res .= '<div class="nleorange" data-nleid="0"><a href="#">[Add Exception]</a></div>';
			if ($ordercount > 0) {
				if (is_null($oLunchDate->ordersPlaced))
					$res .= '<div class="ordercount">'.$ordercount.' Orders Scheduled</div>';
				else
					$res .= '<div class="ordercount">'.$ordercount.' Lunches Ordered</div>';
			}
		} else {
			//if ($dateTS > $todayTS)
				//$res .= '<div class="addtext">[Click to Add]</div>';
		}
		return $res;
	}
    /**
     **************************
     */
	public function getSchedule($startDateYMD) {
		$today = new DateTime();  
		$todayTS = $today->getTimestamp();
		$date = new DateTime($startDateYMD);
		$dateTS = $date->getTimestamp();
		$startMonth = date('n',$dateTS); //1-12
		$dayofweek = date('w',$dateTS); //0-6
		
		if ($dayofweek > 1) //align to Sunday
			$date->modify('-'.$dayofweek.' days');
		else 
			$date->modify('-'.$dayofweek.' day');
		$dateTS = $date->getTimestamp();
		$dateYMD = $date->format("Y-m-d");
		
		$sth = $this->db->prepare("SELECT ld.id AS lunchDateID,lp.id AS providerID,providerName,providerClass,allowOrders,
		 		provideDate,ordersPlaced,additionalText,extendedCareText
			FROM los_lunchdates ld
			INNER JOIN los_providers lp ON lp.id=ld.providerID
			WHERE provideDate >= :dateYMD
			ORDER BY provideDate");	
		$sth -> execute(array(':dateYMD' => $dateYMD)); //could determine an enddate, next month + extra days...worth it?
		$lunchdates = $sth -> fetchAll();
		
		//gradeID,teacherID,reason,description
		$sth = $this->db->prepare("SELECT nle.id as nleID,exceptionDate,reason,gradeDesc
			FROM los_nolunchexceptions nle
			INNER JOIN los_gradelevels gl ON nle.gradeID=gl.id
			WHERE exceptionDate >= :dateYMD
			ORDER BY reportOrder");
		$sth -> execute(array(':dateYMD' => $dateYMD));
		$nles = $sth -> fetchAll();
		
		$sth = $this->db->prepare("SELECT count(id) AS orderCount,orderDate
			FROM los_orders
			WHERE orderDate >= :dateYMD
			GROUP BY orderDate");
		$sth -> execute(array(':dateYMD' => $dateYMD));
		$ordercounts = $sth -> fetchAll();
		
		$res = '<div class="schedslide"><table id="scheduletable" class="table table-bordered table-header table-condensed">';
		$res .= '<tr><th>Sun</th><th>Monday</th><th>Tuesday</th><th>Wednesday</th><th>Thursday</th><th>Friday</th><th>Sat</th></tr>';
		$res .= '<tr>';
		$finished = false;
		$dayno = 0;
		while (!$finished) {
			$loopDateYMD = $date->format("Y-m-d");
			$ld = null;
			$provID = 0;
			foreach ($lunchdates as $lunchdate) {
				if (date("Y-m-d",strtotime($lunchdate->provideDate)) == $loopDateYMD) {
					$ld = $lunchdate;
					$provID = $lunchdate->providerID;
					break;
				}
			}
			$ordercount = 0;
			foreach ($ordercounts as $oc) {
				if (date("Y-m-d",strtotime($oc->orderDate)) == $loopDateYMD) {	
					$ordercount = $oc->orderCount;
					break;
				}
			}
			
			$dayofweek = date('w',$dateTS);
			$res .= $this->buildCellClass($dayofweek,$dateTS,$todayTS,$loopDateYMD,$provID,$ordercount);
			$res .= '<div>'.$date->format('j').'</div>';
			$res .= $this->buildCellContents($dayofweek,$dateTS,$todayTS,$ld,$ordercount,$provID,$nles,$loopDateYMD);//$nlecount,
			$res .= '</td>';
			$dayno += 1;
			if ($dayno == 7) {
				$res .= '</tr>';
				$dayno = 0;
			}
			$date->modify('+1 day');
			$dateTS = $date->getTimestamp();
			$finished = ((date('n',$dateTS) != $startMonth) && ($dayno == 0));
		}
		$res .= '</table></div>';
		return $res;
	}
    /**
     **************************
     */
	public function getProviders() {
		$sth = $this->db->prepare("SELECT id,providerName,allowOrders FROM los_providers");
		$sth -> execute();
		return json_encode($sth->fetchAll());
	}
    /**
     **************************
     */
	public function getMenuItems() {
		$sth = $this->db->prepare("SELECT providerID,itemName,price FROM los_menuitems WHERE active=1");
		$sth -> execute();
		return json_encode($sth->fetchAll());
	}
    /**
     **************************
     */
	public function saveSched($provider,$dateYMD,$addmsg,$ecmsg,$numOrders) {
		//check for orders from db first, don't rely on numOrders?
		if ($numOrders == 0 && $provider == 0) {
			$sql = "DELETE FROM los_nolunchexceptions WHERE exceptionDate=:dateYMD";
			$stmt = $this -> db -> prepare($sql);
			$stmt->bindParam(':dateYMD', $dateYMD, PDO::PARAM_STR);   
			$stmt->execute();
			$sql = "DELETE FROM los_lunchdates WHERE provideDate=:dateYMD";
			$stmt = $this -> db -> prepare($sql);
			$stmt->bindParam(':dateYMD', $dateYMD, PDO::PARAM_STR);   
			$stmt->execute();
		} else if ($numOrders == 0 && $provider > 0) {
			$sql = "DELETE FROM los_lunchdates WHERE provideDate=:dateYMD";
			$stmt =$this -> db -> prepare($sql);
			$stmt->bindParam(':dateYMD', $dateYMD, PDO::PARAM_STR);   
			$stmt->execute();
			$sth = $this -> db -> prepare("INSERT INTO los_lunchdates (providerID,provideDate,additionalText,extendedCareText) 
				VALUES (:provider,:dateYMD,:addmsg,:ecmsg)");
			$sth->execute(array(':provider'=>$provider,':dateYMD'=>$dateYMD,':addmsg'=>$addmsg,':ecmsg'=>$ecmsg));
		} else {
			$sth = $this -> db -> prepare("UPDATE los_lunchdates SET additionalText=:addmsg,extendedCareText=:ecmsg WHERE provideDate=:dateYMD");
			$sth->execute(array(':addmsg' => $addmsg,':ecmsg' => $ecmsg,':dateYMD' => $dateYMD));
		}
	}
    /**
     **************************
     */
	public function getNLEModal($nleid,$orders,$dateYMD) {
		$date = new DateTime($dateYMD);
		$reason = '';
		$desc = '';
		if ($nleid > 0) {
			$sth = $this->db->prepare("SELECT * FROM los_nolunchexceptions WHERE id=:nleid");
			$sth->execute(array(':nleid'=>$nleid));
			$savednle = $sth -> fetch();
			$reason = $savednle->reason;
			$desc = $savednle->description;
			$sth = $this->db->prepare("SELECT gl.id AS gradeID,grade,gradeDesc,lu.firstName,lu.lastName,lu.id AS teacherID
				FROM los_gradelevels gl
				INNER JOIN los_users lu ON lu.id = :teacherID AND gl.id=:gradeID");
			$sth->execute(array(':teacherID'=>$savednle->teacherID,':gradeID'=>$savednle->gradeID));
		} else {
			$sth = $this->db->prepare("SELECT gl.id AS gradeID,grade,gradeDesc,lu.firstName,lu.lastName,lu.id AS teacherID
				FROM los_gradelevels gl
				INNER JOIN los_users lu ON lu.gradeID = gl.id
				WHERE lu.type=2
				AND gradeID NOT IN (SELECT gradeID from los_nolunchexceptions WHERE exceptionDate=:dateYMD)
				AND teacherID NOT IN (SELECT teacherID FROM los_nolunchexceptions WHERE exceptionDate=:dateYMD)
				ORDER BY reportOrder");
			$sth->execute(array(':dateYMD'=>$dateYMD));
		}
		$nles = $sth -> fetchAll();
		
		$res = '<div class="modal-header">';
		$res .= '<div id="selectnle">';
		$res .= 'Lunch Scheduling - No Lunch Exceptions';
		$res .= '</div>';
		$res .= '<div id="nledate">';
		$res .= $date->format('l, F jS, Y');
		$res .= '</div>';
		$res .= '</div>';
		$res .= '<div class="modal-body">';
		$res .= '<form id="formNLE" role="form">';
		$res .= '<div class="form-group">';
		$res .= '<label class="control-label">Teacher (Grade)</label>';
		
		if ($nleid > 0 || $orders > 0)
			$res .= '<select id="gradesteachers" name="teacherid" disabled class="form-control">';
		else 
			$res .= '<select id="gradesteachers" name="teacherid" class="form-control">';
		
		if ($nleid == 0)
			$res .= '<option value="0">[Please Select]</option>';
		
		foreach ($nles as $nle) {
			if ($nleid > 0 && (($nle->teacherID == $savednle->teacherID) || ($nle->gradeID == $savednle->gradeID)))
				$res .= '<option selected value="'.$nle->teacherID.'">'.$nle->lastName.', '.$nle->firstName.' ('.$nle->gradeDesc.')</option>';
			else
				$res .= '<option value="'.$nle->teacherID.'">'.$nle->lastName.', '.$nle->firstName.' ('.$nle->gradeDesc.')</option>';
		}
		$res .= '</select>';
		$res .= '</div><br />';
		$res .= '<div class="form-group">';
		$res .= '<label for="reason">Reason For No Lunch</label>';
		$res .= '<input type="text" class="form-control" id="reason" name="reason" maxlength="30" placeholder="Field Trip, etc." value="'.$reason.'">';
		$res .= '</div><br />';
		$res .= '<div class="form-group">';
		$res .= '<label for="desc">Description</label>';
		$res .= '<input type="text" class="form-control" id="desc" name="desc" maxlength="50" placeholder="Please bring a sack lunch, etc." value="'.$desc.'">';
		$res .= '</div>';
		$res .= '<input type="hidden" name="dateYMD" value="'.$dateYMD.'">';
		$res .= '<input type="hidden" name="orders" value="'.$orders.'">';
		$res .= '<input type="hidden" name="nleid" value="'.$nleid.'">';
		$res .= '</form>';
		$res .= '</div>';
		$res .= '<div class="modal-footer">';
		if ($nleid > 0) {	
			$res .= '<button id="btnNLEDelete" type="button" class="btn btn-danger btn-sm pull-left">Delete</button>';
			$res .= '<button id="btnNLEOK" type="button" class="btn btn-primary btn-sm pull-right">OK</button>';
			$res .= '<button type="button" class="btn btn-default btn-sm pull-right" data-dismiss="modal">Cancel</button>';
		} else {
			$res .= '<button id="btnNLEOK" type="button" class="btn btn-primary btn-sm">OK</button>';
			$res .= '<button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Cancel</button>';
		}
		$res .= '</div>';
		return $res;
	}
    /**
     **************************
     */
	public function saveNLE($nleid,$teacherID,$dateYMD,$reason,$desc) {
		if ($nleid > 0) {
			$sth = $this->db->prepare("UPDATE los_nolunchexceptions SET reason=:reason,description=:desc WHERE id=:nleid");
			$sth->execute(array(':nleid' => $nleid,':reason'=>$reason,':desc'=>$desc));
		} else {
			$sth = $this->db->prepare("SELECT gradeID FROM los_users WHERE id=:teacherID");
			$sth->execute(array(':teacherID'=>$teacherID));
			$grade = $sth -> fetch();
			$sth = $this->db->prepare("INSERT INTO los_nolunchexceptions (exceptionDate,gradeID,teacherID,reason,description)
				VALUES (:dateYMD,:gradeID,:teacherID,:reason,:desc)");
			$sth->execute(array(':dateYMD'=>$dateYMD,':gradeID'=>$grade->gradeID,':teacherID'=>$teacherID,':reason'=>$reason,':desc'=>$desc));
		}
	}
    /**
     **************************
     */
	public function deleteNLE($nleid) {
		$sql = "DELETE FROM los_nolunchexceptions WHERE id=:nleid";
		$stmt =$this->db->prepare($sql);
		$stmt->bindParam(':nleid', $nleid, PDO::PARAM_INT);   
		$stmt->execute();
	}
}