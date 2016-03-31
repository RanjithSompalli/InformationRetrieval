<?php
$broker=enchant_broker_init();
$tag='ipkb';
enchant_broker_set_dict_path($broker, ENCHANT_MYSPELL,"/projects/ipkb/website/dict" );// 
$term=$_GET['term'];
$redundant=array(",",";",":","\"","+"); //symbols to be removed
$word=trim(str_replace($redundant," ",$term));
while (strpos($word,"  "))
{
	$word=trim(str_replace("  "," ",$term));	
}
$words=explode(" ",$word);
$max_sugg=5;
$correct=True;

if (trim(str_replace($redundant," ",$term))=="")
{
	echo '0';
	exit;
}

if (enchant_broker_dict_exists($broker, $tag))
{
	$dict=enchant_broker_request_dict($broker, $tag);  
	
	$suggestions=array();
	for ($i=0;$i<count($words);$i++)
	{
		$isCorrectlySpelled=enchant_dict_check($dict, $words[$i]);
		if (!$isCorrectlySpelled)
		{
			$correct=False;
			$suggestion=enchant_dict_suggest($dict, $words[$i]);
			array_push($suggestions,$suggestion);//Maybe no results. It seems that distance<=3
	 	}
	 	else
	 	{
	 		$tmp=array();
	 		array_push($tmp,$words[$i]);
	 		array_push($suggestions,$tmp);
	 	}
 	}
}
if ($correct)
{
	echo '0';
}
else
{
	$total=1;
	$twocnt=0;
	for ($i=0;$i<count($words);$i++)	
	{
		if (count($suggestions[$i])==0)
		{
			echo '1';
			exit;
		}
		else
		{
			$total=$total*count($suggestions[$i]);
			if (count($suggestions[$i])>=2)
			{
				$twocnt++;
			}
		}
	}
	$arr=array();
	$tmp="";
	if ($total<=5)
	{
		$arr=array_fill(0,count($words),0);
		$step=0;
		while (($step<count($words)) && ($step>=0))
		{
			$arr[$step]++;
			if ($arr[$step]>count($suggestions[$step]))
			{
				$arr[$step]=0;
				$step--;
			}
			else
			{
				if ($step==count($words)-1)
				{	
					for ($i=0;$i<count($words);$i++)
					{
						$tmp.=$suggestions[$i][$arr[$i]-1];
						$tmp.=" ";
					}
					$tmp=trim($tmp);
					$tmp.=",";
				}
				else
				{
					$step++;
				}
			}
		}		
	}
	else
	{
		for ($i=0;$i<count($words);$i++)
		{
			$tmp.=$suggestions[$i][0];
			$tmp.=" ";
		}
		$tmp=trim($tmp);
		$tmp.=",";	
		$cnt=1;
		for ($i=0;$i<count($words);$i++)
		{
			if (count($suggestions[$i])>=2)
			{
				$cnt++;
				for ($j=0;$j<count($words);$j++)
				{
					if ($i==$j)
					{
						$tmp.=$suggestions[$j][1];
					}
					else
					{
						$tmp.=$suggestions[$j][0];
					}
					$tmp.=" ";
				}
				$tmp=trim($tmp);
				$tmp.=",";	
				if ($cnt>=5)
				{
					break;
				}
			}
		}
		if ($cnt<5)
		{
			if ($twocnt==1)
			{
				for ($i=0;$i<count($words);$i++)
				{
					if (count($suggestions[$i])>=2)
					{
						for ($k=2;$k<count($suggestions[$i]);$k++)
						{
							$cnt++;
							for ($j=0;$j<count($words);$j++)
							{
								if ($i==$j)
								{
									$tmp.=$suggestions[$j][$k];
								}
								else
								{
									$tmp.=$suggestions[$j][0];
								}
								$tmp.=" ";
							}
							$tmp=trim($tmp);
							$tmp.=",";	
							if ($cnt>=5)
							{
								break;
							}
						}
					}
					if ($cnt>=5)
					{
						break;
					}
				}				
			}
			else
			{
				for ($i=0;$i<count($words);$i++)
				{
					if (count($suggestions[$i])>=2)	
					{
						for ($j=$i+1;$j<count($words);$j++)
						{
							if (count($suggestions[$j])>=2)	
							{
								$cnt++;
								for ($k=0;$k<count($words);$k++)
								{
									if (($i==$k) || ($k==$j))
									{
										$tmp.=$suggestions[$k][1];
									}
									else
									{
										$tmp.=$suggestions[$k][0];
									}
									$tmp.=" ";
								}
								$tmp=trim($tmp);
								$tmp.=",";				
								if ($cnt>=5)
								{
									break;
								}
							}					
						}
					}
					if ($cnt>=5)
					{
						break;
					}
				}			
			}
		}
	}
	$tmp=trim($tmp,",");
	echo $tmp;	
}

enchant_broker_free($broker);
?>