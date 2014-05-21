<div class="container page width700" >
    <div style="text-align:center;">
        <h2 style="color:#008800;">Receive Payments</h2>
		<table style="margin: 20px auto;">
			<tr>
				<td>
					Account&nbsp;&nbsp;
				</td>
				<td>
					<select id="paymentaccountnames" class="form-control">
						<option value="0">[Please Select]</option>
						<?php
							foreach ($this->accountnames as $accountname) {
								echo '<option value="'.$accountname->id.'">'.$accountname->account_name.'</option>';
							}
						?>
					</select>
				</td>
			</tr>
		</table>
    </div>

    <table id="tblPayments" class="table table-condensed table-header table-striped table-bordered">
        <thead>
            <tr>
                <th>Type</th>
                <th>Description</th>
                <th>Amount</th>
                <th>Received On</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <tr><td>&nbsp;</td><td></td><td></td><td></td><td></td></tr>
            <tr><td>&nbsp;</td><td></td><td><td></td></td><td></td></tr>
            <tr><td>&nbsp;</td><td></td><td><td></td></td><td></td></tr>
            <tr><td>&nbsp;</td><td></td><td><td></td></td><td></td></tr>
            <tr><td>&nbsp;</td><td></td><td><td></td></td><td></td></tr>
            <tr><td>&nbsp;</td><td></td><td><td></td></td><td></td></tr>
        </tbody>
    </table>

    <div>
        <!--<button type="button" title="Delete" class="btn btn-success btn-xs">+</button>&nbsp;
        <button type="button" title="Edit" class="btn btn-primary btn-xs">E</button>&nbsp;
        <button type="button" title="Delete" class="btn btn-danger btn-xs">–</button>&nbsp;=&nbsp;Add / Edit / Delete Payments-->
        <span id="paymentscurbalval" class="hide pull-right"></span>
        <span id="paymentscurbal" class="hide pull-right"></span>
    </div>

    <div class="modal fade" id="modalAddEditPayment" tabindex="-1" role="dialog" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                        &times;
                    </button>
                    <h4 class="modal-title">Add/Edit Payment</h4>
                </div>
                <div class="modal-body">

                    <form role="form" id="formPayment">
                        <div class="form-group">
                            <label for="accountname">Account</label>
                            <input type="text" class="form-control" id="accountname" name="accountname" disabled/>
                        </div>
                        
                        <div class="form-group">
                            <label for="payMeth">Payment Method</label>
                            <select id="payMeth" class="form-control">
                                <option value="1">Cash</option>
                                <option value="2">Check</option>
                                <option value="3">PayPal</option>
                                <option value="4" selected="">Adjustment</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="payDesc">Payment Description (opt.)</label>
                            <input type="text" class="form-control" id="payDesc" name="payDesc" placeholder="Check #, Adjustment Reason, etc."/>
                        </div>
                       
                        <div class="form-group">
                            <label for="payDate">Date Received</label>
                            <input type="date" class="form-control" id="payDate" name="payDate"/>
                        </div>
                      
                        <div class="form-group">
                            <label for="payAmt">Amount</label>
                            <input type="text" class="form-control currency" id="payAmt" name="payAmt"/>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button id="btnPayAddEdit" type="button" class="btn btn-sm">OK</button>
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalDeletePayment" tabindex="-1" role="dialog" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                        &times;
                    </button>
                    <h4 class="modal-title">Delete Payment</h4>
                </div>
                <div class="modal-body" style="text-align:center;">
                </div>
                <div class="modal-footer">
                    <button id="btnPayDelete" type="button" class="btn btn-sm btn-danger">
                        OK
                    </button>
                    <button type="button" class="btn btn-sm btn-default" data-dismiss="modal">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript" src="<?php echo URL; ?>public/js/vendor/jquery.formatCurrency-1.4.0.js"></script>