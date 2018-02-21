if (frameElement == null) {
    window.location = "index.php";
}
function openPage(page) {
    var i, tabcontent, tablinks;
    tabcontent = document.getElementsByClassName("tabcontent");
    for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = "none";
    }
    tablinks = document.getElementsByClassName("tablinks");
    for (i = 0; i < tablinks.length; i++) {
        tablinks[i].className = tablinks[i].className.replace(" active", "");
    }
    document.getElementById(page).style.display = "block";
    //evt.currentTarget.className += " active";
}

function pageButton(pages,place) {
    var buttoncontent = document.getElementById(place);
    for (var i = 0; i < pages; i++) {
        var btn = document.createElement("button");
        var k = i + 1;
        var btn = document.createElement("button");
        var txt = document.createTextNode("page"+k);
        btn.appendChild(txt);
        buttoncontent.appendChild(btn);
    }
}
