(function($) {
	$.fn.goTo = function() {
		$('html, body').animate({
			scrollTop: $(this).offset().top + -200 + 'px'
		}, 'fast');
		return this; // for chaining...
	};
})(jQuery);

$(document).ready(function() {
	"use strict";
	var //schedURL = document.URL.replace(/schedule$/, ""),
		$document = $(document),
		schedProviders = null,
		schedMenuItems = null,
		schedFormDataSaved = '',
		accountFormDataSaved = '',
		userFormDataSaved = '',
		momStartSched = moment().startOf('month'),
		schedNLE = null,
		editDeletePaymentID = 0;
	
	/** scheduling **/
	function getSchedule(scrollDir) {
		//if (blockUI) return; 
		//blockUI = true;
		$('#loadingtext').removeClass('hide');
		$('#loadinggif').removeClass('hide');
		$('.schednavbtn').prop('disabled', true);
		
		momStartSched.add('month',scrollDir);
		$.ajax({
			type: "GET",
			url: 'getscheduletable',
			data: 'startDateYMD='+momStartSched.format("YYYY-M-D")
		})
		.success(function(ajaxResults) {
			$('#loadingtext').addClass('hide');
			$('#loadinggif').addClass('hide');
			
			if (scrollDir == 0) {
				$("#schedslider").html(ajaxResults);
				$('#schedgallery').css('height', $('.schedslide:first').height());
				$('#monthdesc').html(momStartSched.format("MMMM YYYY")).fadeIn('fast');
				$('.schednavbtn').prop('disabled', false);
			} else {
				//$("#monthdesc").fadeOut('fast');
				if (scrollDir < 0) {
					$("#schedslider").prepend(ajaxResults).stop(1);
					$('#schedgallery').css('height', $('.schedslide:first').height());
					$("#schedslider").css("left",-960).animate({left: 0},500, function() {
						$(".schedslide:last").remove();
						$('#monthdesc').html(momStartSched.format("MMMM YYYY"));//.fadeIn('fast');
						$('.schednavbtn').prop('disabled', false);
					});
				} else {
					$("#schedslider").append(ajaxResults).stop(1);
					$('#schedgallery').css('height', $('.schedslide:last').height());
					$("#schedslider").animate({left: -960},500, function() {
						$(".schedslide:first").remove();
						$("#schedslider").css("left",0);
						$('#monthdesc').html(momStartSched.format("MMMM YYYY"));//.fadeIn('fast');
						$('.schednavbtn').prop('disabled', false);
					});
				}
			}
		});	
	}
	$document.on("click", ".schednavbtn.next", function(e) {getSchedule(1);});
	$document.on("click", ".schednavbtn.prev", function(e) {getSchedule(-1);});
	$document.on("mouseover", "#scheduletable td.enabled", function() {$(this).addClass('tdbkg');});
	$document.on("mouseout", "#scheduletable td.enabled", function() {$(this).removeClass('tdbkg');});
	$document.on("change", "#providers", function() {
		$("#modalSchedLunch textarea").html(
			getMenuItemsForTextarea($('#providers').val())
		)
	});
	 function getMenuItemsForTextarea(provID){
	 	var menuItems = '';
	 	if (provID > 0) {
			$.each(schedMenuItems, function(key, value) {
				if (value.providerID == provID) {
					menuItems += value.itemName+', $'+(value.price/100).toFixed(2)+'&#013;&#010;';
				}
			});
		}
		return menuItems;
	 }
	 function showScheduleModal(dateYMD,numOrders,provID,addlText,extendedText) {
	 	var momDate = moment(dateYMD),
	 		res = '<div class="modal-header">';
		res += '<div id="selectprovider">Lunch Scheduling - Provider Selection</div>';
		res += '<div id="scheddate">'+momDate.format("dddd, MMMM Do, YYYY")+'</div>';
		res += '</div>';
		res += '<div class="modal-body">';
		res += '<form id="formSchedule" role="form">';
		res += '<div class="form-group" style="margin-bottom: 2px;">';
		res += '<label class="control-label">Provider</label>';
		if (numOrders > 0)
			res += '<select id="providers" name="provider" disabled class="form-control">';
		else
			res += '<select id="providers" name="provider" class="form-control">';
		
		res += '<option value="0">[No Provider Selected]</option>';		
		$.each(schedProviders, function(key, value) {
			if (value.id == provID)
				res += '<option value="'+value.id+'" selected>'+value.providerName+'</option>';
			else
				res += '<option value="'+value.id+'">'+value.providerName+'</option>';
		});
		res += '</select>';
		res += '</div>';
		res += '<textarea class="form-control" rows="4" disabled="">'+getMenuItemsForTextarea(provID)+'</textarea>';
		res += '<br />';
		res += '<div class="form-group">';
		res += '<label for="addmsg">Additional Message (opt.)</label>';
		res += '<input type="text" class="form-control" id="addmsg" name="addmsg" maxlength="50" placeholder="Thanksgiving, Spring Break, etc." value="'+addlText+'">';
		res += '</div>';
		res += '<div class="form-group">';
		res += '<label for="ecmsg">Extended Care Message (opt.)</label>';
		res += '<input type="text" class="form-control" id="ecmsg" name="ecmsg" maxlength="50" placeholder="No Extended Care, Extended Care Until 5:00p, etc." value="'+extendedText+'">';
		res += '</div>';
		
		res += '<input type="hidden" id="dateYMD" name="dateYMD" value="'+dateYMD+'">';
		res += '<input type="hidden" id="numOrders" name="numOrders" value="'+numOrders+'">';
		
		res += '</form>';
		res += '</div>';
		res += '<div class="modal-footer">';
		res += '<button id="btnSchedOK" type="button" class="btn btn-primary btn-sm">OK</button>';
		res += '<button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Cancel</button>';
		res += '</div>';
		$("#modalSchedLunch .modal-content").html(res);
		schedFormDataSaved = $('#formSchedule').serialize();
		$('#modalSchedLunch').modal();
	}
	$document.on("click touchstart", "#modalSchedLunch #btnSchedOK", function(e) {
		e.preventDefault();
		var addmsg = $.trim($("#addmsg").val());
		var ecmsg = $.trim($("#ecmsg").val());
		$("addmsg").val(addmsg.substring(50));
		$("ecmsg").val(ecmsg.substring(50));
		
		$('#modalSchedLunch').modal('hide');
		var formData = $('#formSchedule').serialize();
		if (schedFormDataSaved == formData)
			return false;
		//blockUI = true;
		$.blockUI({
	 		message: 'Saving...', 
	 		css: { 
	 			top: '30%',
	 			width: '10%',
	 			left: '45%',
        		border: '1px solid #333', 
        		padding: '15px', 
        		backgroundColor: '#000', 
        		'-webkit-border-radius': '10px', 
        		'-moz-border-radius': '10px', 
        		opacity: .5, 
        		color: '#fff' 
    		},
    		showOverlay: false
    	}); 
        
		$.ajax({
			type: "POST",
			url: 'savesched',
			data: formData+'&startDateYMD='+momStartSched.format("YYYY-M-D")
		})
		.done(function(ajaxResults) {
			$("#schedslider").html(ajaxResults);
			$('#schedgallery').css('height', $('.schedslide:first').height());
			$.unblockUI();
			//blockUI = false;
		});
		return false;
	});
	 $document.on("click touchstart", "#scheduletable .enabled", function(e) {
	 	e.preventDefault();
	 	var jqThis = $(this);
       	var numOrders = jqThis.data("orders"),
       		dateYMD = jqThis.data("dateymd"),
       		provID = jqThis.data("provid"),
       		addlText = '',
       		extendedText = '';
       		
		if (jqThis.children('.addltxt').length > 0)
			addlText = jqThis.children('.addltxt').html();
		if (jqThis.children('.extcare').length > 0)
			extendedText = jqThis.children('.extcare').html();
		showScheduleModal(dateYMD,numOrders,provID,addlText,extendedText);
	});
	function getScheduleData() {
		$.ajax({
			type: "GET",
			url: 'getproviders'
		}).done(function(ajaxResults) {
			schedProviders = $.parseJSON(ajaxResults);	
		});
		$.ajax({
			type: "GET",
			url: 'getmenuitems'
		}).done(function(ajaxResults) {
			schedMenuItems = $.parseJSON(ajaxResults);	
		});
	}
	
	$document.on("click touchstart", "#scheduletable .nle a, #scheduletable .nleorange a", function(e) {
	 	e.preventDefault();
	 	e.stopPropagation();
	 
	 	var jqThis = $(this).parent();
	 	var jqParent = jqThis.parent();
       	var dateYMD = jqParent.data("dateymd"),
       		numOrders = jqParent.data("orders"),
       		nleID = jqThis.data("nleid");
     
       	$.ajax({
			type: "GET",
			url: 'getnlemodal',
			data: 'nleid='+nleID+'&orders='+numOrders+'&dateYMD='+dateYMD
		}).done(function(ajaxResults) {
			$("#modalSchedNLE .modal-content").html(ajaxResults);
			$('#modalSchedNLE').modal();	
	 	});
	});
	$document.on("click touchstart", "#modalSchedNLE #btnNLEDelete", function(e) {	
		$('#modalSchedNLE').modal('hide');
		var formData = $('#formNLE').serialize();
		$.ajax({
			type: "GET",
			url: 'deletenle',
			data: formData+'&startDateYMD='+momStartSched.format("YYYY-M-D")
		})
		.done(function(ajaxResults) {
			$("#schedslider").html(ajaxResults);
			$('#schedgallery').css('height', $('.schedslide:first').height());
		});
	});
	$document.on("click touchstart", "#modalSchedNLE #btnNLEOK", function(e) {
		var reason = $.trim($("#reason").val());
		var desc = $.trim($("#desc").val());
		$("reason").val(reason.substring(30));
		$("desc").val(desc.substring(50));
		if ($('#gradesteachers').val() == 0) {
		//if ($("#gradesteachers").select2("val") == 0) {
			alert('Please choose a Teacher/Grade.');
			return false;
		}
		if (reason == '' || desc == '') {
			alert('Please enter a Reason and a Description.');
			return false;
		}
			
		$('#modalSchedNLE').modal('hide');
		
		var formData = $('#formNLE').serialize();
		$.blockUI({
	 		message: 'Saving...', 
	 		css: { 
	 			top: '25%',
	 			width: '10%',
	 			left: '45%',
        		border: '1px solid #333', 
        		padding: '15px', 
        		backgroundColor: '#000', 
        		'-webkit-border-radius': '10px', 
        		'-moz-border-radius': '10px', 
        		opacity: .5, 
        		color: '#fff' 
    		},
    		showOverlay: false
    	}); 
        
		$.ajax({
			type: "POST",
			url: 'savenle',
			data: formData+'&startDateYMD='+momStartSched.format("YYYY-M-D")
		})
		.done(function(ajaxResults) {
			$("#schedslider").html(ajaxResults);
			$('#schedgallery').css('height', $('.schedslide:first').height());
			$.unblockUI();
			//blockUI = false;
		});
		return false;
	});		
	
	/**** payments *****/
	function updatePaymentsHTML(ajaxResults) {
		var pos = ajaxResults.indexOf('|');
		var bal = parseFloat(ajaxResults.substring(0,pos))/100;
		$("#tblPayments tbody").html(ajaxResults.substring(pos+1));
			 
		if (bal == 0) {
			$("#paymentscurbal").html('Current Balance:').removeClass().addClass('pull-right');
			$("#paymentscurbalval").html('$0.00').removeClass().addClass('pull-right');
		} else if (bal < 0) {
			bal = -bal;
			$("#paymentscurbal").html('Amount Due:').removeClass().addClass('redtext pull-right');
			$("#paymentscurbalval").html('$'+bal.toFixed(2)).removeClass().addClass('redtext pull-right');
		} else {
			$("#paymentscurbal").html('Account Credit:').removeClass().addClass('greentext pull-right');
			$("#paymentscurbalval").html('$'+bal.toFixed(2)).removeClass().addClass('greentext pull-right');
		}
	}
	
	$document.on("change", "#paymentaccountnames", function() {
		var account_id=$('#paymentaccountnames').val();
		if (account_id == 0) { 
			var res = '';
			for (var i=1;i < 6;i++) {
				res += '<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td></tr>';
			}
			$("#tblPayments tbody").html(res);
			$("#paymentscurbalval").html('');
			$("#paymentscurbal").html('');
			return;	
		};
		$.ajax({
			type: "GET",
			url: 'getpaymentsbody',
			data: 'account_id='+account_id
		})
		.done(function(ajaxResults) {
			updatePaymentsHTML(ajaxResults);
		});
	});
	$document.on("click", "#tblPayments .btn-danger", function() {
		editDeletePaymentID = $(this).parent().data("id");
		var gp = $(this).parent().parent();
		var t1 = gp.children(":first-child").html();
		var t2 = gp.children(".desc").html();
		var t3 = gp.children(".amt").html();
		var t4 = gp.children(".date").html();
		var msg = '<p>Account:<br />'+$("#paymentaccountnames :selected").text()+'</p>';
		msg += '<p>Type: '+t1+'</p>';
		if (t2 != '')
			msg += '<p>Description: '+t2+'</p>';
		msg += '<p>Amount: '+t3+'</p>';
		msg += '<p>Date: '+t4+'</p>';
		
		$("#modalDeletePayment .modal-body").html(msg+'<br /><p style="color:#3366ff;">Delete This Payment?</p>');
		$('#modalDeletePayment').modal('show');	
	});
	$document.on("click", "#btnPayDelete", function() {
		$('#modalDeletePayment').modal('hide');
		$.ajax({
			type: "GET",
			url: 'deletepayment',
			data: 'account_id='+$("#paymentaccountnames").val()+'&id='+editDeletePaymentID
		})
		.done(function(ajaxResults) {
			updatePaymentsHTML(ajaxResults);
		});
	});
	$document.on("blur",'#payAmt', function(e) {
	 	$('#payAmt').formatCurrency({negativeFormat: '-%s%n'});
	});
	
	
	$document.on("click", "#tblPayments .btn-success", function() {
		editDeletePaymentID = 0;
		$('#btnPayAddEdit').removeClass().addClass('btn btn-sm btn-success');
		$('#modalAddEditPayment .modal-header').removeClass().addClass('modal-header success');
		$('#modalAddEditPayment .modal-title').html('Add Payment');
		
		$('#modalAddEditPayment #accountname').val($("#paymentaccountnames :selected").text());
		$("#payDesc").val('');
		var momToday = moment();
		$("#payDate").val(momToday.format("YYYY-MM-DD"));
		$("#payAmt").val('');
		$('#modalAddEditPayment').modal('show');	
	});
	$document.on("click", "#tblPayments .btn-primary", function() {
		editDeletePaymentID = $(this).parent().data("id");
			
		$('#btnPayAddEdit').removeClass().addClass('btn btn-sm btn-primary');
		$('#modalAddEditPayment .modal-header').removeClass().addClass('modal-header primary');
		$('#modalAddEditPayment .modal-title').html('Edit Payment');
		
		$('#modalAddEditPayment #accountname').val($("#paymentaccountnames :selected").text());
		$("#payMeth").val($(this).parent().data("paymeth"));
		
		var gp = $(this).parent().parent();
		$("#payDesc").val(gp.children(".desc").html());
		$("#payDate").val(gp.children(".date").html());
		$("#payAmt").val(gp.children(".amt").html());
		
		$('#modalAddEditPayment').modal('show');	
	});
	$document.on("click", "#btnPayAddEdit", function() {
		var payDesc = $.trim($("#payDesc").val());
		$("#payDesc").val(payDesc.substring(0,100));
		
		$('#payAmt').formatCurrency({negativeFormat: '-%s%n'});
		var payAmt = $.trim($("#payAmt").val());
		payAmt = parseFloat(payAmt.replace(/\$/g, ""));
		if ((isNaN(payAmt)) || (payAmt == 0)) {
			alert('Please enter an Amount.');
			return;	
		}
		var theDate = $.trim($("#payDate").val());
		var momDate = moment(theDate,["YYYY-MM-DD","YYYY-M-D","YYYY-MM-D","YYYY-M-DD","YYYY/MM/DD","YYYY/M/D","YYYY/MM/D","YYYY/M/DD"]);
		if (!momDate.isValid()) {
			alert('Please enter a valid Date Received.');
			return;	
		}
		var payMeth= $("#payMeth").val();
		$('#modalAddEditPayment').modal('hide');
		
		$.ajax({
			type: "POST",
			url: 'addeditpayment',
			data: 'id='+editDeletePaymentID+'&payMeth='+payMeth+'&payAmt='+payAmt*100+'&payDesc='+payDesc+'&dateYMD='+
				momDate.format("YYYY-MM-DD")+'&account_id='+$("#paymentaccountnames").val()
		})
		.done(function(ajaxResults) {
			updatePaymentsHTML(ajaxResults);
		});
	});
	
	
	/** reports **/
	function doReport(reportid,dateYMD,account_id) {
		$.blockUI({
	 		message: 'Retrieving...', 
	 		css: { 
	 			top: '25%',
	 			width: '10%',
	 			left: '45%',
        		border: '1px solid #333', 
        		padding: '15px', 
        		backgroundColor: '#000', 
        		'-webkit-border-radius': '10px', 
        		'-moz-border-radius': '10px', 
        		opacity: .9, 
        		color: '#fff' 
    		},
    		showOverlay: false
    	});
		$.ajax({
			type: "GET",
			url: 'getadmreport',
			data: 'reportid='+reportid+'&dateYMD='+dateYMD+'&account_id='+account_id
		})
		.done(function(ajaxResults) {
			$("#admreportdata").html(ajaxResults);
			$.unblockUI();
		});
	}	
	$document.on("change", "#admreportselect", function() {
		var reportid = parseInt($("#admreportselect").val());
		$("#admreportdate").val(0);
		$("#admreportaccount").val(0);
		$("#admreportdata").html('');
		
		switch (reportid) {
			case 1:
				$("#admreportdate").removeClass('hide');
				$("#admreportaccount").addClass('hide');
			break;
			case 2:
				$("#admreportdate").removeClass('hide');
				$("#admreportaccount").addClass('hide'); 
			break;
			case 3: 
				$("#admreportdate").addClass('hide');
				$("#admreportaccount").addClass('hide');
				doReport(reportid,0,0);
			break;
			case 4: 
				$("#admreportdate").addClass('hide');
				$("#admreportaccount").removeClass('hide');
			break;
		}
	});
	$document.on("change", "#admreportdate, #admreportaccount", function() {
		var reportid = parseInt($("#admreportselect").val());
		var dateYMD = $("#admreportdate").val();
		var account_id = $("#admreportaccount").val();
		$("#admreportdata").html('');
		var needData = false;
		switch (reportid) {
			case 1: needData = dateYMD != 0; break;
			case 2: needData = dateYMD != 0; break;
			//case 3: break;
			case 4: needData = account_id != 0; break;
		}
		if (needData) 
			doReport(reportid,dateYMD,account_id);
	});
	
	/** lunch orders **/
	$document.on("change", "#lunchordersselectdate", function() {
		var dateYMD = $("#lunchordersselectdate").val();
		if (dateYMD == 0) {
			$("#lunchordersresults").html('');
			return;
		}
		$.ajax({
			type: "GET",
			url: 'getlunchorders',
			data: 'dateYMD='+dateYMD
		})
		.done(function(ajaxResults) {
			$("#lunchordersresults").html(ajaxResults);
		});
	});
	$document.on("click touchstart", "#btnDoAction.btn-success", function() {
		$.ajax({
			type: "GET",
			url: 'setstatusscheduled',
			data: 'dateYMD='+$("#lunchordersselectdate").val()
		})
		.done(function(ajaxResults) {
			$("#lunchordersresults").html(ajaxResults);
		});
	});
	$document.on("click touchstart", "#btnDoAction.btn-warning", function() {
		$.ajax({
			type: "GET",
			url: 'setstatusordered',
			data: 'dateYMD='+$("#lunchordersselectdate").val()
		})
		.done(function(ajaxResults) {
			$("#lunchordersresults").html(ajaxResults);
		});
	});
	
	/** Accounts **/
	function addAccount() {
		$('#modalAccount #title').html('Add Account').addClass('add');
		$('#modalAccount #btnAccountOK').removeClass('btn-primary').addClass('btn-success');
		$("#grpacctype").addClass('hide');
		$("#grpaccactive").addClass('hide');
		$("#grpaccallowneworders").addClass('hide');
		$(".account_id").val(0);
		$(".user_id").val(0);
		$("#aname_acc").val('');
		$("#uname").val('');
		$("#email").val('');
		$("#btnAccountDelete").addClass('hide');
	 	$('#modalAccount').modal();
	}
	function editAccount(el) {
	 	var aname = el.html();
	 	$('#modalAccount #title').html('Edit Account').removeClass('add');
		$('#modalAccount #btnAccountOK').removeClass('btn-success').addClass('btn-primary');
	 	$("#grpacctype").removeClass('hide');
		$("#grpaccactive").removeClass('hide');
		$("#grpaccallowneworders").removeClass('hide');
	 	$(".account_id").val(el.data("aid"));
	 	$("#aname_acc").val(aname.replace(/&amp;/g,'&'));
		$("#uname").val(el.data("uname"));
		$("#email").val(el.parent().parent().children(':nth-child(2)').html());
		$("#uactive").val(el.data("uactive"));
		$("#nnorders").val(el.data("nnorders"));
		$("#atype").val(el.data("atype")); 
		if (el.data("acount") == 0) {
			$(".btn").addClass('pull-right');
			$("#btnAccountDelete").removeClass('hide').addClass('pull-left');
		} else {
			$(".btn").removeClass('pull-right');
			$("#btnAccountDelete").addClass('hide');
		}	
		accountFormDataSaved = $('#formAccount').serialize();
	 	$('#modalAccount').modal();
	}
	function addUser(arec) {
		$('#modalUser #title').html('Add User').addClass('add');
	 	$('#modalUser #btnUserOK').removeClass('btn-primary').addClass('btn-success');
	 	$("#grpuserteachers").removeClass('hide');
	 	$("#grpuserallowedtoorder").addClass('hide');
	 	$("#aname_user").val(arec.html().replace(/&amp;/g,'&'));
	 	$(".account_id").val(arec.data("aid"));
	 	$(".user_id").val(0);
	 	$("#lname").val('');
	 	$("#fname").val('');
	 	$("#utype").val(1);
	 	$("#tid").val(1);
	 	$("#ato").val(1);
	 	$("#btnUserDelete").addClass('hide');
	 	$('#modalUser').modal();
	 }
	 function editUser(el) {
	 	var uid = el.data("uid");
	 	var uname = el.html();
	 	var pos = uname.indexOf(',');
	 	var utype = el.data("utype");
	 	var arec = el.parent().parent().parent().children().find('a');
	 		
	 	$('#modalUser #title').html('Edit User').removeClass('add');
	 	$('#modalUser #btnUserOK').removeClass('btn-success').addClass('btn-primary');
	 	$(".account_id").val(arec.data("aid"));
	 	$(".user_id").val(uid);
	 	$("#lname").val(uname.substring(0,pos));
	 	$("#fname").val(uname.substring(pos+2));
	 	$("#ato").val(el.data("ato"));
	 	$("#tid").val(el.data("tid"));
	 	$("#utype").val(utype);
	 	if (utype != 1)
			$("#grpuserteachers").addClass('hide');
		else
			$("#grpuserteachers").removeClass('hide');
		$("#grpuserallowedtoorder").removeClass('hide');
		$("#aname_user").val(arec.html().replace(/&amp;/g,'&'));
		
		if (el.data("ucount") == 0) {
			$(".btn").addClass('pull-right');
			$("#btnUserDelete").removeClass('hide').addClass('pull-left');
		} else {
			$(".btn").removeClass('pull-right');
			$("#btnUserDelete").addClass('hide');
		}
		userFormDataSaved = $('#formUser').serialize();
	 	$('#modalUser').modal();
	}
	$document.on("click touchstart", "#btnCreateNewAccount", function(e) {e.preventDefault();addAccount();});
	$document.on("click touchstart", "#btnAccountDelete", function(e) {
	e.preventDefault();
		$.ajax({
			type: "GET",
			url: 'deleteaccount',
			data: 'account_id='+$(".account_id").val()
		})
		.done(function(ajaxResults) {
			$("#tblAcctMaint tbody").html(ajaxResults);
			$('#modalAccount').modal('hide');	
			$('#SELECTED').goTo();
		});
	});
	$document.on("click touchstart", "#btnUserDelete", function(e) {
	e.preventDefault();
		$.ajax({
			type: "GET",
			url: 'deleteuser',
			data: 'account_id='+$(".account_id").val()+'&userid='+$(".user_id").val()
		})
		.done(function(ajaxResults) {
			$("#tblAcctMaint tbody").html(ajaxResults);
			$('#modalUser').modal('hide');	
			$('#SELECTED').goTo();
		});
	});
	
	$document.on("click touchstart", "#tblAcctMaint a", function(e) {
		e.preventDefault();
    	
	 	var el = $(this);
	 	if (el.attr('href') == '#ADD') {
	 		var arec = el.parent().parent().parent().children().find('a');
	 		addUser(arec);
	 	} else {
	 		if (el.data("uid") > 0)
	 			editUser(el);
	 		 else 
	 		 	editAccount(el);
	 	}
	});
	$document.on("change", "#utype", function() {
		var utype = parseInt($("#utype").val());
		if (utype != 1)
			$("#grpuserteachers").addClass('hide');
		else {
			$("#grpuserteachers").removeClass('hide');
			$("#tid").val(1);
		}
	});
	
	$document.on("click", "#modalUser #btnUserOK", function(e) {
	e.preventDefault();
		var fname = $.trim($("#fname").val());
		var lname = $.trim($("#lname").val());
		fname = fname.replace(/,/g, '').substring(0,50);
		lname = lname.replace(/,/g, '').substring(0,50);
		
		$("#fname").val(fname);
		$("#lname").val(lname);
		
		if (fname.length == 0) {
			alert("Please enter a First Name.");
			$("#fname").focus();
			return false;
		}
		if (lname.length == 0) {
			alert("Please enter a Last Name.");
			$("#lname").focus();
			return false;
		}
		
		var utype = parseInt($("#utype").val());
		if (utype == 1) {
			var tid = $("#tid").val();
			if (tid < 2) {
				alert("Please select a Teacher.");
				//$('#s2id_tid').select2('open');
				return false;
			}
		}
		
		var serialized = $('#formUser').serialize();
		if (userFormDataSaved == serialized) {
			$('#modalUser').modal('hide');
		} else {
			$.ajax({
				type: "POST",
				url: 'saveuser',
				data: serialized
			})
			.done(function(ajaxResults) {
				if (ajaxResults.substring(0,6) == 'Error:') {
					alert(ajaxResults.substring(7));
				} else {
					$("#tblAcctMaint tbody").html(ajaxResults);
					$('#modalUser').modal('hide');	
					$('#SELECTED').goTo();
				}
			});
		}
	});
	
	function validateEmail(email) { 
    	var re = /@/;
    	return re.test(email);
    }
	$document.on("click", "#modalAccount #btnAccountOK", function(e) {
	e.preventDefault();
		var aname_acc = $.trim($("#aname_acc").val());
		var uname = $.trim($("#uname").val());
		var email = $.trim($("#email").val());
		
		uname = uname.substring(0,64);
		aname_acc = aname_acc.substring(0,64);
		email = email.substring(0,64);
		
		$("#aname_acc").val(aname_acc);
		$("#uname").val(uname);
		$("#email").val(email);
		
		if (!validateEmail(email)) {
			alert("Please enter a valid Email address.");
			$("#email").focus();
			return false;
		}
		if (aname_acc.length == 0) {
			alert("Please enter an Account Name.");
			$("#aname_acc").focus();
			return false;
		}
		if (uname.length == 0) {
			alert("Please enter a Login Name.");
			$("#uname").focus();
			return false;
		}
		var serialized = $('#formAccount').serialize();
		if (accountFormDataSaved == serialized) {
			$('#modalAccount').modal('hide');
		} else {
			$.ajax({
				type: "POST",
				url: 'saveaccount',
				data: serialized
			})
			.done(function(ajaxResults) {
				if (ajaxResults.substring(0,6) == 'Error:') {
					alert(ajaxResults.substring(7));
				} else {
					$("#tblAcctMaint tbody").html(ajaxResults);
					$('#modalAccount').modal('hide');
					$('#SELECTED').goTo();
				}
			});
		}
	});
	
	/*** main ***/
	function doAdminInit() {
		if ($("#schedgallery").length > 0) {
			getScheduleData();
			getSchedule(0);
		}
	}	
	doAdminInit();
});