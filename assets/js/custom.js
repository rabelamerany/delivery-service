/**
 * Resize function without multiple trigger
 *
 * Usage:
 * $(window).smartresize(function(){
 *     // code here
 * });
 */
(function ($, sr) {
	// debouncing function from John Hann
	// http://unscriptable.com/index.php/2009/03/20/debouncing-javascript-methods/
	var debounce = function (func, threshold, execAsap) {
		var timeout;

		return function debounced() {
			var obj = this, args = arguments;

			function delayed() {
				if (!execAsap)
					func.apply(obj, args);
				timeout = null;
			}

			if (timeout)
				clearTimeout(timeout);
			else if (execAsap)
				func.apply(obj, args);

			timeout = setTimeout(delayed, threshold || 100);
		};
	};

	// smartresize
	jQuery.fn[sr] = function (fn) {
		return fn ? this.bind('resize', debounce(fn)) : this.trigger(sr);
	};

})(jQuery, 'smartresize');
/**
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

var CURRENT_URL = window.location.href.split('#')[0].split('?')[0],
	$BODY = $('body'),
	$MENU_TOGGLE = $('#menu_toggle'),
	$SIDEBAR_MENU = $('#sidebar-menu'),
	$SIDEBAR_FOOTER = $('.sidebar-footer'),
	$LEFT_COL = $('.left_col'),
	$RIGHT_COL = $('.right_col'),
	$NAV_MENU = $('.nav_menu'),
	$FOOTER = $('footer');

var chart_settings = {
	series: {
		lines: {
			show: false,
			fill: true
		},
		splines: {
			show: true,
			tension: 0.4,
			lineWidth: 1,
			fill: 0.4
		},
		points: {
			radius: 0,
			show: true
		},
		shadowSize: 2
	},
	grid: {
		verticalLines: true,
		hoverable: true,
		clickable: true,
		tickColor: "#d5d5d5",
		borderWidth: 1,
		color: '#fff'
	},
	colors: ["rgba(38, 185, 154, 0.38)", "rgba(3, 88, 106, 0.38)"],
	xaxis: {
		tickColor: "rgba(51, 51, 51, 0.06)",
		mode: "time",
		tickSize: [1, "day"],
		//tickLength: 10,
		axisLabel: "Date",
		axisLabelUseCanvas: true,
//        axisLabelFontSizePixels: 10,
//        axisLabelFontFamily: 'Verdana, Arial',
//        axisLabelPadding: 5
	},
	yaxis: {
		ticks: 8,
		tickColor: "rgba(51, 51, 51, 0.06)",
		minTickSize: 1,
		tickFormatter: function (val, axis) {
			return val;
		}
	},
	tooltip: true
};


// Sidebar
function init_sidebar() {
// TODO: This is some kind of easy fix, maybe we can improve this
	var setContentHeight = function () {
		// reset height
		$RIGHT_COL.css('min-height', $(window).height());

		var bodyHeight = $BODY.outerHeight(),
			footerHeight = $BODY.hasClass('footer_fixed') ? -10 : $FOOTER.height(),
			leftColHeight = $LEFT_COL.eq(1).height() + $SIDEBAR_FOOTER.height(),
			contentHeight = bodyHeight < leftColHeight ? leftColHeight : bodyHeight;

		// normalize content
		contentHeight -= $NAV_MENU.height() + footerHeight;

		$RIGHT_COL.css('min-height', contentHeight);
	};

	$SIDEBAR_MENU.find('a').on('click', function (ev) {
		var $li = $(this).parent();

		if ($li.is('.active')) {
			$li.removeClass('active active-sm');
			$('ul:first', $li).slideUp(function () {
				setContentHeight();
			});
		} else {
			// prevent closing menu if we are on child menu
			if (!$li.parent().is('.child_menu')) {
				$SIDEBAR_MENU.find('li').removeClass('active active-sm');
				$SIDEBAR_MENU.find('li ul').slideUp();
			} else {
				if ($BODY.is(".nav-sm")) {
					$SIDEBAR_MENU.find("li").removeClass("active active-sm");
					$SIDEBAR_MENU.find("li ul").slideUp();
				}
			}
			$li.addClass('active');

			$('ul:first', $li).slideDown(function () {
				setContentHeight();
			});
		}
	});

// toggle small or large menu
	$MENU_TOGGLE.on('click', function () {

		if ($BODY.hasClass('nav-md')) {
			$SIDEBAR_MENU.find('li.active ul').hide();
			$SIDEBAR_MENU.find('li.active').addClass('active-sm').removeClass('active');
		} else {
			$SIDEBAR_MENU.find('li.active-sm ul').show();
			$SIDEBAR_MENU.find('li.active-sm').addClass('active').removeClass('active-sm');
		}

		$BODY.toggleClass('nav-md nav-sm');

		setContentHeight();

		$('.dataTable').each(function () {
			$(this).dataTable().fnDraw();
		});
	});

	// check active menu
	$SIDEBAR_MENU.find('a[href="' + CURRENT_URL + '"]').parent('li').addClass('current-page');

	$SIDEBAR_MENU.find('a').filter(function () {
		return this.href == CURRENT_URL;
	}).parent('li').addClass('current-page').parents('ul').slideDown(function () {
		setContentHeight();
	}).parent().addClass('active');

	// recompute content when resizing
	$(window).smartresize(function () {
		setContentHeight();
	});

	setContentHeight();

	// fixed sidebar
	if ($.fn.mCustomScrollbar) {
		$('.menu_fixed').mCustomScrollbar({
			autoHideScrollbar: true,
			theme: 'minimal',
			mouseWheel: {preventDefault: true}
		});
	}
};
// /Sidebar


// Panel toolbox
$(document).ready(function () {
	$('.collapse-link').on('click', function () {
		var $BOX_PANEL = $(this).closest('.x_panel'),
			$ICON = $(this).find('i'),
			$BOX_CONTENT = $BOX_PANEL.find('.x_content');

		// fix for some div with hardcoded fix class
		if ($BOX_PANEL.attr('style')) {
			$BOX_CONTENT.slideToggle(200, function () {
				$BOX_PANEL.removeAttr('style');
			});
		} else {
			$BOX_CONTENT.slideToggle(200);
			$BOX_PANEL.css('height', 'auto');
		}

		$ICON.toggleClass('fa-chevron-up fa-chevron-down');
	});

	$('.close-link').click(function () {
		var $BOX_PANEL = $(this).closest('.x_panel');

		$BOX_PANEL.remove();
	});
});
// /Panel toolbox


// Progressbar
if ($(".progress .progress-bar")[0]) {
	$('.progress .progress-bar').progressbar();
}
// /Progressbar

// Switchery
$(document).ready(function () {
	if ($(".js-switch")[0]) {
		var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));
		elems.forEach(function (html) {
			var switchery = new Switchery(html, {
				color: '#26B99A'
			});
		});
	}
});
// /Switchery


// iCheck
$(document).ready(function () {
	if ($("input.flat")[0]) {
		$(document).ready(function () {
			$('input.flat').iCheck({
				checkboxClass: 'icheckbox_flat-green',
				radioClass: 'iradio_flat-green'
			});
		});
	}
});
// /iCheck

// Table
$('table input').on('ifChecked', function () {
	checkState = '';
	$(this).parent().parent().parent().addClass('selected');
	countChecked();
});
$('table input').on('ifUnchecked', function () {
	checkState = '';
	$(this).parent().parent().parent().removeClass('selected');
	countChecked();
});

var checkState = '';

$('.bulk_action input').on('ifChecked', function () {
	checkState = '';
	$(this).parent().parent().parent().addClass('selected');
	countChecked();
});
$('.bulk_action input').on('ifUnchecked', function () {
	checkState = '';
	$(this).parent().parent().parent().removeClass('selected');
	countChecked();
});
$('.bulk_action input#check-all').on('ifChecked', function () {
	checkState = 'all';
	countChecked();
});
$('.bulk_action input#check-all').on('ifUnchecked', function () {
	checkState = 'none';
	countChecked();
});

// Accordion
$(document).ready(function () {
	$(".expand").on("click", function () {
		$(this).next().slideToggle(200);
		$expand = $(this).find(">:first-child");

		if ($expand.text() == "+") {
			$expand.text("-");
		} else {
			$expand.text("+");
		}
	});
});

// NProgress
if (typeof NProgress != 'undefined') {
	$(document).ready(function () {
		NProgress.start();
	});

	$(window).load(function () {
		NProgress.done();
	});
}


//hover and retain popover when on popover content
var originalLeave = $.fn.popover.Constructor.prototype.leave;
$.fn.popover.Constructor.prototype.leave = function (obj) {
	var self = obj instanceof this.constructor ?
		obj : $(obj.currentTarget)[this.type](this.getDelegateOptions()).data('bs.' + this.type);
	var container, timeout;

	originalLeave.call(this, obj);

	if (obj.currentTarget) {
		container = $(obj.currentTarget).siblings('.popover');
		timeout = self.timeout;
		container.one('mouseenter', function () {
			//We entered the actual popover – call off the dogs
			clearTimeout(timeout);
			//Let's monitor popover content instead
			container.one('mouseleave', function () {
				$.fn.popover.Constructor.prototype.leave.call(self, self);
			});
		});
	}
};

$('body').popover({
	selector: '[data-popover]',
	trigger: 'click hover',
	delay: {
		show: 50,
		hide: 400
	}
});

function gd(year, month, day) {
	return new Date(year, month, day).getTime();
}

function dashboard(start, end, url) {
	if (url == null || url == undefined) {
		url = '/dashboard/orderActivities';
	}

	$.ajax({
		url: url,
		method: 'POST',
		data: {
			startDate: start.format('YYYY-M-D'),
			endDate: end.format('YYYY-M-D')
		}, success: function (response) {
			var data = [];
			for (var i = 0; i < response.data.chart.length; i++) {
				data.push([response.data.chart[i].day, response.data.chart[i].count]);
			}

			if ($('#chart_plot_01').length) {
				$.plot($('#chart_plot_01'), [data], chart_settings);
			}

			$('#top_dispatchers').empty();
			if (response.data.dispatcher !== undefined) {
				for (var j = 0; j < response.data.dispatcher.length; j++) {
					$('#top_dispatchers').append(
						'<div class="widget_summary">' +
						'<div class="w_left w_40">' +
						'<span>' + response.data.dispatcher[j].full_name + '</span>' +
						'</div>' +
						'<div class="w_center w_50">' +
						'<div class="progress">' +
						'<div class="progress-bar bg-green" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: ' + response.data.dispatcher[j].score + '%;">' +
						'<span class="sr-only">' + response.data.dispatcher[j].score + '% Complete</span>' +
						'</div>' +
						'</div>' +
						'</div>' +
						'<div class="w_right w_10"><span>' + response.data.dispatcher[j].count + '</span></div>' +
						'<div class="clearfix"></div>' +
						'</div>'
					);
				}
			}

			$('#top_drivers').empty();
			if (response.data.driver !== undefined) {
				for (var k = 0; k < 3 && k < response.data.driver.length; k++) {
					$('#top_drivers').append(
						'<div class="widget_summary">' +
						'<div class="w_left w_40">' +
						'<span>' + response.data.driver[k].full_name + '</span>' +
						'</div>' +
						'<div class="w_center w_50">' +
						'<div class="progress">' +
						'<div class="progress-bar bg-green" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: ' + response.data.driver[k].score + '%;">' +
						'<span class="sr-only">' + response.data.driver[k].score + '% Complete</span>' +
						'</div>' +
						'</div>' +
						'</div>' +
						'<div class="w_right w_10"><span>' + response.data.driver[k].count + '</span></div>' +
						'<div class="clearfix"></div>' +
						'</div>'
					);
				}
			}

			$('#top_products').empty();
			if (response.data.products !== undefined) {
				for (var m = 0; m < 3 && m < response.data.products.length; m++) {
					$('#top_products').append(
						'<div class="widget_summary">' +
						'<div class="w_left w_40">' +
						'<span>' + response.data.products[m].full_name + '</span>' +
						'</div>' +
						'<div class="w_right w_10"><span>' + response.data.products[m].count + '</span></div>' +
						'<div class="clearfix"></div>' +
						'</div>'
					);
				}
			}

			$('#label_doughnut').empty();
			var color = ["#BDC3C7", "#9B59B6", "#E74C3C", "#26B99A", "#3498DB"];
			var hColor = ["#CFD4D8", "#B370CF", "#E95E4F", "#36CAAB", "#49A9EA"];
			var label = [];
			data = [];
			var bgColor = [];
			var hBgColor = [];

			if (response.data.driver !== undefined) {
				for (var l = 0; l < response.data.driver.length; l++) {
					label.push(response.data.driver[l].full_name);
					data.push(response.data.driver[l].count);
					bgColor.push(color[l]);
					hBgColor.push(hColor[l]);
					$('#label_doughnut').append(
						'<tr>' +
						'<td>' +
						'<p><i class="fa fa-square" style="color: ' + color[l] + ';"></i>' + response.data.driver[l].full_name + '</p>' +
						'</td>' +
						'<td>' + response.data.driver[l].count + '</td>' +
						'</tr>'
					);
				}
			}

			var chart_doughnut_settings = {
				type: 'doughnut',
				tooltipFillColor: "rgba(51, 51, 51, 0.55)",
				data: {
					labels: label,
					datasets: [{
						data: data,
						backgroundColor: bgColor,
						hoverBackgroundColor: hBgColor
					}]
				},
				options: {
					legend: true,
					responsive: false
				}
			};

			if ($('#chart_doughnut').length) {
				var chart_doughnut = new Chart($('#chart_doughnut'), chart_doughnut_settings);
			}


			$('#activities').empty();
			if (response.data.activities !== undefined) {
				for (var m = 0; m < response.data.activities.length; m++) {
					$('#activities').append(
						'<li>' +
						'<div class="block">' +
						'<div class="block_content">' +
						'<h2 class="title">' +
						'<a href="/orders/index/read/' + response.data.activities[m].order + '">' +
						response.data.activities[m].title +
						'</a>&nbsp;-&nbsp;' +
						'<b><a href="/orders/index/edit/' + response.data.activities[m].order + '">[edit]</a></b>' +
						'</h2>' +
						'<div class="byline"><span>' + response.data.activities[m].time + '</span> minutes ago</div>' +
						'<p class="excerpt">' + response.data.activities[m].description + '</p>' +
						'</div>' +
						'</div>' +
						'</li>'
					);
				}
			} else {
				$('#activities').parent().parent().parent().addClass('fixed_height_320');
				$('#activities').parent().html('<p>No new orders, just relax.</p>')
			}

			$('#available_drivers_now').empty();
			if (response.data.available !== undefined) {
				for (var n = 0; n < response.data.available.length; n++) {
					$('#available_drivers_now').append(
						'<span class="label label-success">' + response.data.available[n].name + '</span>&nbsp;&nbsp;'
					);
				}
			}
		}
	});
}


/* DATERANGEPICKER */
function init_daterangepicker() {
	if (typeof ($.fn.daterangepicker) === 'undefined') {
		return;
	}

	var cb = function (start, end, label) {
		$('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
	};

	var optionSet1 = {
		startDate: moment().subtract(15, 'days'),
		endDate: moment(),
		minDate: '01/01/2017',
		maxDate: '12/31/2150',
		dateLimit: {
			days: 60
		},
		showDropdowns: true,
		showWeekNumbers: true,
		timePicker: false,
		timePickerIncrement: 1,
		timePicker12Hour: true,
		ranges: {
			'Today': [moment(), moment()],
			'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
			'Last 7 Days': [moment().subtract(6, 'days'), moment()],
			'Last 30 Days': [moment().subtract(29, 'days'), moment()],
			'This Month': [moment().startOf('month'), moment().endOf('month')],
			'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
		},
		opens: 'left',
		buttonClasses: ['btn btn-default'],
		applyClass: 'btn-small btn-primary',
		cancelClass: 'btn-small',
		format: 'MM/DD/YYYY',
		separator: ' to ',
		locale: {
			applyLabel: 'Submit',
			cancelLabel: 'Clear',
			fromLabel: 'From',
			toLabel: 'To',
			customRangeLabel: 'Custom',
			daysOfWeek: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'],
			monthNames: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
			firstDay: 1
		}
	};

	$('#reportrange span').html(moment().subtract(15, 'days').format('MMMM D, YYYY') + ' - ' + moment().format('MMMM D, YYYY'));
	$('#reportrange').daterangepicker(optionSet1, cb);
}

$(document).ready(function () {
	init_sidebar();
	init_daterangepicker();
});
