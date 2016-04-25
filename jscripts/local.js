function append(pagepersite){
    var pagestart = document.getElementById('pagestart');
    pagestart.value = parseInt(pagestart.value, 10) + parseInt(pagepersite, 10);
}
function degress(pagepersite){
    var pagestart = document.getElementById('pagestart');
    var result = parseInt(pagestart.value, 10) - parseInt(pagepersite, 10);
    pagestart.value = (result < 0)?0:result;
}
function nullpagestart(){
    var pagestart = document.getElementById('pagestart');
    pagestart.value = 0;
}
function resetToDefaults(){
  var elements = document.forms['filter'].elements;
  //var inputs = document.getElementById("filter").getElementsByTagName("input");
  
  for (i=0; i<elements.length; i++){
      if(elements[i].type === 'text'){
	elements[i].value = '';
      }

      if(elements[i].type == 'select-one' && elements[i].name != 'pagepersite'){
	$('#'+elements[i].name).val("");
      }
  }
}

