package com.ku.ittc.cbir.application;

import java.io.BufferedWriter;
import java.io.File;
import java.io.FileWriter;
import java.io.IOException;
import java.util.Map;

public class FileUtility 
{
	public static void writeInverseIndexToFile(String fileName, Map<String,PostingsList> dictionary)
	{
		try
		{
			File file = new File(fileName);
			file.createNewFile();
			
			FileWriter fw = new FileWriter(file.getAbsoluteFile());
			BufferedWriter bw = new BufferedWriter(fw);
			for(Map.Entry<String,PostingsList> term : dictionary.entrySet())
			{
				
				StringBuilder sb = new StringBuilder(term.getKey());
				sb.append(":");
				PostingsList pl = term.getValue();
				sb.append(pl.getDocumentFrequency());
				sb.append(",");
				sb.append(pl.getTermFrequency());
				sb.append(",");
				Map<Integer,Integer> postingsList = pl.getPostingsList();
				for(int id : postingsList.keySet())
				{
					sb.append(id);
					sb.append("->");
					sb.append(postingsList.get(id));
					sb.append(",");
				}
				bw.write(sb.toString());
				bw.newLine();
			}
			bw.close(); 
		} 
		catch (IOException e) 
		{
			e.printStackTrace();
		}
		System.out.println("Inverse Index Saved to disk!!!");
	}

	public static void writeTermIDF(String fileName, Map<String, Double> termIDFMap) 
	{
		try
		{
			File file = new File(fileName);
			file.createNewFile();

			FileWriter fw = new FileWriter(file.getAbsoluteFile());
			BufferedWriter bw = new BufferedWriter(fw);

			for(Map.Entry<String,Double> term : termIDFMap.entrySet())
			{
				StringBuilder sb = new StringBuilder();
				sb.append(term.getKey()+":"+term.getValue());
				bw.write(sb.toString());
				bw.newLine();
			}
			bw.close(); 
		} 
		catch (IOException e) 
		{
			e.printStackTrace();
		}
		System.out.println("IDF file Saved to disk!!!");
	}
}
