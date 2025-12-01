/**
 * @copyright: abahlo@hotmail.de
 * @licence Gnu GPL
 * Load the magic
 */
;(function () {
    'use strict'

    let el, offset, transform,
        bbox, minX, maxX, minY, maxY, confined, elForCenter;

    const svg = document.getElementById('main_svg');
    const boundaryX1 = 0;
    const boundaryX2 = document.getElementById('boxX').value;
    const boundaryY1 = 0;
    const boundaryY2 = document.getElementById('boxY').value;

    const dragBoxes = $('.draggable');
    const rotators = $('.rotator');

    let dragging = false;
    let rotating = false;

    dragBoxes.each(function (){
        this.addEventListener('mousedown', startDrag);
        this.addEventListener('touchstart', startDrag);
    });

    rotators.each(function (){
        this.addEventListener('mousedown', startRotate);
        this.addEventListener('touchstart', startRotate);
    });

    document.addEventListener('mouseup', stopAll);
    document.addEventListener('touchend', stopAll);
    document.addEventListener('mousemove', moving);
    document.addEventListener('touchmove', moving);

    document.getElementById('toSvg').addEventListener('click', exportSVG);
    document.getElementById('save').addEventListener('click', exportSVG);
    document.getElementById('messages').hidden = true;

    function getMousePosition(evt) {
        let CTM = svg.getScreenCTM();
        if (evt.touches) { evt = evt.touches[0]; }

        return {
            x: (evt.clientX - CTM.e) / CTM.a,
            y: (evt.clientY - CTM.f) / CTM.d
        };
    }

    function startDrag(evt) {
        dragging = true;
        el = evt.target.parentElement.parentElement;

        offset = getMousePosition(evt);

        offset.x -= el.x.baseVal.value;
        offset.y -= el.y.baseVal.value;

        bbox = {
            x:el.x.baseVal.value,
            y:el.y.baseVal.value,
            height:el.height.baseVal.value,
            width:el.width.baseVal.value
        }

        confined = el.classList.contains('confine');
        if (confined) {
            minX = boundaryX1;
            maxX = boundaryX2 - bbox.width;
            minY = boundaryY1;
            maxY = boundaryY2 - bbox.height;
        }
    }

    function startRotate(evt) {
        rotating = true;
        el = evt.target.parentElement;

        elForCenter = el.childNodes.item(0);
        bbox = elForCenter.getBBox();
        let transforms = el.transform.baseVal;

        if (transforms.length === 0 || transforms.getItem(0).type !== SVGTransform.SVG_TRANSFORM_ROTATE) {
            // Create an transform that translates by (0, 0)
            let translate = svg.createSVGTransform();
            translate.setRotate(0, bbox.width / 2, bbox.height / 2);
            el.transform.baseVal.insertItemBefore(translate, 0);
        }

        transform = transforms.getItem(0);
    }

    function moving(evt) {
        if (dragging) {
            let coord = getMousePosition(evt);

            let dx = coord.x - offset.x;
            let dy = coord.y - offset.y;

            if (confined) {
                if (dx < minX) {
                    dx = minX;
                }
                else if (dx > maxX) {
                    dx = maxX;
                }

                if (dy < minY) { dy = minY; }
                else if (dy > maxY) { dy = maxY; }
            }

            el.setAttributeNS(null, 'x', dx);
            el.setAttributeNS(null, 'y', dy);
        }
        if (rotating) {

            let elForM = el.parentElement;
            let bboxFM = {
                x:elForM.x.baseVal.value,
                y:elForM.y.baseVal.value,
                height:elForM.height.baseVal.value,
                width:elForM.width.baseVal.value
            }

            const rotateCenterX = bboxFM.x + bbox.width/2;
            const rotateCenterY = bboxFM.y + bbox.height/2;

            offset = getMousePosition(evt);

            let deltaX = offset.x - rotateCenterX;
            let deltaY = offset.y - rotateCenterY;

            let angle = Math.atan2(deltaY, deltaX) * (180 / Math.PI) - 90;
            transform.setRotate(angle, bbox.width/2, bbox.height/2);
        }
    }

    function stopAll() {
        dragging = false;
        rotating = false;
        el = false;
    }

    /**
     * Generate a download link and click it
     *
     * @param fileName
     * @param data
     */
    function generateLink(fileName, data) {
        let evt = new MouseEvent("click", {
                view: window,
                bubbles: false,
                cancelable: true
            }
        );
        let link = document.createElement('a'); // Create a element.
        link.download = fileName; // Set value as the file name of download file.
        link.href = data; // Set value as the file content of download file.
        link.setAttribute("target", '_blank');
        link.dispatchEvent(evt);
    }

    /**
     * Export the SVG
     */
    function exportSVG() {
        styles(svg);
        let newSvg = svg;
        let svgString;
        if (window.ActiveXObject) {
            svgString = newSvg.xml;
        }
        else {
            let oSerializer = new XMLSerializer();
            svgString = oSerializer.serializeToString(newSvg);
        }

        // We want a svg file.
        if (this.id === 'toSvg') {
            generateLink('planing.svg', 'data:image/svg+xml;utf8,' + svgString);
        }

        // We want to save
        if (this.id === 'save') {
            let data = new FormData;
            let elements = document.getElementsByClassName('dragMe');

            let elementArray = {};

            let serializer = new XMLSerializer();
            for (let i = 0; i < elements.length; i++)
            {
                if (window.ActiveXObject) {
                    svgString = newSvg.xml;
                    elementArray[elements[i].id] = elements[i].xml;
                }
                else {
                    elementArray[elements[i].id] = serializer.serializeToString(elements[i]);
                }

            }

            data.append('svg', JSON.stringify(elementArray));
            data.append('id', document.getElementById('eventId').value);
            let xhttp = new XMLHttpRequest();
            xhttp.onload = function () {
                if (this.readyState === 4 && this.status === 200) {
                    let html = xhttp.response;
                    let message = '';

                    if (html.toString() === "error") {
                        message = document.createElement('p');
                        message.innerHTML = 'An error occured';
                        document.getElementById('messages').classList.add('failed');
                    }
                    else {
                        message = document.createElement('p');
                        message.innerHTML = 'Erfolgreich gespeichert';
                        document.getElementById('messages').classList.add('ok');
                    }

                    document.getElementById('messages').insertAdjacentElement('afterbegin', message);
                    document.getElementById('messages').hidden = false;
                }
            };

            xhttp.open("POST", "index.php?option=com_sdajem&view=event&task=event.savePlan");
            xhttp.send(data);
        }
    }

    /**
     * Include styles in the svg
     * @param dom
     */
    function styles(dom) {
        let used = "";
        let sheets = document.styleSheets;
        for (let i = 0; i < sheets.length; i++) {
            try {
                let rules = sheets[i].cssRules;
                for (let j = 0; j < rules.length; j++) {
                    let rule = rules[j];
                    if (typeof(rule.style) != "undefined") {
                        let elems = dom.querySelectorAll(rule.selectorText);
                        if (elems.length > 0) {
                            used += rule.selectorText + " { " + rule.style.cssText + " }\n";
                        }
                    }
                }
            }
            catch (e) {
            }
        }

        let s = document.createElement('style');
        s.setAttribute('type', 'text/css');
        s.innerHTML = "<![CDATA[\n" + used + "\n]]>";

        let defs = document.createElement('defs');
        defs.appendChild(s);
        dom.insertBefore(defs, dom.firstChild);
    }

})();
