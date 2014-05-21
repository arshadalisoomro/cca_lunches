<?php

class AccMaintModel {
    public function __construct(Database $db) {
        $this->db = $db;
    }
    /**
     **************************
     */
	public function getTeachers() {
		$sth = $this -> db -> prepare("SELECT u.id,firstName,lastName,gradeDesc
			FROM los_users u
			INNER JOIN los_gradelevels gl ON gl.id=u.gradeID 
			WHERE u.type=2 
			ORDER BY lastName,firstName,gradeDesc");
		$sth->execute();
		return $sth->fetchAll();
	}
    /**
     **************************
     */
	public function getAccountsTableBody($selected_id) {	
		$sth = $this->db->prepare("SELECT u.id AS userID,firstName,lastName,allowedToOrder,teacherID,gradeID,type,
			a.id AS account_id,account_name,user_name,user_email,user_active,user_account_type,no_new_orders
			FROM los_users u
			INNER JOIN los_accounts a ON u.account_id=a.id
			WHERE u.type > 0
			ORDER BY account_name,lastName,firstName");	
		$sth -> execute();
		$usersaccounts = $sth -> fetchAll();
		
		if (count($usersaccounts) == 0)
			return '';
		
		$sth = $this->db->prepare("SELECT count(id) AS acount,account_id FROM los_orders GROUP BY account_id");
		$sth -> execute();
		$a_countrows = $sth -> fetchAll();
		$sth = $this->db->prepare("SELECT count(id) AS ucount,userID FROM los_orders GROUP BY userID");	
		$sth -> execute();
		$u_countrows = $sth -> fetchAll();
		
		$sth = $this->db->prepare("SELECT u.id AS userID,firstName,lastName,allowedToOrder,teacherID,gradeID,type,
			a.id AS account_id,account_name,user_name,user_email,user_active,user_account_type,no_new_orders
			FROM los_users u
			INNER JOIN los_accounts a ON u.account_id=a.id
			WHERE u.type > 0
			ORDER BY account_name,lastName,firstName");	
		$sth -> execute();
		$usersaccounts = $sth -> fetchAll();
		
		$lastAccount = '';
		$firstTime = true;
		$res = '';
		foreach ($usersaccounts as $useraccount) {
			if ($useraccount->account_name != $lastAccount) {
				if ($firstTime) {
					$firstTime = false;
				} else 
					$res .= '<div><a href="#ADD">[ Add ]</a></div></td></tr>';
				
				$firstUserRow = true;
				$acount = 0;
				foreach ($a_countrows as $arow) {
					if ($arow->account_id == $useraccount->account_id) {
						$acount = $arow->acount;
						break;
					}
				}
				$data = 'data-aid="'.$useraccount->account_id.'"';
				$data .= ' data-uname="'.$useraccount->user_name.'"';
				$data .= ' data-uactive="'.$useraccount->user_active.'"';
				$data .= ' data-atype="'.$useraccount->user_account_type.'"';
				$data .= ' data-nnorders="'.$useraccount->no_new_orders.'"';
				$data .= ' data-acount="'.$acount.'"';
				
				if ($useraccount->account_id == $selected_id)
					$res .= '<tr id="SELECTED"><td><a '.$data.' href="#">'.$useraccount->account_name.'</a></td><td>'.$useraccount->user_email.'</td><td>';
				else
					$res .= '<tr><td><a '.$data.' href="#">'.$useraccount->account_name.'</a></td><td>'.$useraccount->user_email.'</td><td>';
			}
			
			if ($firstUserRow) {
				$ucount = 999; //can't delete first user
			} else {
				$ucount = 0;
				foreach ($u_countrows as $urow) {
					if ($urow->userID == $useraccount->userID) {
						$ucount = $urow->ucount;
						break;
					}
				}
			}
			$firstUserRow = false;
			$data = 'data-uid="'.$useraccount->userID.'"';
			$data .= ' data-ato="'.$useraccount->allowedToOrder.'"';
			$data .= ' data-tid="'.$useraccount->teacherID.'"';
			$data .= ' data-utype="'.$useraccount->type.'"';
			$data .= ' data-ucount="'.$ucount.'"';
			$res .= '<div><a '.$data.' href="#">'.$useraccount->lastName.', '.$useraccount->firstName.'</a></div>';
			$lastAccount = $useraccount->account_name;
		}
		$res .= '<div><a href="#ADD">[ Add ]</a></div></td></tr>';
		return $res;
	}
    /**
     **************************
     */
	public function deleteAccount($account_id) {
		$sth = $this->db->prepare("DELETE FROM los_users WHERE account_id=:account_id");
		$sth -> execute(array(':account_id' => $account_id));
		$sth = $this->db->prepare("DELETE FROM los_accounts WHERE id=:account_id");
		$sth -> execute(array(':account_id' => $account_id));
	}
    /**
     **************************
     */
	public function deleteUser($userID) {
		$sth = $this->db->prepare("DELETE FROM los_users WHERE id=:userID");
		$sth -> execute(array(':userID' => $userID));
	}
    /**
     **************************
     */
	public function newUser($account_id,$fname,$lname,$utype,$tid) {
		$sth = $this->db->prepare("SELECT id FROM los_users WHERE account_id=:account_id AND teacherID=:tid AND firstName=:fname");
		$sth -> execute(array(':account_id' => $account_id,':tid' => $tid,':fname' => $fname));
		$existing = $sth -> fetchAll();
		if ($existing) {
			return "Error: The user already exists in the system.";
		}
		
		$gid = 1;
		if ($tid > 1) {
			$sth = $this->db->prepare("SELECT gradeID FROM los_users WHERE id=:tid");
			$sth -> execute(array(':tid' => $tid));
			$grade = $sth -> fetch();
			$gid = $grade->gradeID;
		}
		$sth = $this -> db -> prepare("INSERT INTO los_users (account_id,firstName,lastName,teacherID,gradeID,type) 
			VALUES(:account_id, :fname, :lname, :tid, :gid, :utype)");
		$sth -> execute(array(
			':account_id'=>$account_id,
			':fname'=>$fname,
			':lname'=>$lname,
			':tid'=>$tid,
			':gid'=>$gid,
			':utype'=>$utype));
	}
    /**
     **************************
     */
	public function updateUser($account_id,$user_id,$fname,$lname,$utype,$tid,$ato) {
		$sth = $this->db->prepare("SELECT id FROM los_users WHERE account_id=:account_id AND teacherID=:tid AND firstName=:fname AND id != :user_id");
		$sth -> execute(array(':account_id' => $account_id,':tid' => $tid,':fname' => $fname,':user_id' => $user_id));
		$existing = $sth -> fetchAll();
		if ($existing) {
			return "Error: The user already exists in the system.";
		}
		
		$gid = 1;
		if ($utype == 1) {
			$sth = $this->db->prepare("SELECT gradeID FROM los_users WHERE id=:tid");
			$sth -> execute(array(':tid' => $tid));
			$grade = $sth -> fetch();
			$gid = $grade->gradeID;
		} else {
			$tid = 1;
		}
		$sth = $this->db->prepare("UPDATE los_users
			SET firstName=:fname,lastName=:lname,teacherID=:tid,gradeID=:gid,type=:utype,allowedToOrder=:ato
  			WHERE id=:user_id");
		$sth->execute(array(
			':user_id'=>$user_id,
			':fname'=>$fname,
			':lname'=>$lname,
			':tid'=>$tid,
			':gid'=>$gid,
			':utype'=>$utype,
			':ato'=>$ato));
	}
    /**
     **************************
     */
	public function newAccount($account_name,$user_name,$user_email,&$account_id) {
		$sth = $this->db->prepare("SELECT account_name,user_name,user_email 
			FROM los_accounts 
			WHERE account_name = :account_name OR user_name = :user_name OR user_email = :user_email");
		$sth->execute(array(':account_name' => $account_name,':user_name' => $user_name,':user_email' => $user_email));
		$existing = $sth -> fetchAll();
		foreach ($existing as $row) {
			if ($row->account_name == $account_name)
				return "Error: Account Name already exists in the system.";
			else if ($row->user_name == $user_name)
				return "Error: Login Name already exists in the system.";
			else if ($row->user_email == $user_email)
				return "Error: Email address already exists in the system.";
			break;
		}

		$user_password_hash = password_hash($user_email, PASSWORD_DEFAULT, array('cost' => HASH_COST_FACTOR));
        $user_activation_hash = sha1(uniqid(mt_rand(), true));
        $user_creation_timestamp = time();
        $sql = "INSERT INTO los_accounts (user_name, account_name, user_password_hash, user_email, user_creation_timestamp, user_activation_hash, user_provider_type)
                    VALUES (:user_name, :account_name, :user_password_hash, :user_email, :user_creation_timestamp, :user_activation_hash, :user_provider_type)";
        $query = $this->db->prepare($sql);
        $query->execute(array(':user_name' => $user_name,
            ':account_name' => $account_name,
            ':user_password_hash' => $user_password_hash,
            ':user_email' => $user_email,
            ':user_creation_timestamp' => $user_creation_timestamp,
            ':user_activation_hash' => $user_activation_hash,
            ':user_provider_type' => 'DEFAULT'));
        $count =  $query->rowCount();

        $account_id = 0;
		if ($count == 1) {
			$account_id = $this->db->lastInsertId();
			$lastname = '(Last Name)';
			$pos = trim(strpos($account_name, ','));
			if ($pos > 0) {
				$lastname=substr($account_name,0,$pos);
			}
			$this->newUser($account_id,'(FirstName)',$lastname,1,1);
		}
	}
    /**
     **************************
     */
	public function updateAccount($account_id,$accountname,$username,$email,$atype,$uactive,$nnorders) {
		$sth = $this->db->prepare("SELECT account_name,user_name,user_email 
			FROM los_accounts 
			WHERE (account_name = :accountname OR user_name = :username OR user_email = :email)
			AND id != :account_id");
		$sth -> execute(array(':accountname' => $accountname,':username' => $username,':email' => $email,':account_id' => $account_id));
		$existing = $sth -> fetchAll();
		foreach ($existing as $row) {
			if ($row->account_name == $accountname)
				return "Error: Account Name already exists in the system.";
			else if ($row->user_name == $username)
				return "Error: Login Name already exists in the system.";
			else if ($row->user_email == $email)
				return "Error: Email address already exists in the system.";
			break;
		}
		$sth = $this->db->prepare("UPDATE los_accounts 
			SET user_name=:username,account_name=:accountname,user_email=:email,user_active=:uactive,user_account_type=:atype,no_new_orders=:nnorders
  			WHERE id=:account_id");
		$sth->execute(array(
			':account_id'=>$account_id,
			':username'=>$username,
			':accountname'=>$accountname,
			':email'=>$email,
			':uactive'=>$uactive,
			':atype'=>$atype,
			':nnorders'=>$nnorders));
	}
}