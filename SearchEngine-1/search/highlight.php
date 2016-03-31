<?php
/* This function receives a string (to be output), a string of
keywords separated by space, and adds background color to keywords     
-ymeng 09-28-2011
*/

function highlight($str,$keyword)  
{
	if ($keyword===' ' || $keyword===''){
    return $str;
	}

	$arr=array(" ",",",".",";");	
	
 	$str_begin = "<span class='highlight'>";
 	$str_end   = "</span>";
 	$keywords=explode (" ",strtolower($keyword)); //convert to lower case
 
 	for ($i = 0; $i < count($keywords); $i++)
 	{
  	$word=trim($keywords[$i]);
  	if($word !="" && $word !=" ")
  	{
   		$word_uppercase=strtoupper($word); //covert all letters to upper case
   		$word_first_letter_upper=strtoupper(substr($word,0,1)).substr($word,1); //covert first letter to upper case
   
   		if(strpos($word_first_letter_upper,'-')){ 
   			$hyphen_parts=explode ('-',$word_first_letter_upper); 
   			$word_first_letter_upper1=$hyphen_parts[0].' '.strtoupper(substr($hyphen_parts[1],0,1)).substr($hyphen_parts[1],1);
   			$word_first_letter_upper2=$hyphen_parts[0].' '.$str_begin.strtoupper(substr($hyphen_parts[1],0,1)).substr($hyphen_parts[1],1).$str_end;
   	} 
   //special treatment for words with hyphens, eg. geological time
   //the idea is to highlight "Upper Cambrian", "Cambrian", but not "Upper" alone 	

		if(strpos($word_first_letter_upper,'-')){
			$val=$str_begin.$word_first_letter_upper1.$str_end;
			for ($j=0;$j<4;$j++)
			{
				$tmp1=$val.$arr[$j];
				$tmp2=$word_first_letter_upper1.$arr[$j];
				$str = str_replace($tmp2,$tmp1,$str);
				$tmp2=$word_first_letter_upper2.$arr[$j];
				$str = str_replace($tmp2,$tmp1,$str);
			}
			//$str = str_replace($word_first_letter_upper1,$val,$str);
			//$str = str_replace($word_first_letter_upper2,$val,$str);
		}
		else{
			$val=$str_begin.$word_first_letter_upper.$str_end;
			for ($j=0;$j<4;$j++)
			{
				$tmp1=$val.$arr[$j];
				$tmp2=$word_first_letter_upper.$arr[$j];
				$str = str_replace($tmp2,$tmp1,$str);
			}
			//$str = str_replace($word_first_letter_upper,$val,$str);
			}
    //echo $val.'<br>';
		
		$val=$str_begin.$word.$str_end;
		for ($j=0;$j<4;$j++)
		{
			for ($k=0;$k<4;$k++)
			{
				$tmp1=$arr[$j].$val.$arr[$k];
				$tmp2=$arr[$j].$word.$arr[$k];
				$str = str_replace($tmp2,$tmp1,$str);
			}
		}		
		//$str = str_replace($word,$val,$str);
	   
		$val=$str_begin.$word_uppercase.$str_end;
		for ($j=0;$j<4;$j++)
		{
			for ($k=0;$k<4;$k++)
			{
				$tmp1=$arr[$j].$val.$arr[$k];
				$tmp2=$arr[$j].$word_uppercase.$arr[$k];
				$str = str_replace($tmp2,$tmp1,$str);
			}
		}		
		//$str = str_replace($word_uppercase,$val,$str);
   
  
      
   }
 }
 return $str;
}
?>
