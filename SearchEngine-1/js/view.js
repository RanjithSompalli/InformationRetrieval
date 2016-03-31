function show(limit,full,limit_tag,full_tag)
{
	document.getElementById(limit.id).style.display="none";
	document.getElementById(full.id).style.display="block";
	document.getElementById(full_tag.id).style.display="none";
	document.getElementById(limit_tag.id).style.display="block";	
}
function show_limit(limit,full,limit_tag,full_tag)
{
	document.getElementById(limit.id).style.display="block";
	document.getElementById(full.id).style.display="none";
	document.getElementById(full_tag.id).style.display="block";
	document.getElementById(limit_tag.id).style.display="none";	
}

