function chk_genus(str,mes_obj,mes)
{
	var test1=/^\??[a-zA-Z]+\s*$/;
	var test2=/^\??[a-zA-Z]\.\s?\([a-zA-Z]+\)\s*$/;
	var txt=document.getElementsByName(str)[0];
	
	if (txt.value=="Enter Query Terms Here...")
	{
		txt.focus();
		document.getElementById(mes_obj).innerHTML=mes;
		return false;
	}	
	if (txt.value.search(test1)!=0)
	{
		if (txt.value.search(test2)!=0)
		{
			txt.select();
			document.getElementById(mes_obj).innerHTML=mes;
			return false;
		} 
		else
		{
			return true;
		}
		txt.select();
		document.getElementById(mes_obj).innerHTML=mes;
		return false;
	} 
	return true;
}

function chk_empty(str)
{ 
	if (document.getElementsByName(str)[0].value!="")
	{
		return true;
	}
	return false;
}

