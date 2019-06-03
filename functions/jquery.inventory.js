
function show_file( p_script_url ) {
  script = document.createElement('script');
  script.src = p_script_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}

function text_Length(field, maxlimit) {
  if (field.value.length > maxlimit)
    field.value = field.value.substring(0, maxlimit);
}

function encode_URI( p_text ) {
  var en_text = encodeURIComponent(p_text);
  return en_text.replace(/'/, "%27");
}

function radio_Loop( p_form, p_loop ) {
  var f_radio = 0;
  for (var i = 0; i < p_loop; i++) {
    if (p_form[i].checked == 1) {
      f_radio = i;
    }
  }
  return p_form[f_radio].value;
}

function set_Class( p_Var, p_Class ) {
  if (navigator.appName == "Microsoft Internet Explorer") {
    document.getElementById(p_Var).className = p_Class;
  } else {
    document.getElementById(p_Var).setAttribute("class",p_Class);
  }
}

function toggleDiv(divid) {
  var dv = document.getElementById(divid);
  dv.style.display = (dv.style.display == 'none'? 'block':'none');
}

function showDiv(divid) {
  var dv = document.getElementById(divid);
  dv.style.display = 'block';
}

function hideDiv(divid) {
  var dv = document.getElementById(divid);
  dv.style.display = 'none';
}

function textCounter(field,cntfield,maxlimit) {
  if (field.value.length > maxlimit)
    field.value = field.value.substring(0, maxlimit);
  else
    cntfield.value = maxlimit - field.value.length;
}

