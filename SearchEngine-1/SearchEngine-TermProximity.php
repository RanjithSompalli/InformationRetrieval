<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN"
   "http://www.w3.org/TR/html4/strict.dtd">
<?php
	error_reporting(E_ALL^E_NOTICE^E_WARNING);//
	require_once "search/Paginator.php";
?>
<html lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<title>National Park - Wikipedia</title>

<link rel="stylesheet" href="css/global.css" type="text/css"
	media="screen, projection">
<link rel="stylesheet" href="css/main.css" type="text/css">
<link rel="stylesheet" href="css/style.css" type="text/css">

<script language="javascript" src="js/jquery-1.11.0.js"></script>
<script type="text/javascript">
		$(document).ready(function() {
		 var search = "<?php echo $_GET['searchTerm']; ?>"
		$("#mainSearch").val(search) ;
		});
	</script>
</head>
<body>
	<form name='searchForm' id='searchForm' method='get'
		action="SearchEngine-TermProximity.php">
		<div class="search-box">
			<h1 align="middle">National Park Wiki - Term Proximity Score</h1>
			<input align="middle" class="form-search-box" id="mainSearch"
				type="text" name='searchTerm' /> <input type="hidden" name="ipp"
				value="6" /> <input type="hidden" name="page" value="1" />
			<button type="submit" class="button roundedCorners gradient">Search</button>
		</div>
		<div class="contents">
<?php

performSearch();


function performSearch()
{
	//Variables
	//searchTerm = search term
	//page = pagination variable (to limit x results per page)
	//setting up search options
	
	$startTime = time();
	if(!isset($_SESSION))
	{
		session_start();
	}
	
	include_once 'search/PorterStemmer.php';
	include_once 'search/ReadPostingsList.php';
	
	//setting up search and pagination
	
	if(!empty($_GET['searchTerm']) && isset($_GET['page']) && $_GET['page']==1)
	{
		//read the index documents into the session
		$_SESSION['inverseIndex'] = json_decode(file_get_contents('dictionary/inverseIndex.json'), true);
		$_SESSION['documentVectors'] = json_decode(file_get_contents('dictionary/documentVectors.json'), true);
		$_SESSION['docIDURLMappings'] = json_decode(file_get_contents('dictionary/URLMappings.json'), true);
	
		
		$term = $_GET['searchTerm'];
		//clean the query terms and convert to lower case
		$term = preg_replace("/[^a-zA-Z ]/","",$term);
		$term = strtolower($term);
		$term = trim($term);
		$term = preg_replace("/\s+$/"," ",$term);
	
		//logic for performing stop word and stemming and other operations goes here
		//split the words based on spaces
		$query_terms = explode(" ",$term);
	
		//calculate the query term frequency
		$queryTermFrequency = array();
		$queryTermCount =0 ;
		$finalQueryTerms = array();
		for($i=0;$i<count($query_terms);$i++)
		{
			$cleanedTerm = $query_terms[$i];
			$stopWords = array("a", "about", "above", "across", "after", "afterwards", "again", "against", "all", "almost", "alone", "along", "already", "also","although","always","am","among", "amongst", "amoungst", "amount",  "an", "and", "another", "any","anyhow","anyone","anything","anyway", "anywhere", "are", "around", "as",  "at", "back","be","became", "because","become","becomes", "becoming", "been", "before", "beforehand", "behind", "being", "below", "beside", "besides", "between", "beyond", "bill", "both", "bottom","but", "by", "call", "can", "cannot", "cant", "co", "con", "could", "couldnt", "cry", "de", "describe", "detail", "do", "done", "down", "due", "during", "each", "eg", "eight", "either", "eleven","else", "elsewhere", "empty", "enough", "etc", "even", "ever", "every", "everyone", "everything", "everywhere", "except", "few", "fifteen", "fify", "fill", "find", "fire", "first", "five", "for", "former", "formerly", "forty", "found", "four", "from", "front", "full", "further", "get", "give", "go", "had", "has", "hasnt", "have", "he", "hence", "her", "here", "hereafter", "hereby", "herein", "hereupon", "hers", "herself", "him", "himself", "his", "how", "however", "hundred", "ie", "if", "in", "inc", "indeed", "interest", "into", "is", "it", "its", "itself", "keep", "last", "latter", "latterly", "least", "less", "ltd", "made", "many", "may", "me", "meanwhile", "might", "mill", "mine", "more", "moreover", "most", "mostly", "move", "much", "must", "my", "myself", "name", "namely", "neither", "never", "nevertheless", "next", "nine", "no", "nobody", "none", "noone", "nor", "not", "nothing", "now", "nowhere", "of", "off", "often", "on", "once", "one", "only", "onto", "or", "other", "others", "otherwise", "our", "ours", "ourselves", "out", "over", "own","part", "per", "perhaps", "please", "put", "rather", "re", "same", "see", "seem", "seemed", "seeming", "seems", "serious", "several", "she", "should", "show", "side", "since", "sincere", "six", "sixty", "so", "some", "somehow", "someone", "something", "sometime", "sometimes", "somewhere", "still", "such", "system", "take", "ten", "than", "that", "the", "their", "them", "themselves", "then", "thence", "there", "thereafter", "thereby", "therefore", "therein", "thereupon", "these", "they", "thickv", "thin", "third", "this", "those", "though", "three", "through", "throughout", "thru", "thus", "to", "together", "too", "top", "toward", "towards", "twelve", "twenty", "two", "un", "under", "until", "up", "upon", "us", "very", "via", "was", "we", "well", "were", "what", "whatever", "when", "whence", "whenever", "where", "whereafter", "whereas", "whereby", "wherein", "whereupon", "wherever", "whether", "which", "while", "whither", "who", "whoever", "whole", "whom", "whose", "why", "will", "with", "within", "without", "would", "yet", "you", "your", "yours", "yourself", "yourselves", "the");
	
			if(!in_array($cleanedTerm,$stopWords))
			{
				if(!in_array($cleanedTerm,$finalQueryTerms))
					array_push($finalQueryTerms,$cleanedTerm);
				$stemmedWord = PorterStemmer::Stem ( $cleanedTerm );
				if (array_key_exists ( $stemmedWord, $queryTermFrequency )) 
				{
					$queryTermFrequency ["$stemmedWord"] = $queryTermFrequency ["$stemmedWord"] + 1;
				} 
				else 
				{
					$queryTermFrequency ["$stemmedWord"] = 1;
				}
				$queryTermCount++;
			}
		}
		
		//if all the query terms are filtered by stop words
		if($queryTermCount==0)
		{
			echo "</br>";
			echo "No Results Found";
			exit (0);
		}
		
		
		$_SESSION['queryTerms'] = $finalQueryTerms;
		
		//read the IDF of each term from the Inverse Index file
		$termIDF = ReadPostingsList::readTermIDFFromInverseIndexFile();
		//calculate the query vector
		$queryVector = array();
		$cnt = 1;
		foreach($termIDF as $term => $idf)
		{
			if(array_key_exists($term, $queryTermFrequency) && $idf > 0.0)
			{
				$tf_idf = $idf*$queryTermFrequency[$term];
				$queryVector[$cnt] = $tf_idf;
			}
			$cnt++;
		}
		
		//if none of the query terms are present in the dictionary return no results
		if(count($queryVector)==0)
		{
			echo "</br>";
			echo "No Results Found";
			exit (0);
		}
	
		//read the document vectors file
		$documentVectors = $_SESSION['documentVectors'];
		//Next Steps Identify the Candidate Documents
		 $candidateDocuments = ReadPostingsList::identifyCandidateDocuments($queryTermFrequency);
		 //if there are no candidate documents return no results
		 if(count($candidateDocuments)==0)
		 {
		 	echo "</br>";
		 	echo "No Results Found";
		 	exit (0);
		 }
		//Calculate the similarity between the candiate documents and the query vectors and display the results
		//query vector in $queryVector and document vectors in $documentVectors.
		//calculate the cosine similarity between each candiate document vector and query vector
		
		//calculate query length
		$queryLength = 0;
		foreach($queryVector as $term => $tfIdf)
		{
			$queryLength = $queryLength+(pow($tfIdf, 2));
		}
		$queryLength = sqrt($queryLength);
		
		$rankedDocuments = array();
		foreach($candidateDocuments as $id)
		{
			$docVector = $documentVectors[$id];
			$documentLength = 0;
			foreach($docVector as $term => $tfIdf)
			{
				$documentLength = $documentLength+(pow($tfIdf, 2));
			}
			$documentLength = sqrt($documentLength);
			
			$dotProduct = 0;
			foreach($docVector as $term => $tfIdf)
			{
				if(array_key_exists($term, $queryVector))
					$dotProduct = $dotProduct+($docVector[$term]*$queryVector[$term]);
			}
			$cosineSimilarity = $dotProduct/($documentLength*$queryLength);
			$rankedDocuments[$id]=$cosineSimilarity;
		}
		
		//arsort($rankedDocuments);
		//$rankedDocuments has the cosine similarity value of the documents in sorted order.
		
		//implement term proximity only if there is more than one query term else just use tf-idf.
		$proximityRankedDocuments = array();
		if(count($finalQueryTerms) > 1) 
			$proximityRankedDocuments = calculateScoreBasedOnTermProximity ( $candidateDocuments );
		$_SESSION['termProximityScores'] = $proximityRankedDocuments;
		
		$integratedScores = array(); 
		//integrate proximity score and TF-IDF score
		if(count($proximityRankedDocuments)>0)
		{
			foreach($rankedDocuments as $docID => $similarity)
			{
				$proximityScore = $proximityRankedDocuments[$docID];
				$newScore  = $similarity*$proximityScore;
				$integratedScores["$docID"] = $newScore;
			}
		}
		else
		{
			foreach($rankedDocuments as $docID => $similarity)
			{
				$integratedScores["$docID"] = $similarity;
			}
		}
		
		arsort($integratedScores);
		$finalDocumentIds = array();
		foreach($integratedScores as $docID => $score)
		{
			//echo "DOC ID: " .$docID . " TF_IDF:" .$rankedDocuments[$docID]." Proximity Score:" .$proximityRankedDocuments[$docID]. " Integrated Score: ".$score . "<br/>";
			array_push($finalDocumentIds,$docID);
		}

		
		//the array final 
		$_SESSION['numDocs'] = count($finalDocumentIds);
		$_SESSION['finalDocIds'] = $finalDocumentIds;
		
		$endTime = time();
		$timeDiff = $endTime - $startTime;
		$_SESSION['timeDiff'] = $timeDiff;
		echo "<br />";
		echo $_SESSION['numDocs']." relevant results retrieved in ".$_SESSION['timeDiff']. " Seconds";
		echo "<br /><br />";
		
		returnDoc();
	}
	else if(isset($_GET['page']))
	{
		echo "<br />";
		echo $_SESSION['numDocs']." relevant results retrieved in ".$_SESSION['timeDiff']. " Seconds";
		echo "<br /><br />";
		
		returnDoc();
	}
}

function calculateScoreBasedOnTermProximity($candidateDocuments)
{
	$docIndex = $_SESSION['docIDURLMappings'];
	$query = $_SESSION['queryTerms'];
	
	$termProximityScore = array();
	
	foreach($candidateDocuments as $id)
	{
		$document = $docIndex[$id];
		// Get file content
		$file = file_get_contents($document);
		
		$idxs = array();
		$re = " ";
		$stripped = preg_replace("/\r|\n/"," ",$file);
		$stripped = preg_replace("~<\s*\bscript\b[^>]*>(.*?)<\s*\/\s*script\s*>~is","",$stripped);
		$stripped = preg_replace("~<\s*\bstyle\b[^>]*>(.*?)<\s*\/\s*style\s*>~is","",$stripped);
		$stripped = strip_tags($stripped);
		$stripped = strtolower($stripped);
		$stripped = preg_replace("/[^a-zA-Z ]/"," ",$stripped);
		$words = explode($re, $stripped);
		$queryTermsCovered = array();
		$distance = 0;
		$minDistance = 25;

		foreach($words as $word)
		{
			if(in_array($word,$query ) && !in_array($word,$queryTermsCovered))
			{
				$queryTermsCovered[] = $word;
			}
			if(count($queryTermsCovered)>0)
				$distance = $distance+1;
			if(count($queryTermsCovered) == count($query))
			{
				if($distance < $minDistance)
					$minDistance = $distance;
				$queryTermsCovered = array();
				$distance =0;
			}
			if($distance > $minDistance)
			{
				$queryTermsCovered = array();
				$distance = 0;
			}
		}
		$proximityScore = 25-$minDistance;
		
		$termProximityScore["$id"] = $proximityScore;
		
	}
	return $termProximityScore;
} 



function returnDoc()
{
	$numDocs = $_SESSION['numDocs'];
	$docIds = $_SESSION['finalDocIds'];
	$docIndex = $_SESSION['docIDURLMappings'];
	$query = $_SESSION['queryTerms'];
	$termProximityScores = $_SESSION['termProximityScores'];
	$num_rows = $numDocs;
	$pages = new Paginator;
	$pages->current_page=1;
	$pages->items_total = $num_rows;
	$pages->default_ipp = 8;
	$pages->mid_range = 7;
	$pages->paginate();
	//echo "<br><br>";
	echo "<div class=","results","><ul>";
	for($i=$pages->low;$i<=$pages->high;$i++)
	{
		if($i<$numDocs)
		{
			display($docIndex[$docIds[$i]],$query,$termProximityScores[$docIds[$i]]);
		}
	}
	echo "</ul></div>";
	echo "<br>";
	echo $pages->display_pages();
}
function display($document,$query,$score)
{
	// Get file content
	$file = file_get_contents($document);
	$nakedFile = preg_replace("/docs\//","",$document);
	$nakedFile = substr_replace($nakedFile,"",-4,4);
	//get the title of the page if exists
	if (preg_match("/<title>(.+)<\/title>/",$file,$matches) && isset($matches[1]))
		$title = $matches[1];
	else
		$title = $nakedFile;
	
	$title = $title . "(Term Proximity : ".$score . ")";
	$idxs = array();
	$re = "?";
	$stripped = preg_replace("/\r|\n/"," ",$file);
	$stripped = preg_replace("~<\s*\bscript\b[^>]*>(.*?)<\s*\/\s*script\s*>~is","",$stripped);
	$stripped = preg_replace("~<\s*\bstyle\b[^>]*>(.*?)<\s*\/\s*style\s*>~is","",$stripped);
	$stripped = strip_tags($stripped);
	$stripped = strtolower($stripped);
	$stripped = preg_replace("/[^a-zA-Z ]/","?",$stripped);
	$sentences = explode($re, $stripped);
	//store the sentence with query term
	$description = "";
	$maxQ = 0;
	$maxS = 0;
	foreach ($sentences as $s){
		$numOfWords = 0;
		$newQ = array();
		$flag = false;
		$midWord = "";
		foreach ( $query as $q){
			if (strpos($s,$q) != false) {
				$newQ[] = $q;
			}
				
		}
		$slen = strlen($s);
		//make query words in the sentense bold	(do it for the sentence where max number of query word is found)
		for($i=0;$i<count($newQ) && count($newQ)>=$maxQ; $i++){
				
			//also choose the longes sentence that has the query term/s
			if($slen >= $maxS){
				$description = $description." ".$s;
				$flag = true;
				$maxS = $slen;
			}
			$maxQ = count($newQ);
		}
	}
	//echo $description;
	//Keep only first 30 words
	$cutWords = "|((\w+[\s]+){1,30}).*|";
	$description = preg_replace($cutWords,"$1",$description);
	//highlight the query terms in the sentence
	foreach ( $query as $q){
		$pattern = "|(".$q.")|";
		$description = preg_replace($pattern,"$1",$description);
		$description = preg_replace("|(".$q.")|"," <b>$1</b> ",$description);
	}
	
	//Display
	$divE = "<div class='a_no_image'>";
	$descE = "<div class='a_desc'>".ucfirst($description)."...</div></div></li>";
	$newLine = "<br />";
	
	if ($description == null){
		$descE = "<div class='a_desc'>No description available</div></div>";
	}
	echo "<li>"."<a href='".$document."'>".$title."</a>";
	echo $divE;
	echo $descE;
	echo $newLine;
	echo "</li>";
}

?>

</div>
	</form>
</body>
</html>