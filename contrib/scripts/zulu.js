function zulu (login,name,domain) {
  if (!name) name=login+'@'+domain;
  document.write('<a href=\"mailto:'+login+'@'+domain+'\">'+name+'</a>');
}

function confirmSubmit(request) {
  if (confirm(request))
    return true;
  else
    return false;
}
