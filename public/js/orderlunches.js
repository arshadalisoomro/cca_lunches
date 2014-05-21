function getLunchIncludes(provider_id) {
	var includes = '';
	switch(provider_id) {
		case 4: includes = 'Includes Chips &amp; Salsa'; break;	
		//case 5: includes = '&nbsp;';break;
		case 6: includes = 'Includes Roll &amp; Butter'; break;
		case 7: includes = 'Includes Chips &amp; Milk'; break;
		//case 8: includes = '&nbsp;';break;
		case 9: includes = 'Includes Fries'; break;
		case 10: includes = 'Includes Chips and Mild Sauce'; break;
		case 11: includes = 'Includes Small Tortilla Strips and Salsa'; break;
		case 12: includes = 'Includes Chips &amp; Milk'; break;
	}
	return includes;
}
function getLunchChoices(provider_id) {
	var lc = '';
	switch(provider_id) {
		case 4:
			lc += '<div class="radio"><label><input type="radio" name="rb" id="ctl-6" value="6">Bean Burrito<span class="pull-right">$4.50</span></label></div>';
			lc += '<div class="radio"><label><input type="radio" name="rb" id="ctl-7" value="7">Cheese Quesadilla<span class="pull-right">$4.50</span></label></div>';
			lc += '<div class="checkbox"><label><input type="checkbox" name="chk" id="ctl-8" value="8" disabled>Extra Quesadilla<span class="pull-right">$2.50</span></label></div>';
			break;	
		case 5:
			lc += '<div class="radio"><label><input type="radio" name="rb" id="ctl-2" value="2">Honey Seared Chicken<span class="pull-right">$4.50</span></label></div>';
			lc += '<div class="radio"><label><input type="radio" name="rb" id="ctl-3" value="3">Teryaki Chicken<span class="pull-right">$4.50</span></label></div>';
			break;
		case 6:
			lc += '<div class="radio"><label><input type="radio" name="rb" id="ctl-1" value="1">Spaghetti<span class="pull-right">$4.50</span></label></div>';
			break;
		case 7:
			lc += '<div class="radio"><label><input type="radio" name="rb" id="ctl-9" value="9">6" Ham Sub<span class="pull-right">$4.50</span></label></div>';
			lc += '<div class="radio"><label><input type="radio" name="rb" id="ctl-10" value="10">6" Turkey Sub<span class="pull-right">$4.50</span></label></div>';
			break;
		case 8:
			lc += '<div class="radio"><label><input type="radio" name="rb" id="ctl-4" value="4">Cheese Pizza<span class="pull-right">$4.50</span></label></div>';
			lc += '<div class="radio"><label><input type="radio" name="rb" id="ctl-5" value="5">Pepperoni Pizza<span class="pull-right">$4.50</span></label></div>';
			break;
		case 9:	
			lc += '<div class="radio"><label><input type="radio" name="rb" id="ctl-11" value="11">6 Chicken Nuggets<span class="pull-right">$4.50</span></label></div>';
			lc += '<div class="checkbox"><label><input type="checkbox" name="chk" id="ctl-12" value="12" disabled>Extra 4 Chicken Nuggets<span class="pull-right">$2.00</span></label></div>';
			break;
		case 10:	
			lc += '<div class="radio"><label><input type="radio" name="rb" id="ctl-13" value="13">Mini Cheese Crisp<span class="pull-right">$4.50</span></label></div>';
			lc += '<div class="radio"><label><input type="radio" name="rb" id="ctl-14" value="14">Mini Bean Burro<span class="pull-right">$4.50</span></label></div>';
			lc += '<div class="radio"><label><input type="radio" name="rb" id="ctl-15" value="15">2 Beef Taquitos<span class="pull-right">$4.50</span></label></div>';
			lc += '<div class="radio"><label><input type="radio" name="rb" id="ctl-16" value="16">2 Chicken Taquitos<span class="pull-right">$4.50</span></label></div>';
			lc += '<div class="checkbox"><label><input type="checkbox" name="chk" id="ctl-17" value="17" disabled>Extra Cheese Quesadilla<span class="pull-right">$2.50</span></label></div>';
			break;
		case 11:	
			lc += '<div class="radio"><label><input type="radio" name="rb" id="ctl-18" value="18">Chicken Mini Burro<span class="pull-right">$4.50</span></label></div>';
			lc += '<div class="radio"><label><input type="radio" name="rb" id="ctl-19" value="19">Machaca Mini Burro<span class="pull-right">$4.50</span></label></div>';
			lc += '<div class="radio"><label><input type="radio" name="rb" id="ctl-20" value="20">Bean and Cheese Burrito<span class="pull-right">$4.50</span></label></div>';
			lc += '<div class="radio"><label><input type="radio" name="rb" id="ctl-21" value="21">Three Chicken Taquitos<span class="pull-right">$4.50</span></label></div>';
			lc += '<div class="radio"><label><input type="radio" name="rb" id="ctl-22" value="22">Three Beef Taquitos<span class="pull-right">$4.50</span></label></div>';
			lc += '<div class="radio"><label><input type="radio" name="rb" id="ctl-23" value="23">Two Beef Fried Tacos<span class="pull-right">$4.50</span></label></div>';
			lc += '<div class="radio"><label><input type="radio" name="rb" id="ctl-24" value="24">Two Chicken Fried Tacos<span class="pull-right">$4.50</span></label></div>';
			lc += '<div class="radio"><label><input type="radio" name="rb" id="ctl-27" value="27">Cheese Quesadilla<span class="pull-right">$4.50</span></label></div>';
			break;
		case 12:	
			lc += '<div class="radio"><label><input type="radio" name="rb" id="ctl-25" value="25">Ham Sub<span class="pull-right">$4.50</span></label></div>';
			lc += '<div class="radio"><label><input type="radio" name="rb" id="ctl-26" value="26">Turkey Sub<span class="pull-right">$4.50</span></label></div>';
			break;
	}
	return lc;
}
function myblockUI() {
	$('.ajaxloader').removeClass('invisible');
	//$('.ajaxactiontext').removeClass('hide');
	$('.blockui').removeClass('uienabled').addClass('uiblocked');
}
function myunblockUI() {
	$('.ajaxloader').addClass('invisible');
	//$('.ajaxactiontext').addClass('hide');
	$('.blockui').removeClass('uiblocked').addClass('uienabled');
}

$(document).ready(function() {
	"use strict";
	var $document = $(document),
		displayPeriod = 'week',
		displayPeriodShort = 'w',
		momToday = moment(),
		momStartPeriod = moment().startOf(displayPeriod),
		momEndPeriod = moment().endOf(displayPeriod),
		curPeriodNo = 0,
		scrollDir = 0,
		editLunchParams = null,
		spinopts = {
			lines: 13, 
			length: 15, 
			color: '#cc0000',
			shadow: true
		};
	var spinner = new Spinner(spinopts);
	
	$('#lunchCarousel').on('slid.bs.carousel', function () {
		var el = $('#lunchCarousel .item.active');
		if (scrollDir > 0)
			el.prev().remove();
		else 
			el.next().remove();
		myunblockUI();
		displayPeriodDesc();
	});
	/** **/
	function displayPeriodDesc() {
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
		$(".weekdesc").html(periodDesc);
	}
	/** **/
	function updateLunchesTable(ajaxResults) {
		var results = '<div class="item"><table id="lunchestable">'+ajaxResults+'</table></div>';
		if (scrollDir > 0) {
			$('.carousel-inner').append(results);
			$('.carousel').carousel('next');
		} else {
			$('.carousel-inner').prepend(results);
			$('.carousel').carousel('prev');
		}
	}
	/** **/
	function getLunchesTable(sDir,offset) {
		myblockUI();
		scrollDir = sDir;
		momStartPeriod.add(displayPeriodShort,offset);
		momEndPeriod.add(displayPeriodShort,offset); 
		curPeriodNo += offset;
	
		var d = 
			'account_id='+$('#accountnames').val()+
			'&startDateYMD='+momStartPeriod.format("YYYY-MM-DD")+
			'&endDateYMD='+momEndPeriod.format("YYYY-MM-DD")+
			'&displayPeriod='+displayPeriod;
		
		$.ajax({
			type: "GET",
			url: 'getlunchestable',
			data: d
		})
		.done(function(ajaxResults) {
			if ((ajaxResults.length == 0) || (ajaxResults == 'error'))
				location.reload();
			else
				updateLunchesTable(ajaxResults);
		});
	}
	/** **/
	$(document).on('click touchstart', '.clickable', function(e){
        e.stopPropagation();
        e.preventDefault();
		if (e.handled !== true) {
			var $this = $(this).parent();
			var el = $this.parent().children(':first');
			var provID = parseInt($this.data("provid"));
			
			editLunchParams=new Object();
			editLunchParams.userID = el.data('userid');
			editLunchParams.orderID = $this.data("orderid");
			editLunchParams.orderDate = $this.data("dateymd");
			editLunchParams.clickedCell = $this;
				
			var momOrderDate = moment(editLunchParams.orderDate);
			$('#lunchdate').html(momOrderDate.format("dddd, MMMM Do, YYYY"));
			
			var t = $('tr.providers').find('a[data-provimgid="'+provID+'"]').first();
			$('#lunchimg').html(t.parent().html());
			$('#orderee').html(el.data('username'));
			$('#lunchchoices').html(getLunchChoices(provID));
			$('#lunchincludes').html(getLunchIncludes(provID));
					
			if (editLunchParams.orderID == 0) {
				$('#nlo').prop('checked', true);	
				$("input[type='checkbox']").removeAttr('checked').attr('disabled', 'disabled');
				editLunchParams.serializedForm = $('#lunchform').serialize();
				$('#modalOrderLunch').modal('show');  	
			} else {
				$.ajax({
					type: "GET",
					url: 'getorderdetails',
					data: 'userID='+editLunchParams.userID+'&dateYMD='+editLunchParams.orderDate
				}).done(function(ajaxResults) { 
					if (ajaxResults == 'error') {
						location.reload();
						return;
					}
					if (ajaxResults.length == 0) { //thought we had an order, deleted.
						$('#nlo').prop('checked', true);	
						$("input[type='checkbox']").removeAttr('checked').attr('disabled', 'disabled');
						editLunchParams.serializedForm = $('#lunchform').serialize();
						$('#modalOrderLunch').modal('show');  	
						return;
					}
					
					$("input[type='checkbox']").removeAttr('disabled');
					$(jQuery.parseJSON(JSON.stringify(ajaxResults))).each(function() {
						$("#ctl-"+this.menuItemID).removeAttr('disabled').prop('checked', true);
					});
					editLunchParams.serializedForm = $('#lunchform').serialize();  //this is to check for changes & need to save
					$('#modalOrderLunch').modal('show');
				});
			}
		
			e.handled = true;
        } else {
            return false;
        }
	});
	/** **/
	$document.on("click", "#btnOrderLunchOK", function(e) {
		e.preventDefault;
		var editedSerialized = $('#lunchform').serialize();
		if (editLunchParams.serializedForm != editedSerialized) {
			var spintarget = document.getElementById('lunchCarousel');
			spinner.spin(spintarget);
			$('.btn').prop('disabled', true);
        	var params = editedSerialized+
				'&dateYMD='+editLunchParams.orderDate+
				'&userID='+editLunchParams.userID+
				'&orderID='+editLunchParams.orderID+
				'&account_id='+$('#accountnames').val()+
				'&startDateYMD='+momStartPeriod.format("YYYY-MM-DD")+
				'&endDateYMD='+momEndPeriod.format("YYYY-MM-DD");
			$.ajax({
				type: "POST",
				url: 'saveorder',
				data: params
			})
			.done(function(ajaxResults) {
                if ((ajaxResults.length == 0) || (ajaxResults == 'error')) {
                    location.reload();
                    return;
                }
				var obj = $(jQuery.parseJSON(JSON.stringify(ajaxResults)));
				$(editLunchParams.clickedCell).replaceWith(obj[0]);
				if (obj[1])
					$(".amountdue").html(obj[1]);
				
				$('.btn').prop('disabled', false);
				$('#modalOrderLunch').modal('hide');	
				spinner.stop();
			});
		} else
			$('#modalOrderLunch').modal('hide');
		return false;
	});
	/** **/
	$document.on("click", ".todaytext", function(e) {
		e.preventDefault();
		if (curPeriodNo > 0)
    		getLunchesTable(-1,-curPeriodNo);
		else if (curPeriodNo < 0)
			getLunchesTable(1,-curPeriodNo);
    	return false;
	});
	$document.on("change", "#accountnames", function() {getLunchesTable(1,-curPeriodNo);});
	$document.on("mouseover", "td.editable", function() {$(this).addClass('tdbkg');});//ipad hack
	$document.on("mouseout", "td.editable", function() {$(this).removeClass('tdbkg');});//ipad hack
	$document.on("click", ".navbtn.prev", function() {getLunchesTable(-1,-1);});
	$document.on("click", ".navbtn.next", function() {getLunchesTable(1,1);});
	$document.on("change", "#lunchform input[type='radio']", function(e) {
		var selection=parseInt($(this).val());
		if (selection == 0)
			$("input[type='checkbox']").removeAttr('checked').attr('disabled', 'disabled');
		else
			$("input[type='checkbox']").removeAttr('disabled');
	});
});