<?php

class CommonModel {
    public function __construct(Database $db) {
        $this->db = $db;
    }
    /**
     **************************
     */
    public function updateDebits($account_id) {
        $sth = $this->db->prepare("UPDATE los_accounts a
			SET total_debits=
				(SELECT COALESCE(SUM(totalPrice),0)
  					FROM los_orders o
  					WHERE account_id=:account_id
  					AND o.statusCode < 2)
  			WHERE a.id=:account_id");
        $sth->execute(array(':account_id'=>$account_id));

        $sth = $this->db->prepare("UPDATE los_accounts a
			SET confirmed_debits=
				(SELECT COALESCE(SUM(totalPrice),0)
  					FROM los_orders o
  					WHERE account_id=:account_id
  					AND o.statusCode = 1)
  			WHERE a.id=:account_id");
        $sth->execute(array(':account_id'=>$account_id));
    }
    /**
     **************************
     */
    public function updateCredits($account_id) {
        $sth = $this->db->prepare("UPDATE los_accounts a
			SET confirmed_credits=
				(SELECT COALESCE(SUM(creditAmt),0)
				FROM los_payments p
				WHERE p.account_id=:account_id
				AND deleted=0)
			WHERE a.id=:account_id");
        $sth->execute(array(':account_id'=>$account_id));
    }
    /**
     **************************
     */
    public function updateAllAccountsCreditsAndDebits() {
        $sth = $this->db->prepare("UPDATE los_accounts a
			SET confirmed_credits=
				(SELECT COALESCE(SUM(creditAmt),0)
				FROM los_payments p
				WHERE a.id = p.account_id
				AND deleted=0)");
        $sth->execute();

        $sth = $this->db->prepare("UPDATE los_accounts a
			SET total_debits=
				(SELECT COALESCE(SUM(totalPrice),0)
  				FROM los_orders o
  				WHERE a.id = o.account_id
  				AND o.statusCode < 2)");
        $sth->execute();

        $sth = $this->db->prepare("UPDATE los_accounts a
			SET confirmed_debits=
				(SELECT COALESCE(SUM(totalPrice),0)
  				FROM los_orders o
  				WHERE a.id = o.account_id
  				AND o.statusCode = 1)");
        $sth->execute();
    }
}