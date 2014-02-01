<?php

class PaymentsModel {
    public function __construct(Database $db) {
        $this->db = $db;
    }
    /**
     **************************
     */
	public function getAccountNames() {
		$sth = $this -> db -> prepare("SELECT DISTINCT account_id AS id,account_name 
			FROM los_orders o
			INNER JOIN los_accounts a ON o.account_id=a.id
			ORDER BY account_name");
		$sth -> execute();
		return $sth -> fetchAll();
	}
    /**
     **************************
     */
	public function getPaymentsBody($account_id) {
		$sth = $this -> db -> prepare("SELECT confirmed_credits,total_debits FROM los_accounts WHERE id=:account_id");
		$sth -> execute(array(':account_id' => $account_id));
		$account = $sth -> fetch();
		$bal = intval($account->confirmed_credits) - intval($account->total_debits);
		
		$sth = $this->db->prepare("SELECT id,payMethod,creditAmt,creditDate,creditDesc
			FROM los_payments
			WHERE account_id=:account_id
			AND deleted = 0
			ORDER BY creditDate");
		$sth -> execute(array(':account_id' => $account_id));
		$payments = $sth -> fetchAll();
		
		$res = '';
		foreach ($payments as $payment) {
			$res .= '<tr>';
				switch ($payment->payMethod) {
					case PAY_METHOD_CASH: $res .= '<td>Cash</td>';break;
					case PAY_METHOD_CHECK: $res .= '<td>Check</td>'; break;
					case PAY_METHOD_PAYPAL: $res .= '<td>PayPal</td>';break;
					case PAY_METHOD_ADJ: $res .= '<td>Adjustment</td>';break;		
				}	
				$res .= '<td class="desc">'.$payment->creditDesc.'</td>';
				$res .= '<td class="amt">$'.number_format($payment->creditAmt/100, 2, '.', '').'</td>';
				$res .= '<td class="date">';
				$crDate = new DateTime($payment->creditDate);
				$res .= $crDate->format("Y-m-d");
				$res .= '</td>';
				$res .= '<td data-id="'.$payment->id.'" data-paymeth="'.$payment->payMethod.'">';
				$res .= '<button type="button" title="Edit" class="btn btn-primary btn-xs">E</button>&nbsp;<button type="button" title="Delete" class="btn btn-danger btn-xs">–</button>';
				$res .= '</td>';
			$res .= '</tr>';
		}
		$res .= '<tr><td></td><td></td><td></td><td></td><td data-id="0"><button type="button" title="Add" class="btn btn-success btn-sm">+</button></td></tr>';
		for ($i=1;$i<6;$i++) {
			$res .= '<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td></tr>';
		}
		return $bal.'|'.$res;
	}
    /**
     **************************
     */
	public function addEditPayment($id,$account_id,$payMethod,$creditAmt,$creditDesc,$dateYMD) {
		if ($id == 0) {
			$sth = $this->db->prepare("INSERT INTO los_payments (account_id,payMethod,creditAmt,creditDesc,creditDate)
				VALUES (:account_id,:payMethod,:creditAmt,:creditDesc,:dateYMD)");
			$sth->execute(array(':account_id' => $account_id,':payMethod' => $payMethod,':creditAmt' => $creditAmt,':creditDesc' => $creditDesc,':dateYMD' => $dateYMD));
		} else { 
			$sth = $this->db->prepare("UPDATE los_payments
				SET payMethod=:payMethod,creditAmt=:creditAmt,creditDesc=:creditDesc,creditDate=:dateYMD
				WHERE id=:id");
			$sth->execute(array(':id' => $id,':payMethod' => $payMethod,':creditAmt' => $creditAmt,':creditDesc' => $creditDesc,':dateYMD' => $dateYMD));
		}
	}
    /**
     **************************
     */
	public function deletePayment($id) {
		$sth = $this->db->prepare("UPDATE los_payments SET deleted=1 WHERE id=:id");
		$sth->execute(array(':id' => $id));
	}
}