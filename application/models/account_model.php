<?php

class AccountModel {
	
	protected $payments;
	protected $orders;
	protected $totalDebits = 0;
	protected $confirmedCredits = 0;
	protected $balance = 0;
	protected $firstNames = array();
	protected $numOrders = 0;
	protected $numPayments = 0;

    public function __construct(Database $db) {
        $this->db = $db;
    }
    /**
     **************************
     */
	public function getConfirmedCredits() {
		return '$'.number_format($this->confirmedCredits/100, 2, '.', '');
	}
    /**
     **************************
     */
	public function getTotalDebits() {
		return '$'.number_format($this->totalDebits/100, 2, '.', '');
	}
    /**
     **************************
     */
	public function getNumOrders() {
		return $this->numOrders;
	}
    /**
     **************************
     */
	public function getNumPayments() {
		return $this->numPayments;
	}
    /**
     **************************
     */
	public function getBalanceRow() {
		if ($this->balance > 0) {
			return '<tr class="success creditavail"><td>Credit Available</td>'.
				'<td style="text-align:right;">$'.number_format($this->balance, 2, '.', '').'</td></tr>';
		} else {
			return '<tr class="curbal"><td>Current Balance</td>'.
				'<td style="text-align:right;">$'.number_format(-$this->balance, 2, '.', '').'</td></tr>';
		}
	}
    /**
     **************************
     */
	public function getBalance() {
		return $this->balance;
	}
    /**
     **************************
     */
	public function getAmtToPay() {
		$retval = '$'.number_format(-$this->balance*1.022, 2, '.', '');
		return $retval;
	}
    /**
     **************************
     */
	public function getFirstNames() {
		return $this->firstNames;
	}
    /**
     **************************
     */
	public function getPayPalExtraRow() {
		$retval = '';
		if ($this->balance < 0) {
			$retval = '<tr class="paynow"><td>When paying with Pay Now, add 2.2% transaction fee</td>'.
				'<td style="text-align:right;">$'.number_format(-$this->balance*0.022, 2, '.', '').'</td></tr>';
		}
		return $retval;
	}
    /**
     **************************
     */
	public function populateOrderTables() {
		$tables = array();
		$nameRow = '';
		$tableRow = '';
		$totalRow = '<tr class="trtotalrow"><td class="tdtotalrow1" colspan="2">Total of orders</td><td class="tdtotalrow2">$';
		$total = 0;
		foreach ($this->orders as $order) {
			//name,shortDesc,orderDate,totalPrice,statusCode
			if ($nameRow != $order->firstName) {
				//$tableRow = '<tr class="info"><td style="color:#336699;font-weight:bold;font-size:15px;">'.$order["name"].'</td><td></td><td></td></tr>';
				$nameRow = $order->firstName;
				$this->firstNames[] = $nameRow;
				if (!empty($tableRow)) {
					$tableRow .= $totalRow.number_format(($total/100), 2, '.', '').'</td></tr>';
					$tables[] = $tableRow;
					$tableRow = '';
					$total = 0;
				}
			}
			$tableRow .= '<tr><td>'.$order->shortDesc.
				'</td><td>'.date("Y-m-d",strtotime($order->orderDate)).
				'</td><td style="text-align:right;">$'.number_format($order->totalPrice/100, 2, '.', '').'</td></tr>';
			$total += $order->totalPrice;
		}
		if (!empty($tableRow)) {
			//$tableRow .= '<tr style="color:#ff6600;font-weight:bold;"><td style="background-color:#FCF8E3;" colspan="2">Total of orders</td><td style="background-color:#FCF8E3;text-align:right;">$'.number_format(($total/100), 2, '.', '').'</td></tr>';
			$tables[] = $tableRow.$totalRow.number_format(($total/100), 2, '.', '').'</td></tr>';
		} else {
			$tables[] ='<tr><td colspan="3">No orders found</td></tr>';
		}
		return $tables;
	}
    /**
     **************************
     */
	public function populatePaymentRows() {
		$table = '';
		$total = 0;
		$today = date("Y-m-d");
		$payMeth = '';
		foreach ($this->payments as $payment) {
			switch ($payment->payMethod) {
				case PAY_METHOD_CASH: $payMeth = 'Cash';break;
				case PAY_METHOD_CHECK: $payMeth = 'Check'; break;
				case PAY_METHOD_PAYPAL: $payMeth = 'PayPal';break;
				case PAY_METHOD_ADJ: $payMeth = 'Adjustment';break;
			}
			$dbDate = date("Y-m-d",strtotime($payment->creditDate));
			if ($dbDate === $today) {
				$table .= '<tr style="color:#3366ff;font-weight:bold;">';
				$dbDate = '<td style="text-align:center;">Today</td>';
			} else {
				$table .= '<tr>';
				$dbDate = '<td>'.$dbDate.'</td>';
			}
			$table .= '<td>'.$payMeth.'</td><td>'.$payment->creditDesc.'</td>'.$dbDate.'<td style="text-align:right">$'.number_format($payment->creditAmt/100, 2, '.', '').'</td></tr>';
			$total += $payment->creditAmt;
		}
		if ($total > 0) {
			$table .= '<tr style="color:#ff6600;font-weight:bold;"><td style="background-color:#FCF8E3;" colspan="3">Total received</td>
					<td style="background-color:#FCF8E3;text-align:right;">$'.number_format($total/100, 2, '.', '').'</td></tr>';
			return $table;
		} else
			return '<tr><td colspan="4">No payments found</td></tr>';
	}
    /**
     **************************
     */
	public function populateModelVars() {
		$sth = $this->db->prepare("SELECT confirmed_credits,confirmed_debits,total_debits FROM los_accounts WHERE id=:account_id");
		$sth -> execute(array(':account_id'=>Session::get('account_id')));
		$account = $sth->fetch();
		$this->totalDebits = $account->total_debits;
		$this->confirmedCredits = $account->confirmed_credits;
		$this->balance = ($this->confirmedCredits-$this->totalDebits)/100;
		
		$sth = $this->db->prepare("SELECT firstName,lastName,shortDesc,orderDate,totalPrice,statusCode
			FROM los_orders o
			INNER JOIN los_users u ON o.userID = u.id
			WHERE o.account_id=:account_id
			AND statusCode < 2
			ORDER BY lastName,firstName,orderDate");
		$sth -> execute(array(':account_id'=>Session::get('account_id')));
		$this->orders = $sth -> fetchAll();
		$this->numOrders = count($this->orders);
		
		$sth = $this->db->prepare("SELECT id,payMethod,creditAmt,creditDate,creditDesc 
			FROM los_payments 
			WHERE account_id=:account_id
			AND deleted=0 
			ORDER BY creditDate");
		$sth -> execute(array(':account_id'=>Session::get('account_id')));
		$this->payments = $sth -> fetchAll();
		$this->numPayments = count($this->payments);
	}
    /**
     **************************
     */
	public function savePayment($resArray) {
		$creditAmt = Session::get('amtToPay')*100/1.022;
		$sth = $this->db->prepare("INSERT INTO los_payments (account_id,payMethod,creditAmt,creditDesc,creditDate) 
			VALUES (:account_id,3,:creditAmt,:creditDesc,NOW())");
		$sth->execute(array(':account_id'=>Session::get('account_id'),':creditAmt'=>$creditAmt,':creditDesc'=>$resArray["TRANSACTIONID"]));
	}
}