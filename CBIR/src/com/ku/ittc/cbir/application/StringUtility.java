package com.ku.ittc.cbir.application;

public class StringUtility 
{
	/***
	 * 
	 * Cleans the given input string
	 * @param str
	 * @return cleaned String
	 */
	public static String cleanDescription(String str)
	{
		String removeHyphens = str.replaceAll("- ", "");
		String alphaOnly = removeHyphens.replaceAll("[^a-zA-Z]+"," ");
		String cleanedString = alphaOnly.replaceAll("[()]"," ");
		//System.out.println(cleanedString);
		return cleanedString.toLowerCase();
	}

}
