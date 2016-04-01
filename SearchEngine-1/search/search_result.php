<?php
	//Variables
	//s = search term
	//q = pagination variable (to limit x results per page)
	//setting up search options
	/* if(!isset($_SESSION))
	{
		session_start();
	} */
	
	
	include_once 'PorterStemmer.php';
	include_once 'ReadPostingsList.php';
	//setting up search and pagination
	$term = $_GET['s'];
	echo $term;
	//logic for performing stop word and stemming and other operations goes here
	//split the words based on spaces
	$query_terms = explode(" ",$term);
	$redundant = array(".",",",";",":","\"","?"); //symbols to be removed
	
	//calculate the query term frequency
	$queryTermFrequency = array();
	echo count($query_terms) . "<br/>";
	for($i=0;$i<count($query_terms);$i++)
	{
		$cleanedTerm = trim(str_replace($redundant,"",$query_terms[$i]));
		$cleanedTerm = strtolower(str_replace($nonAlphbets,"",$cleanedTerm));
		echo $cleanedTerm . "<br/>";
		$stopWords = array("","a", "about", "above", "across", "after", "afterwards", "again", "against", "all", "almost", "alone", "along", "already", "also","although","always","am","among", "amongst", "amoungst", "amount",  "an", "and", "another", "any","anyhow","anyone","anything","anyway", "anywhere", "are", "around", "as",  "at", "back","be","became", "because","become","becomes", "becoming", "been", "before", "beforehand", "behind", "being", "below", "beside", "besides", "between", "beyond", "bill", "both", "bottom","but", "by", "call", "can", "cannot", "cant", "co", "con", "could", "couldnt", "cry", "de", "describe", "detail", "do", "done", "down", "due", "during", "each", "eg", "eight", "either", "eleven","else", "elsewhere", "empty", "enough", "etc", "even", "ever", "every", "everyone", "everything", "everywhere", "except", "few", "fifteen", "fify", "fill", "find", "fire", "first", "five", "for", "former", "formerly", "forty", "found", "four", "from", "front", "full", "further", "get", "give", "go", "had", "has", "hasnt", "have", "he", "hence", "her", "here", "hereafter", "hereby", "herein", "hereupon", "hers", "herself", "him", "himself", "his", "how", "however", "hundred", "ie", "if", "in", "inc", "indeed", "interest", "into", "is", "it", "its", "itself", "keep", "last", "latter", "latterly", "least", "less", "ltd", "made", "many", "may", "me", "meanwhile", "might", "mill", "mine", "more", "moreover", "most", "mostly", "move", "much", "must", "my", "myself", "name", "namely", "neither", "never", "nevertheless", "next", "nine", "no", "nobody", "none", "noone", "nor", "not", "nothing", "now", "nowhere", "of", "off", "often", "on", "once", "one", "only", "onto", "or", "other", "others", "otherwise", "our", "ours", "ourselves", "out", "over", "own","part", "per", "perhaps", "please", "put", "rather", "re", "same", "see", "seem", "seemed", "seeming", "seems", "serious", "several", "she", "should", "show", "side", "since", "sincere", "six", "sixty", "so", "some", "somehow", "someone", "something", "sometime", "sometimes", "somewhere", "still", "such", "system", "take", "ten", "than", "that", "the", "their", "them", "themselves", "then", "thence", "there", "thereafter", "thereby", "therefore", "therein", "thereupon", "these", "they", "thickv", "thin", "third", "this", "those", "though", "three", "through", "throughout", "thru", "thus", "to", "together", "too", "top", "toward", "towards", "twelve", "twenty", "two", "un", "under", "until", "up", "upon", "us", "very", "via", "was", "we", "well", "were", "what", "whatever", "when", "whence", "whenever", "where", "whereafter", "whereas", "whereby", "wherein", "whereupon", "wherever", "whether", "which", "while", "whither", "who", "whoever", "whole", "whom", "whose", "why", "will", "with", "within", "without", "would", "yet", "you", "your", "yours", "yourself", "yourselves", "the");
		
		if(!array_search($cleanedTerm, $stopWords))
		{
			$stemmedWord = PorterStemmer::Stem($cleanedTerm);
			echo $stemmedWord;
			if(array_key_exists($stemmedWord,$queryTermFrequency))
			{
				$queryTermFrequency[$stemmedWord]= $queryTermFrequency[$stemmedWord]+1;
			}
			else
			{
				$queryTermFrequency[$stemmedWord]=1;
			}
		}	
	}
	
	//read the IDF of each term from the Inverse Index file
	$termIDF = ReadPostingsList::readInverseIndexFile();
	
	//calculate the query vector
	$queryVector = array();
	$cnt = 1;
	foreach($termIDF as $term => $idf)
	{
		echo "Key=" . $term . ", Value=" . $idf;
		echo "<br>";
		if(array_key_exists($term, $queryTermFrequency) && $idf > 0.0)
		{
			$tf_idf = $idf*$queryTermFrequency[$term];
			$queryVector[$cnt] = $tf_idf;
		}
		$cnt++;
	}
	
	
	//display results for testing::: Remove later
	foreach($queryTermFrequency as $term => $termFrequency)
	 {
		echo "Key=" . $term . ", Value=" . $termFrequency;
		echo "<br>";
		
	}
	
	foreach($queryVector as $term => $tfIDF)
	{
		echo "Key=" . $term . ", Value=" . $tfIDF;
		echo "<br>";
	
	}
	
	//Next Steps Identify the Candidate Documents
	//Read the Document vectors
	//Calculate the similarity between the candiate documents and the query vectors and display the results
	
		
/* echo "<div id='result_numbers'>";
	if ($searchterm == '' || $searchterm == 'Search here...') 
	{
		echo "<div class='span-15 last notice' id='result_num_info'>Please enter a search term.</div>";
	}
	else 
	{
		if ($num > 0) 
		{
			echo "<div class='span-12 last success' id='result_num_info'> Searching for: 
			<b>$searchterm</b>. $num Results Found: </div>";
		}
		if ($num == 0) 
		{
			echo "<div class='span-15 last error' id='result_num_info'>
					Searching for: <b>$searchterm</b>. No Results Found </div>";
		}
	}
echo "</div>";	
	if ($num >0){
       		include_once 'search/result_form.php';
	}
 */
?>
