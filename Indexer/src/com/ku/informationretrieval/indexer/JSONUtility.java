/**
 * 
 */
package com.ku.informationretrieval.indexer;

import java.util.LinkedHashMap;
import java.util.List;
import java.util.Map;

import org.json.simple.JSONArray;


public class JSONUtility 
{
	@SuppressWarnings("unchecked")
	public static JSONObject writeInverseIndexToJSONObject(Map<String,PostingsList> dictionary)
	{
		Map<String,Object> jsonObjectMap = new LinkedHashMap<String,Object>();
		JSONArray inverseIndex = new JSONArray();
		for(Map.Entry<String,PostingsList> term : dictionary.entrySet())
		{
			JSONObject dictionaryTerm = new JSONObject();
			dictionaryTerm.put("term", term.getKey());
			PostingsList postingsList = term.getValue();
			dictionaryTerm.put("DF",postingsList.getDocumentFrequency());
			dictionaryTerm.put("IDF",postingsList.getIdf());
			dictionaryTerm.put("TF",postingsList.getTermFrequency());
			Map<Integer,Integer> pl = postingsList.getPostingsList();
			JSONObject eachPostingsList =new JSONObject(pl);
			dictionaryTerm.put("PostingsList",eachPostingsList);
			inverseIndex.add(dictionaryTerm);
		}
		jsonObjectMap.put("inverseIndex",inverseIndex);
		JSONObject rootJSON = new JSONObject(jsonObjectMap);
		return rootJSON;
	}
	
	public static JSONObject writeDocumentVectorToJSONObject(List<Map<Integer, Double>> documentVectors)
	{
		Map<Integer,Object> documentVectorsMap = new LinkedHashMap<Integer,Object>();
		int counter=1;
		for(Map<Integer,Double> documentVector : documentVectors)
		{
			JSONObject termDimensions = new JSONObject(documentVector);
			documentVectorsMap.put(counter,termDimensions);
			counter++;
		}
		JSONObject rootJSON = new JSONObject(documentVectorsMap);
		return rootJSON;
		
	}
	

}
