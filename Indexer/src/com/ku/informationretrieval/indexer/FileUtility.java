package com.ku.informationretrieval.indexer;

import java.io.BufferedWriter;
import java.io.File;
import java.io.FileWriter;
import java.io.IOException;
import java.util.List;
import java.util.Map;

//import org.json.simple.JSONObject;

import com.fasterxml.jackson.databind.ObjectMapper;
import com.fasterxml.jackson.databind.SerializationFeature;

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
				sb.append(pl.getIdf());
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

	public static void writeDocumentVectorsToFile(String fileName, List<Map<Integer, Double>> documentVectors)
	{
		try
		{
			File file = new File(fileName);
			file.createNewFile();
			
			FileWriter fw = new FileWriter(file.getAbsoluteFile());
			BufferedWriter bw = new BufferedWriter(fw);
			int counter = 1;
			for(Map<Integer,Double> documentVector : documentVectors)
			{
				StringBuilder sb = new StringBuilder();
				sb.append(counter).append("=");
				for(Integer term : documentVector.keySet())
				{
					sb.append(term).append(":").append(documentVector.get(term)).append(",");
				}
				bw.write(sb.toString());
				bw.newLine();
				counter++;
			}
			bw.close(); 
		} 
		catch (IOException e) 
		{
			e.printStackTrace();
		}
		System.out.println("Document Vectors Saved to disk!!!");
	}
	
	/**
	 * This method will writes the identified dependencies in the JSON object to an output file
	 * @param JSONObject dependencies in the JSON object format
	 * 
	 */
	public static void writeJSONObjectToOutputFile(JSONObject resultInJSONFormat,String outputFile)
	{
		FileWriter writer = null;
		try 
		{
			File file = new File(outputFile);
			
			writer = new FileWriter(file);
			//in pretty printable format
			ObjectMapper mapper = new ObjectMapper();
			mapper.configure(SerializationFeature.INDENT_OUTPUT, true);
			mapper.writeValue(writer, resultInJSONFormat);
			System.out.println("Successfully Copied JSON Object to File...");
		} 
		catch (IOException e) 
		{
			e.printStackTrace();

		} 
		finally 
		{
			try {writer.close();} catch (IOException e) {e.printStackTrace();}
		}	
	}
	
}
