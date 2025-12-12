function downloadIcs(path, filename) {
    var xhttp = new XMLHttpRequest();
    xhttp.onload = function () {
        if (this.readyState == 4 && this.status == 200) {
            var arrContents = xhttp.responseText.split("\n"); // gotcha!

            // The actual download
            var blob = new Blob(arrContents, {type: 'text/calendar'});
            var link = document.createElement('a');
            link.href = window.URL.createObjectURL(blob);
            link.download = filename;

            document.body.appendChild(link);

            link.click();

            document.body.removeChild(link);
        }
    };
    xhttp.open("GET", path, true);
    xhttp.send();
}
