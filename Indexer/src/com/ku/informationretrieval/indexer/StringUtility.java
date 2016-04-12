package com.ku.informationretrieval.indexer;

public class StringUtility 
{
	/***
	 * 
	 * Cleans the given input string
	 * @param str
	 * @return cleaned String
	 */
	public static String cleanText(String str)
	{
		String removeHyphens = str.replaceAll("- ", "");
		String alphaOnly = removeHyphens.replaceAll("[^a-zA-Z]+"," ");
		String cleanedString = alphaOnly.replaceAll("[()]"," ");
		//System.out.println(cleanedString);
		return cleanedString.toLowerCase();
	}

}
