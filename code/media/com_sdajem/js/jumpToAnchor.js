;(function () {
        'use strict'

        const activeAccordion = document.getElementById('activeAccordion');
        document.getElementById(activeAccordion.value).scrollIntoView({behavior: 'smooth'});
    }
)()
