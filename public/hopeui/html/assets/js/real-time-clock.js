
(function() {
    'use strict';

    // variables for time clock
    var current_time = document.getElementById('current-time');
    var current_date = document.getElementById('current-date');
    var current_day = document.getElementById('current-day');

    // time function to prevent the 1s delay
    var setTime = function() {
        // initialize clock with timezone
        var time = moment();

        // set your date and time language here, english is default locale
        moment.locale();

        // example: for spanish language add 'es' to moment.locale()
        // moment.locale('es');

        // set time in html
        current_time.innerHTML = time.format("kk:mm:ss");

        // set date in html
        current_date.innerHTML = time.format('MMMM D YYYY');

        // set day in html
        current_day.innerHTML = time.format('ddd');
    }

    setTime();
    setInterval(setTime, 1000);
})();