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

	var displayPeriod = 'week',
		displayPeriodShort = 'w',
		momToday = moment(),
		momStartPeriod = moment().startOf(displayPeriod),
		momEndPeriod = moment().endOf(displayPeriod),
		curPeriodNo = 0,
		editLunchParams = null,
		blockUI = false;
	
	/** reports page **/
	$(document).on('click', '.btn-print', function (e) {
		var f = $('.new-tab-opener');
		f.attr('action', $(this).attr('data-href'));
		f.submit();
	});
	
	/** account page **/
	$(document).on("blur",'.currency', function(e) {
	 	$('.currency').formatCurrency();
	});
	$('#paypal-form').submit(function() {
		$("#amountToPay").attr('readonly', true);
		$("#ppbtn").attr('readonly', true);
		$("#redirectingtextaccount").removeClass('hide');
		$("#loadinggifaccount").removeClass('hide');
	});
	$(window).unload(function() {
		$("#amountToPay").attr('readonly', false);
		$("#ppbtn").attr('readonly', false);
		$("#redirectingtextaccount").addClass('hide');
		$("#loadinggifaccount").addClass('hide');
	});
	
	/** order lunch calendar **/
	 $(document).on("click touchstart", "#lunchestable .editable", function(e) {
	 	e.preventDefault();
	 	if (blockUI)
	 		return false;
	 		
	 	var image = '';
       	var lc = '';
       	var includes = '';
       	var provID = $(this).data("provid");
       	var name = $(this).data("name");
       	
       	editLunchParams=new Object();
       	editLunchParams.userID = $(this).data("userid");
		editLunchParams.orderID = $(this).data("orderid");
		editLunchParams.orderDate = $(this).data("dateymd");
		editLunchParams.clickedUserLunchDate = $(this);
		
       	switch(parseInt(provID)) {
			case 4:
				image = 'someburros';
				lc += '<tr><td><div class="radio"><label><input type="radio" name="rb" id="ctl6" value="6">Bean Burrito</label></div></td>';
				lc += '<td>$4.50</td></tr>';
				lc += '<tr><td><div class="radio"><label><input type="radio" name="rb" id="ctl7" value="7">Cheese Quesadilla</label></div></td>';
				lc += '<td>$4.50</td></tr>';
				lc += '<tr><td><div class="checkbox"><label><input type="checkbox" name="chk" id="ctl8" value="8" disabled>Extra Cheese Quesadilla</label></div></td>';
				lc += '<td>$2.50</td></tr>';
				includes = 'Includes Chips &amp; Salsa';
			break;	
			case 5:
				image = 'peiwei';
				lc += '<tr><td><div class="radio"><label><input type="radio" name="rb" id="ctl2" value="2">Honey Seared Chicken</label></div></td>';
				lc += '<td>$4.50</td></tr>';
				lc += '<tr><td><div class="radio"><label><input type="radio" name="rb" id="ctl3" value="3">Teryaki Chicken</label></div></td>';
				lc += '<td>$4.50</td></tr>';
			break;
			case 6:
				image = 'floridinos_s';
				lc += '<tr><td><div class="radio"><label><input type="radio" name="rb" id="ctl1" value="1">Spaghetti</label></div></td>';
				lc += '<td>$4.50</td></tr>';
				includes = 'Includes Roll &amp; Butter';	
			break;
			case 7:
				image = 'subway';
				lc += '<tr><td><div class="radio"><label><input type="radio" name="rb" id="ctl9" value="9">6" Ham Sub</label></div></td>';
				lc += '<td>$4.50</td></tr>';
				lc += '<tr><td><div class="radio"><label><input type="radio" name="rb" id="ctl10" value="10">6" Turkey Sub</label></div></td>';
				lc += '<td>$4.50</td></tr>';
				includes = 'Includes Chips &amp; Milk';
			break;
			case 8:
				image = 'floridinos_p';
				lc += '<tr><td><div class="radio"><label><input type="radio" name="rb" id="ctl4" value="4">Cheese Pizza</label></div></td>';
				lc += '<td>$4.50</td></tr>';
				lc += '<tr><td><div class="radio"><label><input type="radio" name="rb" id="ctl5" value="5">Pepperoni Pizza</label></div></td>';
				lc += '<td>$4.50</td></tr>';
			break;
			case 9:
				image = 'wendys';
				lc += '<tr><td><div class="radio"><label><input type="radio" name="rb" id="ctl11" value="11">6 Chicken Nuggets</label></div></td>';
				lc += '<td>$4.50</td></tr>';
				lc += '<tr><td><div class="checkbox"><label><input type="checkbox" name="chk" id="ctl12" value="12" disabled>Extra 4 Chicken Nuggets</label></div></td>';
				lc += '<td>$2.00</td></tr>';
				includes = 'Includes Fries';
			break;
			case 10:
				image = 'someburros';
				lc += '<tr><td><div class="radio"><label><input type="radio" name="rb" id="ctl13" value="13">Mini Cheese Crisp</label></div></td>';
				lc += '<td>$4.50</td></tr>';
				lc += '<tr><td><div class="radio"><label><input type="radio" name="rb" id="ctl4" value="14">Mini Bean Burro</label></div></td>';
				lc += '<td>$4.50</td></tr>';
				lc += '<tr><td><div class="radio"><label><input type="radio" name="rb" id="ctl5" value="15">2 Beef Taquitos</label></div></td>';
				lc += '<td>$4.50</td></tr>';
				lc += '<tr><td><div class="radio"><label><input type="radio" name="rb" id="ctl16" value="16">2 Chicken Taquitos</label></div></td>';
				lc += '<td>$4.50</td></tr>';
				lc += '<tr><td><div class="checkbox"><label><input type="checkbox" name="chk" id="ctl17" value="17" disabled>Extra Cheese Quesadilla</label></div></td>';
				lc += '<td>$2.50</td></tr>';
				includes = 'Includes Chips and Mild Sauce';
			break;
			case 11:
				image = 'elmers';
				lc += '<tr><td><div class="radio"><label><input type="radio" name="rb" id="ctl18" value="18">Chicken Mini Burro</label></div></td>';
				lc += '<td>$4.50</td></tr>';
				lc += '<tr><td><div class="radio"><label><input type="radio" name="rb" id="ctl19" value="19">Machaca Mini Burro</label></div></td>';
				lc += '<td>$4.50</td></tr>';
				lc += '<tr><td><div class="radio"><label><input type="radio" name="rb" id="ctl20" value="20">Bean and Cheese Burrito</label></div></td>';
				lc += '<td>$4.50</td></tr>';
				lc += '<tr><td><div class="radio"><label><input type="radio" name="rb" id="ctl21" value="21">Three Chicken Taquitos</label></div></td>';
				lc += '<td>$4.50</td></tr>';
				lc += '<tr><td><div class="radio"><label><input type="radio" name="rb" id="ctl22" value="22">Three Beef Taquitos</label></div></td>';
				lc += '<td>$4.50</td></tr>';
				lc += '<tr><td><div class="radio"><label><input type="radio" name="rb" id="ctl23" value="23">Two Beef Fried Tacos</label></div></td>';
				lc += '<td>$4.50</td></tr>';
				lc += '<tr><td><div class="radio"><label><input type="radio" name="rb" id="ctl24" value="24">Two Chicken Fried Tacos</label></div></td>';
				lc += '<td>$4.50</td></tr>';
				includes = 'Includes Small Tortilla Strips and Salsa';
			break;
			
			default:
				window.open(event.url);
				return false;
			break;
		}
		
		var momOrderDate = moment(editLunchParams.orderDate);
		$('#lunchdate').html(momOrderDate.format("dddd, MMMM Do, YYYY"));
		$('#lunchimg').removeClass().addClass('provimgmodal '+image);
		$('#orderee').html(name);
		$('#lunchchoices').html(lc);
		$('#lunchincludes').html(includes);
			
		if (editLunchParams.orderID == 0) {
			$('#nlo').prop('checked', true);	
			$("input[type='checkbox']").removeAttr('checked').attr('disabled', 'disabled');
			editLunchParams.serializedForm = $('#lunchform').serialize();
			$('#modalOrderLunch').modal();  	
		} else {
			$.ajax({
				type: "GET",
				url: 'getorderdetails',
				data: 'userID='+editLunchParams.userID+'&dateYMD='+editLunchParams.orderDate
			}).done(function(ajaxResults) { //TODO may come back with nothing - order deleted!!!
                if (ajaxResults == '')
                    return;
                if (ajaxResults == 'error') {
                    location.reload();
                    return;
                }
				$("input[type='checkbox']").removeAttr('disabled');
				var obj = $.parseJSON(ajaxResults);
				$.each(obj, function(i) {
					$("#ctl"+obj[i].menuItemID).removeAttr('disabled').prop('checked', true);
				});
				editLunchParams.serializedForm = $('#lunchform').serialize();  //this is to check for changes & need to save
				$('#modalOrderLunch').modal();
			});
		}
		return false;
	});

	$(document).on("click touchstart", "#modalOrderLunch #btnOK", function(e) {
		e.preventDefault;
		$('#modalOrderLunch').modal('hide');
		var editedSerialized = $('#lunchform').serialize();
		if (editLunchParams.serializedForm != editedSerialized) {
			blockUI = true;
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
        	var params = editedSerialized+
				'&dateYMD='+editLunchParams.orderDate+
				'&userID='+editLunchParams.userID+
				'&orderID='+editLunchParams.orderID+
				'&account_id='+$('#accountnames option:selected').val()+
				'&startDateYMD='+momStartPeriod.format("YYYY-MM-DD")+
				'&endDateYMD='+momEndPeriod.format("YYYY-MM-DD");
			$.ajax({
				type: "POST",
				url: 'saveorder',
				data: params
			})
			.done(function(ajaxResults) {
                if (ajaxResults == '')
                    return;
                if (ajaxResults == 'error') {
                    location.reload();
                    return;
                }
				var obj = $.parseJSON(ajaxResults);
				$(editLunchParams.clickedUserLunchDate).html(obj[0]);
				$(editLunchParams.clickedUserLunchDate).data("orderid",obj[1]);
				if ($("#balance").length > 0)
					$("#balance").html(obj[2]);
				$('#lunchgallery').css('height', $('.lunchslide:first').height());
				$.unblockUI();
				blockUI = false;
			});
		}
		return false;
	});
	
	$(document).on("change", "#lunchform input[type='radio']", function(e) {
		var selection=parseInt($(this).val());
		if (selection == 0)
			$("input[type='checkbox']").removeAttr('checked').attr('disabled', 'disabled');
		else
			$("input[type='checkbox']").removeAttr('disabled');
	});
	
	/******************************************/

    $(document).on("click touchstart", ".todaytext", function(e) {
		e.preventDefault();
		if (blockUI)
			return false;
		if (curPeriodNo != 0) {
    		getLunchesTable(-curPeriodNo);
    	}
    	return false;
	});
	
	$(document).on("change", "#accountnames", function() {getLunchesTable(-curPeriodNo);});
	$(document).on("click touchstart", ".navbtn.prev", function() {getLunchesTable(-1);});
	$(document).on("click touchstart", ".navbtn.next", function() {getLunchesTable(1);});
	$(document).on("mouseover", "#lunchestable td.editable", function() {$(this).addClass('tdbkg');});
	$(document).on("mouseout", "#lunchestable td.editable", function() {$(this).removeClass('tdbkg');});
	
	function displayPeriodDesc(scrollDir) {
		var periodDesc = '';
		switch (curPeriodNo) {
			case 0: periodDesc = 'This '+displayPeriod;break;
			case 1: periodDesc = 'Next '+displayPeriod;break;
			case -1: periodDesc = 'Last '+displayPeriod;break;
			default: 
				if (curPeriodNo > 1)
					periodDesc = curPeriodNo+' '+displayPeriod+'s from now';
				else 
					periodDesc =  Math.abs(curPeriodNo)+' '+displayPeriod+'s ago';
			break;
		}	
		$("#weekdesc").html(periodDesc);
		if (scrollDir != 0)
			$("#weekdesc").fadeIn('fast');
		 $('.navbtn').prop('disabled', false);
		 blockUI = false;	
	}
	function updateLunchesTable(scrollDir,ajaxResults) {
		if (scrollDir == 0) {
			$("#lunchslider").html(ajaxResults);
			$('#lunchgallery').css('height', $('.lunchslide:first').height());
			displayPeriodDesc(scrollDir);
		} else {
			$("#weekdesc").fadeOut('fast');
			if (scrollDir < 0) {
				$("#lunchslider").prepend(ajaxResults).stop(1);
				$('#lunchgallery').css('height', $('.lunchslide:first').height());
				$("#lunchslider").css("left",-960).animate({left: 0},500, function() {
					$(".lunchslide:last").remove();
					displayPeriodDesc(scrollDir);
				});
			} else {
				$("#lunchslider").append(ajaxResults).stop(1);
				$('#lunchgallery').css('height', $('.lunchslide:last').height());
				$("#lunchslider").animate({left: -960},500, function() {
					$(".lunchslide:first").remove();
					$("#lunchslider").css("left",0);
					displayPeriodDesc(scrollDir);
				});
			}
		}
	}
	function getLunchesTable(offset) {
		if (blockUI) return; 
		blockUI = true;
		$('#loadingtext').removeClass('hide');
		$('#loadinggif').removeClass('hide');
		 $('.navbtn').prop('disabled', true);
		
		momStartPeriod.add(displayPeriodShort,offset);
		momEndPeriod.add(displayPeriodShort,offset); 
		curPeriodNo += offset;
		
		var d = 'account_id='+$('#accountnames option:selected').val()+
			'&startDateYMD='+momStartPeriod.format("YYYY-MM-DD")+
			'&endDateYMD='+momEndPeriod.format("YYYY-MM-DD")+
			'&displayPeriod='+displayPeriod;
			
		$.ajax({
			type: "GET",
			url: 'getlunchestable',
			data: d
		})
		.done(function(ajaxResults) {
			$('#loadingtext').addClass('hide');
			$('#loadinggif').addClass('hide');
            if (ajaxResults == 'error') {
                location.reload();
            } else
			    updateLunchesTable(offset,ajaxResults);
		});
	}
	function doInit() {
		if ($("#local_tzo").length > 0) {
			$("#local_tzo").val(new Date().getTimezoneOffset()/60);
		}
		if ($("#accountnames").length > 0) {
            $(".select2-container input").prop("readonly",true);
            $("#accountnames").select2({minimumResultsForSearch: -1});
        }

		if ($("#lunchgallery").length > 0) {
			$('#lunchgallery').css('height', $('.lunchslide').height());
		}
		/*if ($("#lunchgallery").length > 0) {
			//if today is Sat, advance; curPeriodNo = 0
			if (momToday.day() == 6) {
				momStartPeriod.add(displayPeriodShort,1);
				momEndPeriod.add(displayPeriodShort,1); 
			}
			$.ajax({
				type: "GET",
				url: 'getaccountid'
			})
			.success(function(account_id) {
				$("#accountnames").select2("val", account_id);
				//getLunchesTable(0);
				displayPeriodDesc(0);
				$('#lunchgallery').css('height', $('.lunchslide').height());
			});
		}*/
	}	
	doInit();
});