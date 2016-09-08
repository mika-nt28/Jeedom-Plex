			//EQUALIZER//
var dots = $('.dot');
var TIMEFRAME = 1;
var MAX_HEIGHT = 11;
var MIN_HEIGHT = 1;
var MAX_COLOR = 5;
var MIN_COLOR = 5;
//var COLOR = '189,195,199'; // RGB
var COLOR = '67,74,84';

dots.each(function (i) {
  $(this).css({ 'margin-left': (i * 0.2) + 'em'});
});

setInterval(function () {
  dots.each(function () {
    var height = Math.floor((Math.random() * MAX_HEIGHT) + MIN_HEIGHT);
    $(this).css({ 
      height: height + 'em', 
      top: '-' + height/2 + 'em',
      background: 'rgba('+ COLOR +',.' + Math.floor((Math.random() * MAX_COLOR) + MIN_COLOR) + ')'
    });
  });
}, 1000 * TIMEFRAME);

setInterval(function () {
  dots.each(function () {
    var height = Math.floor((Math.random() * 2) + MIN_HEIGHT);
    $(this).css({ 
      height: height + 'em', 
      top: '-' + height/1.5 + 'em',
      background: 'rgba('+ COLOR +',.' + Math.floor((Math.random() * MAX_COLOR) + MIN_COLOR) + ')'
    });
  });
}, 1000 * (TIMEFRAME + (TIMEFRAME/5)));

setInterval(function () {
  dots.each(function () {
    var height = Math.floor((Math.random() * 5) + MIN_HEIGHT);
    $(this).css({ 
      height: height + 'em', 
      top: '-' + height/1.5 + 'em',
      background: 'rgba('+ COLOR +',.' + Math.floor((Math.random() * MAX_COLOR) + MIN_COLOR) + ')'
    });
  });
}, 1000 * (TIMEFRAME + (2*TIMEFRAME/5)));

setInterval(function () {
  dots.each(function () {
    var height = Math.floor((Math.random() * 2) + MIN_HEIGHT);
    $(this).css({ 
      height: height + 'em', 
      top: '-' + height/1.5 + 'em',
      background: 'rgba('+ COLOR +',.' + Math.floor((Math.random() * MAX_COLOR) + MIN_COLOR) + ')'
    });
  });
}, 1000 * (TIMEFRAME + (3*TIMEFRAME/5)));

setInterval(function () {
  dots.each(function () {
    var height = Math.floor((Math.random() * 8) + MIN_HEIGHT);
    $(this).css({ 
      height: height + 'em', 
      top: '-' + height/4 + 'em',
      background: 'rgba('+ COLOR +',.' + Math.floor((Math.random() * MAX_COLOR) + MIN_COLOR) + ')'
    });
  });
}, 1000 * (TIMEFRAME + (4*TIMEFRAME/5)));