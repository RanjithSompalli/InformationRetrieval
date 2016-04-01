<?php

class ReadPostingsList
{
	public static function readInverseIndexFile()
	{
		//Read the term IDF from the Inverse Index File
		$inverseIndexFile=fopen("dictionary/inverseIndex.txt","r") or die("Unable to opne file!");
		$termIDF = array();
		while (!feof($inverseIndexFile))
		{
			$line=fgets($inverseIndexFile);
			$pair=explode(":",$line);
			if(count($pair)>0)
			{
				$idf = explode ( ",", $pair [1] );
				$termIDF ["$pair[0]"] = $idf [1];
			}
		}
		fclose($inverseIndexFile);
		return $termIDF;
	}
}				
?>