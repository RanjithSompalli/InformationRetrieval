<?php

class ReadPostingsList
{
	public static function readTermIDFFromInverseIndexFile()
	{
		$inverseIndex = $_SESSION['inverseIndex'];
		$termIDF = array();
		
		foreach($inverseIndex as $term => $postingsList)
		{
			$termIDF["$term"] = $postingsList['idf'];
		}
		return $termIDF;
	} 
	
	
	public static function identifyCandidateDocuments($queryTermFrequency)
	{
		//Read the term IDF from the Inverse Index File
		$inverseIndex = $_SESSION['inverseIndex'];
		$termDocumentsList = array();
		foreach($inverseIndex as $term => $dictionary)
		{
			$postingsList = $dictionary['postingsList'];
			$documentIds = array_keys($postingsList);
			$termDocumentsList["$term"] = $documentIds;
		}
		
		$candidateDocuments = array();
		foreach($queryTermFrequency as $queryTerm => $frequency)
		{
			if(array_key_exists($queryTerm, $termDocumentsList))
			{
				foreach($termDocumentsList[$queryTerm] as $id)
				{
					if(!in_array($id, $candidateDocuments))
						array_push($candidateDocuments, $id);
				}
			}
		}
		
		sort($candidateDocuments);
		return $candidateDocuments;
	}
}				
?>