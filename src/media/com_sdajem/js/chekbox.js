;(function () {
        'use strict'

        var boxes = $(":checkbox");

    for (var i = 0, l = boxes.length; l > i; i += 1) {
        if (boxes.checked) {
            boxes.checked = "true";
        }
    }
    }
)()