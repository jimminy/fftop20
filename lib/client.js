function ajax_post(ff, command, body, comment, images)
{
  var xmlhttp;
  if (window.XMLHttpRequest)
  {// code for IE7+, Firefox, Chrome, Opera, Safari
  xmlhttp=new XMLHttpRequest();
  }
  else
  {// code for IE6, IE5
  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  }
  xmlhttp.open("POST",ff,true);
  xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
  xmlhttp.send("command="+command+"&body="+body.replace(/&/g, '%26')+"&comment="+comment.replace(/&/g, '%26')+"&images="+images);
}

function remove_element(entry, child){
  var d = document.getElementById(entry);

  var olddiv = document.getElementById(child);

  d.removeChild(olddiv);
}