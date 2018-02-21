window.addEventListener("orientationchange", function() {
      window.location.reload();
}, false);
$(document).ready(function()
{
$('#show-menu').click( function(event){
    event.stopPropagation();
});

$(document).click( function(){
    const mq = window.matchMedia( "(max-width: 600px)" );
    if (mq.matches) {
        closeNav();
    }
});
$('#mainFrame').on('click', function(event) { 
    closeNav();
});
})

function loadit() {
var tabs=document.getElementById('tabs').getElementsByTagName("a");
for (var i=0; i < tabs.length; i++)
{
    if(tabs[i].href == element.href) 
         tabs[i].className="selected";
    else
         tabs[i].className="";
}
}
function closeNav() {
        document.getElementById("menu").style.width = "0px";
        document.getElementById("main-div").style.marginLeft="0px";
        document.getElementById("show-menu").style.width = "40px";
}
function openNav() {
    const mq = window.matchMedia( "(min-width: 600px)" );
    //document.getElementById("menu").style.display = "block";
    if (mq.matches) {
        document.getElementById("menu").style.width = "150px";
        document.getElementById("main-div").style.marginLeft = "150px";
        document.getElementById("show-menu").style.width = "0px";
    } else {
        document.getElementById("menu").style.width = "150px";
    }
}
