function toggle_visibility(id)
{
	var e = document.getElementById(id);
	if(e && e.style.display == 'none')
	e.style.display = 'block';
}

function toggle_hidden(id)
{
	var e = document.getElementById(id);
	if(e && e.style.display == 'block')
	e.style.display = 'none';
}  
 
function showFullDesc(id)
{
	var e = document.getElementById(id);
	if(e.style.display == 'none')
	e.style.display = '';
}    

function hideMoreDesc(id)
{
	var e = document.getElementById(id);
	if(e.style.display == 'inline-block')
	e.style.display = 'none';
}  
