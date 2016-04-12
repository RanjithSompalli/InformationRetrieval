package com.ku.informationretrieval.indexer;

import java.util.ArrayList;
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

public class Indexer 
{
	
	//to hold the total number of documents
	private static int totalNumberOfDocuments; 
	
	public static void main(String[] args)
	{
		//update this location to local document location
		String directoryLocation = "D:\\Ranjith\\Projects\\GitProjects\\docs";
		
		long startTime = System.currentTimeMillis();
		//Get the Descriptions in cleaned and in lower case form the Crawled Documents.
		Map<Integer,String> descriptionList = HTMLparser.ParseDocs(directoryLocation);
		
		if(descriptionList!=null && descriptionList.size()>0)
		{
			//the size of the map is the the total number of documents
			totalNumberOfDocuments = descriptionList.size();
			
			//Build the initial inverse index
			//Format : Each Term-> <List of Document IDs (Duplicates allowed)>
			//e.g.: Term1-><1,1,2,2,2,3,3,3,10> term frequency: 9 Doc Frequency: 4
			Map<String,List<Integer>> inverseIndex = buildInverseIndex(descriptionList);
			System.out.println("Number of terms in dictonary:::"+inverseIndex.size());//total number of terms in dictionary
			
			//Build the actual Term Dictionary to Postings List Mapping (FInal Inverse Index) and write it to file
			//Format: Each Term: DocFrequency, TermFrequency, Map of DocIds and term frequency in that doc (document object)
			//e.g.: for above Term1-> 4,9, [1->2],[2->3]->[3->3]->[10->1]
			Map<String,PostingsList> dictionary = buildPostingsList(inverseIndex);
			JSONObject inverseIndexInJSONFormat = new JSONObject(dictionary);//JSONUtility.writeInverseIndexToJSONObject(dictionary);
			FileUtility.writeJSONObjectToOutputFile(inverseIndexInJSONFormat, "inverseIndex.json");
			
			//Build the document vectors and write the document vectors to text files to be used by search engine
			//Format: Each Document D1=<term1:tf-idf1>,<term2:tf-idf2>,....<termn:tf-idfn> 
			//note: a term will be added to document vector only if the tf-idf != 0.0 to eliminate sparse vectors.
			List<Map<Integer,Double>> documentVectors = buildDocumentVectors(dictionary);
			JSONObject documentVectorInJSONFormat = JSONUtility.writeDocumentVectorToJSONObject(documentVectors);
			FileUtility.writeJSONObjectToOutputFile(documentVectorInJSONFormat, "documentVectors.json");
			
			long endTime = System.currentTimeMillis();
			System.out.println("Time Taken to Index::"+(endTime-startTime)/1000+ " seconds");
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
					if(!stemmedString.equals(""))
					{
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
			double idf = Math.log10((double)totalNumberOfDocuments/docFrequency);
			Map<Integer,Integer> postingsList = getDocFrequencyForEachDoc(docList);
			PostingsList termPostingsList = new PostingsList(termFrequency,docFrequency,idf,postingsList);
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
	 * To build the document vectors for all the documents in the term space.
	 * @param dictionary
	 * @return ArraysList : a List of document vectors
	 */
	private static List<Map<Integer,Double>> buildDocumentVectors(Map<String,PostingsList> dictionary)
	{
		List<Map<Integer,Double>> documentVectors = new ArrayList<Map<Integer,Double>>();
		for(int i=1;i<=totalNumberOfDocuments;i++)
		{
			Map<Integer,Double> documentVector = new TreeMap<Integer,Double>();
			int counter =1;
			for(String term : dictionary.keySet())
			{
				double idf = dictionary.get(term).getIdf();
				Map<Integer,Integer> postingsList = dictionary.get(term).getPostingsList();
				if(postingsList.get(i)!=null && idf!=0.0)
				{
					double tf_idf = postingsList.get(i)*idf;
					documentVector.put(counter, tf_idf);
				}
				counter++;
			}
			documentVectors.add(documentVector);
		}
		return documentVectors;
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
