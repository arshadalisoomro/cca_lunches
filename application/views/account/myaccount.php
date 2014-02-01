<div class="container page">
    <?php
        if (!empty($this->paymentmessage)) {
			echo '<br /><div class="alert alert-success alert-dismissable">';
			echo '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>';
			echo '<div>' . $this->paymentmessage . '</div>';
			echo '</div>';
		}

        $this->renderFeedbackMessages();
	?>
			
    <div class="accountdiv">
        <img class="pull-left" style="margin-right: 10px;" src="<?php echo URL; ?>public/img/ccaimages/myaccount.png" alt="My account">
        <h2><?php echo $this->account_name; ?></h2>
        <br />
        <ul id="accountTab" class="nav nav-tabs">
            <li class='active'><a data-toggle="tab" href="#tabTotals">Summary</a></li>
            <li><a data-toggle="tab" href="#tabPayments">Payments</a></li>
            <?php foreach ($this->firstnames as $i=>$firstName) : ?>
                <li><a data-toggle="tab" href="#tab<?php echo $i; ?>"><?php echo $firstName.'\'s Orders'; ?></a></li>
            <?php endforeach; ?>
        </ul>

        <div id="accountTabContent" class="tab-content">
            <div id="tabTotals" class="tab-pane fade in active">
                <table class="table table-bordered table-condensed table-header">
                    <thead>
                        <tr >
                            <th>Totals</th>
                            <th width="60">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?php echo $this->numorders; ?> Lunches Ordered</td>
                            <td style="text-align:right;"><?php echo $this->totaldebits; ?></td>
                        </tr>
                        <tr>
                            <td><?php echo $this->numpayments; ?> Payments Received</td>
                            <td style="text-align:right;"><?php echo $this->confirmedcredits; ?></td>
                        </tr>
                        <?php echo $this->balancerow; ?>
                        <?php echo $this->paypalextrarow; ?>
                    </tbody>
                </table>
                <?php if ($this->balance < 0) :?>
                    <div style="text-align:right;">
                        <form class="form-inline" role="form" id="paypal-form" action="<?php echo URL; ?>account/pay" method="post">
                            <div class="form-group">
                                <span id='redirectingtextaccount' class="hide">Redirecting to PayPal...</span>
                                <span id='loadinggifaccount' class="hide"></span>
                                <input id="ppbtn" type="image" name="submit" src="https://www.paypal.com/en_US/i/btn/btn_paynow_LG.gif"/>
                                <input type="text" class="form-control currency" id="amountToPay" name="amtToPay" value="<?php echo $this->amttopay; ?>"
                                    data-orig="<?php echo $this->amttopay; ?>" />
                            </div>
                        </form>
                    </div>
                <?php endif ?>
            </div>

            <div id="tabPayments" class="tab-pane fade in">
                <table class="table table-striped table-bordered table-condensed table-header">
                    <thead>
                        <tr>
                            <th width="90">Type</th>
                            <th>Description</th>
                            <th width="100">Received On</th>
                            <th width="60">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php echo $this->paymentrows; ?>
                    </tbody>
                </table>
            </div>


            <?php foreach ($this->ordertables as $i=>$orderTable) : ?>
                <div id="tab<?php echo $i; ?>" class="tab-pane fade in">
                    <table class="table table-striped table-bordered table-condensed table-header">
                        <thead>
                            <tr>
                                <th>Lunch Ordered</th>
                                <th width="100">Date</th>
                                <th width="60">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php echo $orderTable; ?>
                        </tbody>
                    </table>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>