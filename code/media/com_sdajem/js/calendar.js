;(function () {
    'use strict'

    var calenderDivs = $('.field-calendar');
//    var allDay = $('#jform_allDayEvent');

    calenderDivs.on('change', function () {

        var calendar0 = calenderDivs[0]._joomlaCalendar;
        var calendar1 = calenderDivs[1]._joomlaCalendar;

        if (calendar0.inputField.value !== '') {
            if (calendar1.inputField.value === '' || calendar1.inputField.value < calendar0.inputField.value) {
                var inputAltValueDate0 = Date.parseFieldDate(calendar0.inputField.getAttribute('data-alt-value'), calendar0.params.dateFormat, 'gregorian', calendar0.strings);
                calendar1.date = inputAltValueDate0;
                //calendar1.inputField.value = inputAltValueDate.print(calendar1.params.dateFormat, calendar1.params.dateType, true, calendar1.strings);
                calendar1.inputField.setAttribute('value', inputAltValueDate0.print(calendar1.params.dateFormat, calendar1.params.dateType, true, calendar1.strings));
                calendar1.inputField.setAttribute('data-alt-value', inputAltValueDate0.print(calendar1.params.dateFormat, calendar1.params.dateType, true, calendar1.strings));
            }
        }

        if (calendar1.inputField.value !== '') {
            if (calendar0.inputField.value === '' || calendar0.inputField.value > calendar1.inputField.value) {
                var inputAltValueDate1 = Date.parseFieldDate(calendar1.inputField.getAttribute('data-alt-value'), calendar1.params.dateFormat, 'gregorian', calendar1.strings);
                calendar0.date = inputAltValueDate1;
                calendar0.inputField.setAttribute('value', inputAltValueDate1.print(calendar0.params.dateFormat, calendar0.params.dateType, true, calendar0.strings));
                calendar0.inputField.setAttribute('data-alt-value', inputAltValueDate1.print(calendar0.params.dateFormat, calendar0.params.dateType, true, calendar0.strings));
            }
        }
    }
    )
/*
    allDay.on('change', function () {
        var calendar0 = calenderDivs[0]._joomlaCalendar;
        var calendar1 = calenderDivs[1]._joomlaCalendar;

        if (!!parseInt(allDay.val())) {
            calendar0.params.showsTime = true;
            calendar0.params.dateFormat = "%Y-%m-%d %H:%M:%S";
            calendar0.recreate();
        } else {
            calendar0.params.showsTime = false;
            calendar0.params.dateFormat = "%Y-%m-%d";
            calendar0.recreate();
        }

        calendar1.params.showsTime = !!parseInt(allDay.val());
        calendar1.recreate();
        }
    )
*/
})()