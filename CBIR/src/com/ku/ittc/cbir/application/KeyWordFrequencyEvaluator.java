package com.ku.ittc.cbir.application;

import java.util.Arrays;
import java.util.Collections;
import java.util.Comparator;
import java.util.HashSet;
import java.util.LinkedHashMap;
import java.util.LinkedList;
import java.util.List;
import java.util.Map;
import java.util.Set;
import java.util.TreeMap;

public class KeyWordFrequencyEvaluator 
{
	
	//to hold the total number of documents
	private static int totalNumberOfDocuments; 
	
	public static void main(String[] args)
	{
		
		//Get the Descriptions in cleaned and in lower case form the Database.
		Map<Integer,String> descriptionList = DBAccess.getDescriptionFromDB();
		
		if(descriptionList!=null && descriptionList.size()>0)
		{
			//the size of the map is the the total number of documents
			totalNumberOfDocuments = descriptionList.size();
			
			//Build the initial inverse index
			//Format : Each Term-> <List of Document IDs (Duplicates allowed)>
			//e.g.: Term1-><1,1,2,2,2,3,3,3,10> term frequency: 9 Doc Frequency: 4
			Map<String,List<Integer>> inverseIndex = buildInverseIndex(descriptionList);
			System.out.println(inverseIndex.size());//total number of terms in dictionary
			
			//Build the actual Term Dictionary to Postings List Mapping (FInal Inverse Index)
			//Format: Each Term: DocFrequency, TermFrequency, Map of DocIds and term frequency in that doc (document object)
			//e.g.: for above Term1-> 4,9, [1->2],[2->3]->[3->3]->[10->1]
			Map<String,PostingsList> dictionary = buildPostingsList(inverseIndex);
			
			//Write the inverse index to file
			FileUtility.writeInverseIndexToFile("inverseIndex.txt",dictionary);
			
			//generate the IDF value for each term and store it in a text file in format term:IDF
			Map<String,Double> termIDFMap = calculateIDF(dictionary);
			FileUtility.writeTermIDF("termIDF.txt",termIDFMap);
			
			//Next step is to build Vector Space Model
		}
	}
	
	/**
	 * 
	 * Method to build the inverse index
	 * @param descriptionList
	 * @return 
	 */
	private static Map<String,List<Integer>> buildInverseIndex(Map<Integer, String> descriptionList) 
	{
		//Map to store the inverseIndex. Each term is a Key and all the docs in which it exists is a List of values.
		Map<String,List<Integer>> inverseIndex = new TreeMap<String,List<Integer>>();
		//Stemmer class instance
		Porter stemmer = new Porter();
		//List of all stop words.
	    Set<String> stopWordsSet = new HashSet<String>(Arrays.asList(StopWords.stopWords));
	    
		for(Integer id : descriptionList.keySet())
		{
			String description = descriptionList.get(id);
			String[] descriptionTokens = description.split("\\s+");
			for(String str : descriptionTokens)
			{
				if(!str.equals("") && !stopWordsSet.contains(str))
				{
					//perform the stemming
					String stemmedString = stemmer.stripAffixes(str);
					
					if(inverseIndex.get(stemmedString)!=null)
					{
						inverseIndex.get(stemmedString).add(id);
					}
					else
					{
						List<Integer> docList = new LinkedList<Integer>();
						docList.add(id);
						inverseIndex.put(stemmedString,docList);
					}
				}
			}
		}
		return inverseIndex;
	}
	
	/***
	 * 
	 * method to build the postings List
	 * @param inverseIndex
	 * @return
	 */
	private static Map<String,PostingsList> buildPostingsList(Map<String, List<Integer>> inverseIndex) 
	{
		Map<String,PostingsList> dictionary = new TreeMap<String,PostingsList>();
		for(String term : inverseIndex.keySet())
		{
			
			List<Integer> docList = inverseIndex.get(term);
			int termFrequency = docList.size();
			int docFrequency = getDocumentFrequecy(docList);
			Map<Integer,Integer> postingsList = getDocFrequencyForEachDoc(docList);
			PostingsList termPostingsList = new PostingsList(termFrequency,docFrequency,postingsList);
			dictionary.put(term, termPostingsList);
		}
		return dictionary;
	}


	/**
	 * method to build the map of doc id and term frequency for postings list
	 * @param docList
	 * @return
	 */
	private static Map<Integer,Integer> getDocFrequencyForEachDoc(List<Integer> docList) 
	{
		Map<Integer,Integer> docFreqList = new TreeMap<Integer,Integer>();
		for(Integer id : docList)
		{
			if(docFreqList.get(id)!=null)
				docFreqList.put(id,(docFreqList.get(id))+1);
			else
				docFreqList.put(id,1);
		}
		return docFreqList;
	}

	/**
	 * 
	 * getting the unique number of elements from a sorted list
	 * @param docList
	 * @return
	 */
	private static int getDocumentFrequecy(List<Integer> docList) 
	{
		int docFrequency = 1;
		int currentId= docList.get(0);
		for(int i=1;i<docList.size();i++)
		{
			if(docList.get(i)!=currentId)
				docFrequency++;
			else
				continue;
			currentId = docList.get(i);
		}
		return docFrequency;
	}
	
	/**
	 * Calculate IDF for each term
	 * @param dictionary
	 * @return
	 */
	private static Map<String, Double> calculateIDF(Map<String, PostingsList> dictionary) 
	{
		Map<String,Double> termIDFMap = new LinkedHashMap<String,Double>();
		for(String term: dictionary.keySet())
		{
			PostingsList temPl = dictionary.get(term);
			//double TF = temPl.getTermFrequency();
			double IDF = Math.log(totalNumberOfDocuments/temPl.getDocumentFrequency());
			//double TF_IDF = TF * IDF;
			termIDFMap.put(term,IDF);
		}
		return sortByValue(termIDFMap);
	}

	/**
	 * To sort the map based on the IDF value decreasing
	 * @param map
	 * @return
	 */
	public static <K, V extends Comparable<? super V>> Map<K, V> sortByValue( Map<K, V> map )
	{
		List<Map.Entry<K, V>> list = new LinkedList<Map.Entry<K, V>>( map.entrySet());
		Collections.sort( list, new Comparator<Map.Entry<K, V>>()
		{
			public int compare( Map.Entry<K, V> o1, Map.Entry<K, V> o2 )
			{
				return (o2.getValue()).compareTo( o1.getValue() );
			}
		} );

		Map<K, V> result = new LinkedHashMap<K, V>();
		for (Map.Entry<K, V> entry : list)
		{
			result.put( entry.getKey(), entry.getValue() );
		}
		return result;
	}
}
