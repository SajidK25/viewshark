google.load("visualization", "1", {packages:["corechart"]});


jQuery(window).load(function(){
    jQuery(document).on({
	click: function () {
            var _sel = new Array;
	    jQuery(".this-week-filter").each(function(index, value){
                if (jQuery(this).is(":checked")) {
                    _sel[index] = jQuery(this).val();
                } else {
                    _sel[index] = '0';
                }
            });
            var f = $('#filter-section-sub a.active').attr('rel-t');

            myLineChart1.destroy();
            thisWeekdata = ((f == '' || f == 'week') ? thisWeek(_sel) : (f == 'month' ? thisMonth(_sel) : (f == 'year' ? thisYear(_sel) : [])));
            myLineChart1 = new Chart(makeCanvas('chart-1-container')).Bar(thisWeekdata);
            generateLegend('legend-1-container', thisWeekdata.datasets);
	}
    }, "#this-week-uploads");
});

jQuery(window).load(function(){
    jQuery(document).on({
	click: function () {
            var _sel = new Array;
	    jQuery(".last-week-filter").each(function(index, value){
                if (jQuery(this).is(":checked")) {
                    _sel[index] = jQuery(this).val();
                } else {
                    _sel[index] = '0';
                }
            });
            var f = $('#filter-section-sub a.active').attr('rel-t');

            myLineChart2.destroy();
            lastWeekdata = ((f == '' || f == 'week') ? lastWeek(_sel) : (f == 'month' ? lastMonth(_sel) : (f == 'year' ? lastYear(_sel) : [])));
            myLineChart2 = new Chart(makeCanvas('chart-2-container')).Bar(lastWeekdata);
            generateLegend('legend-2-container', lastWeekdata.datasets);
	}
    }, "#last-week-uploads");
});




function thisWeek(_sel) {
    var ds = [];
    _sel = _sel || '';

    _s = {
        label: "Earned Revenue",
        fillColor: "rgba(133, 187, 101,0.5)",
        strokeColor: "rgba(133, 187, 101,1)",
        pointColor: "rgba(133, 187, 101,1)",
        pointStrokeColor: "#fff",
        pointHighlightFill: "#fff",
        pointHighlightStroke: "rgba(133, 187, 101,1)",
        data: sw1
    };
    if (_sel == '' || _sel.indexOf("s") >= 0)
    	ds.push(_s);


    var data = {
        labels: generateWeekLabels("this"),
        datasets: ds
    };

    return data;
}

function thisMonth(_sel) {
    var ds = [];
    _sel = _sel || '';

    _s = {
        label: "Earned Revenue",
        fillColor: "rgba(133, 187, 101,0.5)",
        strokeColor: "rgba(133, 187, 101,1)",
        pointColor: "rgba(133, 187, 101,1)",
        pointStrokeColor: "#fff",
        pointHighlightFill: "#fff",
        pointHighlightStroke: "rgba(133, 187, 101,1)",
        data: sw1
    };
    if (_sel == '' || _sel.indexOf("s") >= 0)
    	ds.push(_s);

    var data = {
        labels: generateMonthLabels("this"),
        datasets: ds
    };

    return data;
}

function thisYear(_sel) {
    var ds = [];
    _sel = _sel || '';

    _s = {
        label: "Earned Revenue",
        fillColor: "rgba(133, 187, 101,0.5)",
        strokeColor: "rgba(133, 187, 101,1)",
        pointColor: "rgba(133, 187, 101,1)",
        pointStrokeColor: "#fff",
        pointHighlightFill: "#fff",
        pointHighlightStroke: "rgba(133, 187, 101,1)",
        data: sw1
    };
    if (_sel == '' || _sel.indexOf("s") >= 0)
    	ds.push(_s);

    var data = {
        labels: generateUserYearLabels("this"),
        datasets: ds
    };

    return data;
}

function thisWeek_subs(_sel) {
    _sel = _sel || '';
    
    //_e = (ecount["total"] !== '' && (_sel == '' || _sel.indexOf("e") >= 0)) ? {
    _e = (_sel == '' || _sel.indexOf("e") >= 0) ? {
        label: "Subscriptions",
        fillColor: "rgba(133, 187, 101,0.5)",
        strokeColor: "rgba(133, 187, 101,1)",
        pointColor: "rgba(133, 187, 101,1)",
        pointStrokeColor: "#fff",
        pointHighlightFill: "#fff",
        pointHighlightStroke: "rgba(133, 187, 101,1)",
        data: tws
    } : {};

    var data = {
        labels: generateWeekLabels("this"),
        datasets: [
            _e
        ]
    };

    return data;
}

function thisMonth_subs(_sel) {
    _sel = _sel || '';
    
    //_e = (ecount["total"] !== '' && (_sel == '' || _sel.indexOf("e") >= 0)) ? {
    _e = (_sel == '' || _sel.indexOf("e") >= 0) ? {
        label: "Subscriptions",
        fillColor: "rgba(133, 187, 101,0.5)",
        strokeColor: "rgba(133, 187, 101,1)",
        pointColor: "rgba(133, 187, 101,1)",
        pointStrokeColor: "#fff",
        pointHighlightFill: "#fff",
        pointHighlightStroke: "rgba(133, 187, 101,1)",
        data: tws
    } : {};

    var data = {
        labels: generateMonthLabels("this"),
        datasets: [
            _e
        ]
    };

    return data;
}

function thisYear_subs(_sel) {
    _sel = _sel || '';
    
    //_e = (ecount["total"] !== '' && (_sel == '' || _sel.indexOf("e") >= 0)) ? {
    _e = (_sel == '' || _sel.indexOf("e") >= 0) ? {
        label: "Subscriptions",
        fillColor: "rgba(133, 187, 101,0.5)",
        strokeColor: "rgba(133, 187, 101,1)",
        pointColor: "rgba(133, 187, 101,1)",
        pointStrokeColor: "#fff",
        pointHighlightFill: "#fff",
        pointHighlightStroke: "rgba(133, 187, 101,1)",
        data: tws
    } : {};

    var data = {
        labels: generateUserYearLabels("this"),
        datasets: [
            _e
        ]
    };

    return data;
}

function lastWeek(_sel) {
    var ds = [];
    _sel = _sel || '';

    _s = {
        label: "Earned Revenue",
        fillColor: "rgba(133, 187, 101,0.5)",
        strokeColor: "rgba(133, 187, 101,1)",
        pointColor: "rgba(133, 187, 101,1)",
        pointStrokeColor: "#fff",
        pointHighlightFill: "#fff",
        pointHighlightStroke: "rgba(133, 187, 101,1)",
        data: sw2
    };
    if (_sel == '' || _sel.indexOf("s") >= 0)
    	ds.push(_s);

    var data = {
        labels: generateWeekLabels("last"),
        datasets: ds
    };

    return data;
}
function lastMonth(_sel) {
    var ds = [];
    _sel = _sel || '';

    _s = {
        label: "Earned Revenue",
        fillColor: "rgba(133, 187, 101,0.5)",
        strokeColor: "rgba(133, 187, 101,1)",
        pointColor: "rgba(133, 187, 101,1)",
        pointStrokeColor: "#fff",
        pointHighlightFill: "#fff",
        pointHighlightStroke: "rgba(133, 187, 101,1)",
        data: sw2
    };
    if (_sel == '' || _sel.indexOf("s") >= 0)
    	ds.push(_s);

    var data = {
        labels: generateMonthLabels("last"),
        datasets: ds
    };

    return data;
}

function lastYear(_sel) {
    var ds = [];
    _sel = _sel || '';

    _s = {
        label: "Earned Revenue",
        fillColor: "rgba(133, 187, 101,0.5)",
        strokeColor: "rgba(133, 187, 101,1)",
        pointColor: "rgba(133, 187, 101,1)",
        pointStrokeColor: "#fff",
        pointHighlightFill: "#fff",
        pointHighlightStroke: "rgba(133, 187, 101,1)",
        data: sw2
    };
    if (_sel == '' || _sel.indexOf("s") >= 0)
    	ds.push(_s);

    var data = {
        labels: generateUserYearLabels("last"),
        datasets: ds
    };

    return data;
}

function lastWeek_subs(_sel) {
    _sel = _sel || '';
    
    _e = (ecount["total"] !== '' && (_sel == '' || _sel.indexOf("e") >= 0)) ? {
        label: "Subscriptions",
        fillColor: "rgba(133, 187, 101,0.5)",
        strokeColor: "rgba(133, 187, 101,1)",
        pointColor: "rgba(133, 187, 101,1)",
        pointStrokeColor: "#fff",
        pointHighlightFill: "#fff",
        pointHighlightStroke: "rgba(133, 187, 101,1)",
        data: lws
    } : {};

    var data = {
        labels: generateWeekLabels("last"),
        datasets: [
            _e
        ]
    };

    return data;
}

function lastMonth_subs(_sel) {
    _sel = _sel || '';
    
    _e = (ecount["total"] !== '' && (_sel == '' || _sel.indexOf("e") >= 0)) ? {
        label: "Subscriptions",
        fillColor: "rgba(133, 187, 101,0.5)",
        strokeColor: "rgba(133, 187, 101,1)",
        pointColor: "rgba(133, 187, 101,1)",
        pointStrokeColor: "#fff",
        pointHighlightFill: "#fff",
        pointHighlightStroke: "rgba(133, 187, 101,1)",
        data: lws
    } : {};

    var data = {
        labels: generateMonthLabels("last"),
        datasets: [
            _e
        ]
    };

    return data;
}

function lastYear_subs(_sel) {
    _sel = _sel || '';
    
    _e = (ecount["total"] !== '' && (_sel == '' || _sel.indexOf("e") >= 0)) ? {
        label: "Subscriptions",
        fillColor: "rgba(133, 187, 101,0.5)",
        strokeColor: "rgba(133, 187, 101,1)",
        pointColor: "rgba(133, 187, 101,1)",
        pointStrokeColor: "#fff",
        pointHighlightFill: "#fff",
        pointHighlightStroke: "rgba(133, 187, 101,1)",
        data: lws
    } : {};

    var data = {
        labels: generateUserYearLabels("last"),
        datasets: [
            _e
        ]
    };

    return data;
}


function memberData() {
    // Some raw data (not necessarily accurate)
    var data = {
        labels: generateUserWeekLabels(),
        datasets: [
            {
                label: "This Week",
                fillColor: "rgba(133, 187, 101,0.2)",
                strokeColor: "rgba(133, 187, 101,1)",
                pointColor: "rgba(133, 187, 101,1)",
                pointStrokeColor: "#fff",
                pointHighlightFill: "#fff",
                pointHighlightStroke: "rgba(133, 187, 101,1)",
                data: this_week_users.split(",")
            },
            {
                label: "Last Week",
                fillColor: "rgba(155, 101, 187,0.2)",
                strokeColor: "rgba(155, 101, 187,1)",
                pointColor: "rgba(155, 101, 187,1)",
                pointStrokeColor: "#fff",
                pointHighlightFill: "#fff",
                pointHighlightStroke: "rgba(155, 101, 187,1)",
                data: last_week_users.split(",")
            }
        ]
    };

    return data;
}

function userData() {
    // Some raw data (not necessarily accurate)
    var data = {
        labels: generateUserYearLabels(),
        datasets: [
            {
                label: "This Year",
                fillColor: "rgba(133, 187, 101,0.2)",
                strokeColor: "rgba(133, 187, 101,1)",
                pointColor: "rgba(133, 187, 101,1)",
                pointStrokeColor: "#fff",
                pointHighlightFill: "#fff",
                pointHighlightStroke: "rgba(133, 187, 101,1)",
                data: this_year_earnings.split(",")
            },
            {
                label: "Last Year",
                fillColor: "rgba(155, 101, 187,0.2)",
                strokeColor: "rgba(155, 101, 187,1)",
                pointColor: "rgba(155, 101, 187,1)",
                pointStrokeColor: "#fff",
                pointHighlightFill: "#fff",
                pointHighlightStroke: "rgba(155, 101, 187,1)",
                data: last_year_earnings.split(",")
            }
        ]
    };

    return data;
}

Chart.defaults.global.animation = true;
Chart.defaults.global.animationSteps = 60;
Chart.defaults.global.animationEasing = 'easeInOutQuart';
Chart.defaults.global.responsive = true;
Chart.defaults.global.maintainAspectRatio = false;

function drawChart1() {
    var f = $('#filter-section-sub a.active').attr('rel-t');
    var thisWeekdata = ((f == '' || f == 'week') ? thisWeek() : (f == 'month' ? thisMonth() : (f == 'year' ? thisYear() : [])));
    var myLineChart1 = new Chart(makeCanvas('chart-1-container')).Bar(thisWeekdata);
    generateLegend('legend-1-container', thisWeekdata.datasets);

    return myLineChart1;
}
var myLineChart1 = drawChart1();

function drawChart1a() {
    var f = $('#filter-section-sub a.active').attr('rel-t');
    var thisWeekdata = ((f == '' || f == 'week') ? thisWeek_subs() : (f == 'month' ? thisMonth_subs() : (f == 'year' ? thisYear_subs() : [])));
    var myLineChart1a = new Chart(makeCanvas('chart-1a-container')).Line(thisWeekdata);
    generateLegend('legend-1a-container', thisWeekdata.datasets);

    return myLineChart1a;
}
var myLineChart1a = drawChart1a();

function drawChart2() {
    var f = $('#filter-section-sub a.active').attr('rel-t');
    var lastWeekdata = ((f == '' || f == 'week') ? lastWeek() : (f == 'month' ? lastMonth() : (f == 'year' ? lastYear() : [])));
    var myLineChart2 = new Chart(makeCanvas('chart-2-container')).Bar(lastWeekdata);
    generateLegend('legend-2-container', lastWeekdata.datasets);

    return myLineChart2;
}
var myLineChart2 = drawChart2();

function drawChart2a() {
    var f = $('#filter-section-sub a.active').attr('rel-t');
    var lastWeekdata = ((f == '' || f == 'week') ? lastWeek_subs() : (f == 'month' ? lastMonth_subs() : (f == 'year' ? lastYear_subs() : [])));
    var myLineChart2a = new Chart(makeCanvas('chart-2a-container')).Line(lastWeekdata);
    generateLegend('legend-2a-container', lastWeekdata.datasets);

    return myLineChart2a;
}
var myLineChart2a = drawChart2a();

/*
function drawChart3() {
    if (twcount["total"] !== '') {
        var vStatsdata = twStats();
        var myLineChart3 = new Chart(makeCanvas('chart-3-container')).Doughnut(vStatsdata);
        generateLegend('legend-3-container', vStatsdata);
        
        return myLineChart3;
    }
}
var myLineChart3 = drawChart3();
*/
function twStats() {
  // Some raw data (not necessarily accurate)
  var data = [
    {
        value: twcount["total"],
        color:"rgb(155,101,187,0.5)",
        highlight: "rgb(130,73,164,0.5)",
        label: "Total"
    },
    {
        value: twcount["shared"],
        color: "rgb(101,133,187,0.5)",
        highlight: "rgb(73,107,164, 0.5)",
        label: "Shared"
    },
    {
        value: twcount["earned"],
        color: "rgb(133,187,101,0.5)",
        highlight: "rgb(107,164,73,0.5)",
        label: "Earned"
    },
];

return data;
}


/*
function drawChart3a() {
    if (lwcount["total"] !== '') {
        var vStatsdata = lwStats();
        var myLineChart3a = new Chart(makeCanvas('chart-3a-container')).Doughnut(vStatsdata);
        generateLegend('legend-3a-container', vStatsdata);
        
        return myLineChart3a;
    }
}
var myLineChart3a = drawChart3a();
*/
function lwStats() {
  // Some raw data (not necessarily accurate)
  var data = [
    {
        value: lwcount["total"],
        color:"rgb(155,101,187,0.5)",
        highlight: "rgb(130,73,164,0.5)",
        label: "Total"
    },
    {
        value: lwcount["shared"],
        color: "rgb(101,133,187,0.5)",
        highlight: "rgb(73,107,164, 0.5)",
        label: "Shared"
    },
    {
        value: lwcount["earned"],
        color: "rgb(133,187,101,0.5)",
        highlight: "rgb(107,164,73,0.5)",
        label: "Earned"
    },
];

return data;
}



/*
function drawChart3a() {
    if (vcount["total"] !== '') {
        var vStatsdata = vStats_featured();
        var myLineChart3a = new Chart(makeCanvas('chart-3a-container')).Doughnut(vStatsdata);
        generateLegend('legend-3a-container', vStatsdata);
        
        return myLineChart3a;
    }
}
var myLineChart3a = drawChart3a();

function drawChart11() {
    if (lcount["total"] !== '') {
        var lStatsdata = lStats_active();
        var myLineChart11 = new Chart(makeCanvas('chart-11-container')).Doughnut(lStatsdata);
        generateLegend('legend-11-container', lStatsdata);
        
        return myLineChart11;
    }
}
var myLineChart11 = drawChart11();

function drawChart11a() {
    if (lcount["total"] !== '') {
        var lStatsdata = lStats_featured();
        var myLineChart11a = new Chart(makeCanvas('chart-11a-container')).Doughnut(lStatsdata);
        generateLegend('legend-11a-container', lStatsdata);
        
        return myLineChart11a;
    }
}
var myLineChart11a = drawChart11a();

function drawChart4() {
    var memberNrData = memberData();
    var myLineChart4 = new Chart(makeCanvas('chart-4-container')).Bar(memberNrData);
    generateLegend('legend-4-container', memberNrData.datasets);
    
    return myLineChart4;
}
var myLineChart4 = drawChart4();

function drawChart8() {
    var userNrData = userData();
    var myLineChart8 = new Chart(makeCanvas('chart-8-container')).Bar(userNrData);
    generateLegend('legend-8-container', userNrData.datasets);
    
    return myLineChart8;
}
var myLineChart8 = drawChart8();

function drawChart5() {
    if (acount["total"] !== '') {
        var aNrStats = aStats_active();
        var myLineChart5 = new Chart(makeCanvas('chart-5-container')).Doughnut(aNrStats);
        generateLegend('legend-5-container', aNrStats);
        
        return myLineChart5;
    }
}
var myLineChart5 = drawChart5();

function drawChart5a() {
    if (acount["total"] !== '') {
        var aNrStats = aStats_featured();
        var myLineChart5a = new Chart(makeCanvas('chart-5a-container')).Doughnut(aNrStats);
        generateLegend('legend-5a-container', aNrStats);
        
        return myLineChart5a;
    }
}
var myLineChart5a = drawChart5a();

function drawChart6() {
    if (icount["total"] !== '') {
        var iNrStats = iStats_active();
        var myLineChart6 = new Chart(makeCanvas('chart-6-container')).Doughnut(iNrStats);
        generateLegend('legend-6-container', iNrStats);
        
        return myLineChart6;
    }
}
var myLineChart6 = drawChart6();

function drawChart6a() {
    if (icount["total"] !== '') {
        var iNrStats = iStats_featured();
        var myLineChart6a = new Chart(makeCanvas('chart-6a-container')).Doughnut(iNrStats);
        generateLegend('legend-6a-container', iNrStats);
        
        return myLineChart6a;
    }
}
var myLineChart6a = drawChart6a();


function drawChart7() {
    if (dcount["total"] !== '') {
        var dNrStats = dStats_active();
        var myLineChart7 = new Chart(makeCanvas('chart-7-container')).Doughnut(dNrStats);
        generateLegend('legend-7-container', dNrStats);
        
        return myLineChart7;
    }
}
var myLineChart7 = drawChart7();

function drawChart7a() {
    if (dcount["total"] !== '') {
        var dNrStats = dStats_featured();
        var myLineChart7a = new Chart(makeCanvas('chart-7a-container')).Doughnut(dNrStats);
        generateLegend('legend-7a-container', dNrStats);
        
        return myLineChart7a;
    }
}
var myLineChart7a = drawChart7a();


function drawChart9() {
    if (bcount["total"] !== '') {
        var bNrStats = bStats_active();
        var myLineChart9 = new Chart(makeCanvas('chart-9-container')).Doughnut(bNrStats);
        generateLegend('legend-9-container', bNrStats);
        
        return myLineChart9;
    }
}
var myLineChart9 = drawChart9();

function drawChart9a() {
    if (bcount["total"] !== '') {
        var bNrStats = bStats_featured();
        var myLineChart9a = new Chart(makeCanvas('chart-9a-container')).Doughnut(bNrStats);
        generateLegend('legend-9a-container', bNrStats);
        
        return myLineChart9a;
    }
}
var myLineChart9a = drawChart9a();
*/

function generateWeekLabels(type) {
    moment.locale('us', { week: { dow: 1 }});//set 6 to start week with Sundays
    moment.locale('us');

    var labels = new Array();
    var nr = type == "this" ? 0 : 7;
    var cw = moment().week();
    var df = nr == 0 ? 0 : 1;
    var f = $("#week-sort-filters .content-filters li a.active");
    var cwr = (typeof f.attr("rel-w") !== "undefined" ? f.attr("rel-w") : cw);

    for (var i=0; i<=6; i++) {
    	labels[i] = moment().week(cwr-df).weekday(i).format("ddd, MMM DD");
    }

    return labels;
}
function generateMonthLabels(type) {
    var labels = new Array();
    var nr = type === "this" ? 0 : 1;
    var cm = moment().month()+1;
    var f = $("#week-sort-filters .content-filters li a.active");
    var mnr = (typeof f.attr("rel-m") !== "undefined" ? f.attr("rel-m") : cm);
    var df = nr == 0 ? 1 : 2;
    var tnr = moment().month(mnr-df).daysInMonth();

    for (var i=0; i<tnr; i++) {
    	labels[i] = moment().month(mnr-df).date(i+1).format("ddd DD");
    }

    return labels;
}
/*
function generateUserWeekLabels(type) {
//    var now = moment();
    var labels = new Array();

    var cm = moment().week()+1;
    var f = $("#week-sort-filters .content-filters li a.active");
    var nr = type === "this" ? 0 : 7;
    var mnr = (typeof f.attr("rel-m") !== "undefined" ? f.attr("rel-m") : cm);

    for (var i=0; i<=6; i++) {
//    	labels[i] = moment().week(mnr-df).date(i+1).format("DD.MM");
    	//labels[i] = moment(now).subtract(0, 'day').day(i).format("DD.MM") + ' / ' + moment(now).subtract(7, 'day').day(i).format("DD.MM");
    }
    
    return labels;
}
*/
function generateUserYearLabels(type) {
    var now = moment();
    var labels = new Array();
    
    var nr = type === "this" ? 1 : 7;
    
    //labels[0] = moment(now).subtract(1, 'day').day(0).format("YYYY") + ' / ' + moment(now).subtract(1, 'year').day(0).format("YYYY");
    //labels[0] = moment(now).subtract(1, 'day').day(0).format("MMM") + ' / ' + moment(now).subtract(7, 'day').day(0).format("DD.MM");
/*    labels[0] = moment().month(0).format("MMM");
    labels[1] = moment().month(1).format("MMM");
    labels[2] = moment().month(2).format("MMM");
    labels[3] = moment().month(3).format("MMM");
    labels[4] = moment().month(4).format("MMM");
    labels[5] = moment().month(5).format("MMM");
    labels[6] = moment().month(6).format("MMM");
    labels[7] = moment().month(7).format("MMM");
    labels[8] = moment().month(8).format("MMM");
    labels[9] = moment().month(9).format("MMM");
    labels[10] = moment().month(10).format("MMM");
    labels[11] = moment().month(11).format("MMM");*/
    for (var i=0; i<=11; i++) {
    	if (nr == 1)
 		labels[i] = moment().month(i).format("MMM");
 	else
 		labels[i] = moment().subtract(1, 'years').month(i).format("MMM");
    }
    
    return labels;
}
function generateLegend(id, items) {
    var legend = document.getElementById(id);
    legend.innerHTML = items.map(function(item) {
      var color = item.color || item.fillColor;
      var label = item.label;
      
      if (typeof(label) == 'undefined' || typeof(color) == 'undefined') {
          return;
      }
      return '<li><i style="background:' + color + '"><span title="' + label + '" rel="tooltip">&nbsp;&nbsp;&nbsp;</span></i></li>';
    }).join('');
    
    setTimeout(function () { resizeDelimiter(); }, 1000);
    
    var animatespeed   = 0;
    var targets = $( '[rel=tooltip]' ),
        target  = false,
        tooltip = false,
        title   = false;

    targets.bind( 'mouseenter', function()
    {
        target  = $( this );
        tip     = target.attr( 'title' );
        tooltip = $( '<div class="tooltip"></div>' );

        if( !tip || tip == '' )
            return false;

        target.removeAttr( 'title' );
        tooltip.css( 'opacity', 0 )
               .html( tip )
               .appendTo( 'body' );

        var init_tooltip = function()
        {
            if( $( window ).width() < tooltip.outerWidth() * 1.5 )
                tooltip.css( 'max-width', $( window ).width() / 2 );
            else
                tooltip.css( 'max-width', 340 );

            var pos_left = target.offset().left + ( target.outerWidth() / 2 ) - ( tooltip.outerWidth() / 2 ),
                pos_top  = target.offset().top - tooltip.outerHeight() - 20;

            if( pos_left < 0 )
            {
                pos_left = target.offset().left + target.outerWidth() / 2 - 20;
                tooltip.addClass( 'left' );
            }
            else
                tooltip.removeClass( 'left' );

            if( pos_left + tooltip.outerWidth() > $( window ).width() )
            {
                pos_left = target.offset().left - tooltip.outerWidth() + target.outerWidth() / 2 + 20;
                tooltip.addClass( 'right' );
            }
            else
                tooltip.removeClass( 'right' );

            if( pos_top < 0 )
            {
                var pos_top  = target.offset().top + target.outerHeight();
                tooltip.addClass( 'top' );
            }
            else
                tooltip.removeClass( 'top' );

            tooltip.css( { left: pos_left, top: pos_top } )
                   .animate( { top: '+=10', opacity: 1 }, animatespeed );
        };

        init_tooltip();
        $( window ).resize( init_tooltip );

        var remove_tooltip = function()
        {
            tip = tooltip.html();
            
            tooltip.animate( { top: '-=10', opacity: 0 }, animatespeed, function()
            {
                $( this ).remove();
            });

            target.attr( 'title', tip );
        };

        target.bind( 'mouseleave', remove_tooltip );
        tooltip.bind( 'click', remove_tooltip );
    });
}

function makeCanvas(id) {
    var container = document.getElementById(id);
    var canvas = document.createElement('canvas');
    var ctx = canvas.getContext('2d');

    container.innerHTML = '';
    canvas.width = container.offsetWidth;
    canvas.height = container.offsetHeight;
 
    container.appendChild(canvas);
   
    return ctx;
}









jQuery(".close_but").click(function () {
    var _for = "." + jQuery(this).attr("rel-close");
    jQuery(_for).fadeOut("normal", function () {
        jQuery(this).detach();
    });
});




/*
(function(jQuery) {
    jQuery.fn.countTo = function(options) {
    // merge the default plugin settings with the custom options
    options = jQuery.extend({}, jQuery.fn.countTo.defaults, options || {});

    // how many times to update the value, and how much to increment the value on each update
    var loops = Math.ceil(options.speed / options.refreshInterval),
    increment = (options.to - options.from) / loops;

    return jQuery(this).each(function() {
        var _this = this,
        loopCount = 0,
        value = options.from,
        interval = setInterval(updateTimer, options.refreshInterval);

        function updateTimer() {
            value += increment;
            loopCount++;
            jQuery(_this).html(value.toFixed(options.decimals));

            if (typeof(options.onUpdate) == 'function') {
                options.onUpdate.call(_this, value);
            }

            if (loopCount >= loops) {
                clearInterval(interval);
                value = options.to;

                if (typeof(options.onComplete) == 'function') {
                    options.onComplete.call(_this, value);
                }
            }
        }
    });
};
*/
/*
jQuery.fn.countTo.defaults = {
    from: 0,  // the number the element should start at
    to: 100,  // the number the element should end at
    speed: 1000,  // how long it should take to count between the target numbers
    refreshInterval: 100,  // how often the element should be updated
    decimals: 0,  // the number of decimal places to show
    onUpdate: null,  // callback method for every time the element is updated,
    onComplete: null  // callback method for when the element finishes updating
    };
})(jQuery);
*/
/*
jQuery(function (jQuery) {
    if (lcount["total"] !== '') {
        jQuery('.timer-live').countTo({
            from: 0,
            to: lcount["total"],
            speed: 500,
            refreshInterval: 10,
            onComplete: function (value) {

            }
        });
        jQuery('.small-timer-live').countTo({
            from: 0,
            to: lcount["active"],
            speed: 500,
            refreshInterval: 10,
            onComplete: function (value) {

            }
        });
        jQuery('.small-inactive-timer-live').countTo({
            from: 0,
            to: lcount["inactive"],
            speed: 1200,
            refreshInterval: 10,
            onComplete: function (value) {

            }
        });
    }
    if (vcount["total"] !== '') {
        jQuery('.timer-videos').countTo({
            from: 0,
            to: vcount["total"],
            speed: 500,
            refreshInterval: 10,
            onComplete: function (value) {

            }
        });
        jQuery('.small-timer-video').countTo({
            from: 0,
            to: vcount["active"],
            speed: 500,
            refreshInterval: 10,
            onComplete: function (value) {

            }
        });
        jQuery('.small-inactive-timer-video').countTo({
            from: 0,
            to: vcount["inactive"],
            speed: 1200,
            refreshInterval: 10,
            onComplete: function (value) {

            }
        });
    }
    if (acount["total"] !== '') {
        jQuery('.timer-audios').countTo({
            from: 0,
            to: acount["total"],
            speed: 1200,
            refreshInterval: 10,
            onComplete: function (value) {

            }
        });
        jQuery('.small-timer-audio').countTo({
            from: 0,
            to: acount["active"],
            speed: 500,
            refreshInterval: 10,
            onComplete: function (value) {

            }
        });
        jQuery('.small-inactive-timer-audio').countTo({
            from: 0,
            to: acount["inactive"],
            speed: 1200,
            refreshInterval: 10,
            onComplete: function (value) {

            }
        });
    }
    if (icount["total"] !== '') {
        jQuery('.timer-images').countTo({
            from: 0,
            to: icount["total"],
            speed: 1200,
            refreshInterval: 10,
            onComplete: function (value) {

            }
        });
        jQuery('.small-timer-image').countTo({
            from: 0,
            to: icount["active"],
            speed: 500,
            refreshInterval: 10,
            onComplete: function (value) {

            }
        });
        jQuery('.small-inactive-timer-image').countTo({
            from: 0,
            to: icount["inactive"],
            speed: 1200,
            refreshInterval: 10,
            onComplete: function (value) {

            }
        });
    }
    if (dcount["total"] !== '') {
        jQuery('.timer-docs').countTo({
            from: 0,
            to: dcount["total"],
            speed: 1200,
            refreshInterval: 10,
            onComplete: function (value) {

            }
        });
        jQuery('.small-timer-doc').countTo({
            from: 0,
            to: dcount["active"],
            speed: 500,
            refreshInterval: 10,
            onComplete: function (value) {

            }
        });
        jQuery('.small-inactive-timer-doc').countTo({
            from: 0,
            to: dcount["inactive"],
            speed: 1200,
            refreshInterval: 10,
            onComplete: function (value) {

            }
        });
    }

    if (bcount["total"] !== '') {
        jQuery('.timer-blogs').countTo({
            from: 0,
            to: bcount["total"],
            speed: 1200,
            refreshInterval: 10,
            onComplete: function (value) {

            }
        });
        jQuery('.small-timer-blog').countTo({
            from: 0,
            to: bcount["active"],
            speed: 500,
            refreshInterval: 10,
            onComplete: function (value) {

            }
        });
        jQuery('.small-inactive-timer-blog').countTo({
            from: 0,
            to: bcount["inactive"],
            speed: 1200,
            refreshInterval: 10,
            onComplete: function (value) {

            }
        });
    }
});
*/