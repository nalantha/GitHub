if (frameElement == null) {
    window.location = "index.php";
}
window.addEventListener('resize', function(event){
         $( ".date" ).datepicker( "hide" );
});

// functions for services page
function service_editButton (serviceID) {
    window.location.href = "services.php?action=update&service_id="+serviceID;   
} 
function service_removeButton (serviceID) {
    window.location.href = "services.php?action=remove&service_id="+serviceID;           
} 
function service_cancelButton () {
    window.location.href = "services.php?action=start";           
}

//function for services
function select_service()
{
  var x = document.getElementById("services");
  var option = x.options[x.selectedIndex];
  var y = document.getElementById('servicelist');
     
  var choices = y.getElementsByTagName('input');
  for (var i = 0; i < choices.length; i++)
    if (choices[i].value == option.value || option.value == "0")
      return;
     
  var li = document.createElement('li');
  var input = document.createElement('input');
  var text = document.createTextNode(option.firstChild.data);
     
  input.type = 'hidden';
  input.name = 'services[]';
  input.value = option.value;
  li.appendChild(input);
  li.appendChild(text);
  li.setAttribute('onclick', 'this.parentNode.removeChild(this);');       
  y.appendChild(li);
}

function get_services(services)
{
  var y = document.getElementById('servicelist');
  var services_list = services.split(",");
  for ( i in services_list){
      var li = document.createElement('li');
      var input = document.createElement('input');
      var text = document.createTextNode(services_list[i]);
      $('li').addClass('hidden');
      input.type = 'hidden';
      input.name = 'services[]';
      input.value = services_list[i];
      li.appendChild(input);
      li.appendChild(text);
      if (services_list[i]!=""){
         y.appendChild(li);
      }
  }
}

function add_dicom(uid,i=1)
{
  var y = document.getElementById('dicomlist');
  var li = document.createElement('li');
  var input = document.createElement('input');
  var butn = document.createElement('button');
  var text = document.createTextNode('study'+i);
  butn.appendChild(text);
  //butn.classList.add('button');
  butn.type='button';
  butn.onclick = function(){window.location="dicom_open.php?uid=" + uid;};
  input.type = 'hidden';
  input.name = 'dicoms[]';
  input.value = uid;
  li.appendChild(input);
  li.appendChild(butn);
  y.appendChild(li);
}
