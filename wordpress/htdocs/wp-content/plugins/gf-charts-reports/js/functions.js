jQuery(document).ready(
	function($) {

	    console.log(window.maxicharts_reports);

	    window.maxicharts_reports = window.maxicharts_reports || {};
	    window.maxicharts_reports_init = window.maxicharts_reports_init || {};
	    var size = Object.keys(window.maxicharts_reports).length;
	    var initSize = Object.keys(window.maxicharts_reports_init).length;
	    //console.log("number of charts : "+size);
	    $.each(window.maxicharts_reports, function(index, value) {

		console.log(index + ' => ' + value);
		//console.log(document.getElementById(index));
		var ctx = document.getElementById(index).getContext("2d");
		console.log(value.type);

		var chartjsType = '';//value.type.toLowerCase();
		var chartData = value.data;
		//console.log(chartData);
		var chartOptions = value.options;
		//console.log(chartOptions);
		switch (value.type) {
		case 'doughnut':
		    //window.maxicharts_reports_init[ index ] = new Chart(ctx).Doughnut( value.data, value.options );
		    chartjsType = 'doughnut'
		    break;
		case 'PolarArea':
		    chartjsType = 'polarArea';
		    //window.maxicharts_reports_init[ index ] = new Chart(ctx).PolarArea( value.data, value.options );
		    break;
		case 'horizontalBar':
		    chartjsType = 'horizontalBar';
		    break;
		case 'bar':
		    //window.maxicharts_reports_init[ index ] = new Chart(ctx).Bar( value.data, value.options );
		    chartjsType = 'bar';
		    /*window.maxicharts_reports_init[ index ] = new Chart(ctx,{
		        type: 'bar',
		        data: chartData,
		        options: chartOptions,
		    });*/

		    break;
		case 'line':
		    chartjsType = 'line';
		    //window.maxicharts_reports_init[ index ] = new Chart(ctx).Line( value.data, value.options );
		    break;
		case 'Radar':
		    //window.maxicharts_reports_init[ index ] = new Chart(ctx).Radar( value.data, value.options );
		    chartjsType = 'radar';
		    break;
		default:
		    // window.maxicharts_reports_init[ index ] = new Chart(ctx).Pie(
		    // value.data, value.options );
		    chartjsType = 'pie';

		}

		if (ctx && ctx != null && chartjsType && chartData && chartOptions &&
			chartjsType != null && chartData  != null && chartOptions != null  &&
			typeof chartjsType !== 'undefined'
			&& typeof chartData !== 'undefined'
			&& typeof chartOptions !== 'undefined'
			    ) {
		    //console.log(index);
		    //console.log(window.maxicharts_reports_init);
		    try {
			window.maxicharts_reports_init[index] = new Chart(ctx, {
				type : chartjsType,
				data : chartData,
				options : chartOptions,
			    });
			}catch(err) {
			    console.error("Error processing "+index);
			}
		    
		}
	    });

	    // SmartResise
	    !function(a, b) {
		var c = function(a, b, c) {
		    var d;
		    return function() {
			function g() {
			    c || a.apply(e, f), d = null
			}
			var e = this, f = arguments;
			d ? clearTimeout(d) : c && a.apply(e, f),
				d = setTimeout(g, b || 100)
		    }
		};
		jQuery.fn[b] = function(a) {
		    return a ? this.bind("resize", c(a)) : this.trigger(b)
		}
	    }(jQuery, "smartresize");

	    // set Height to width.
	    function reSize(selector) {
		$(selector).each(function() {
		    var current = $(this);
		    var proportion = current.data('proportion');
		    var thisWidth = current.outerWidth();
		    current.css('height', (thisWidth / proportion));
		    current.parent().css('height', (thisWidth / proportion));
		});
	    }

	    // call on load
	    reSize('.maxicharts_reports_canvas');

	    // Call on debounced resize event
	    $(window).smartresize(function() {
		reSize('.maxicharts_reports_canvas');
	    });

	});