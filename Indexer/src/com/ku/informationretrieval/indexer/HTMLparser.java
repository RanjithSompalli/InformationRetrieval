package com.ku.informationretrieval.indexer;

import java.io.BufferedReader;
import java.io.File;
import java.io.FileNotFoundException;
import java.io.FileReader;
import java.io.IOException;
import java.io.PrintWriter;
import java.io.UnsupportedEncodingException;
import java.util.HashMap;

import java.util.Map;


public class HTMLparser {
	
	public static void writeTofile(String content,String file) throws FileNotFoundException, UnsupportedEncodingException{
		PrintWriter writer = new PrintWriter(file, "UTF-8");
		writer.println(content);
		writer.close();
	}
	public static String readFromFile(String fileName) throws FileNotFoundException{
		File filename = new File(fileName); 
		// Checks if file exists
		if (!filename.exists()) {
			throw new FileNotFoundException ("File does not exist: " + filename);
		}
		StringBuilder contents = new StringBuilder();
		try {
			BufferedReader input =  new BufferedReader(new FileReader(filename));
			try {
				String line = null;
				while ((line = input.readLine()) != null) {
					contents.append(line);
				}
			}
			finally {
				input.close();
			}
		}
		catch (IOException ex){
			ex.printStackTrace();
		}
		return contents.toString();
	}
	@SuppressWarnings("unchecked")
	public static Map<Integer,String> ParseDocs(String directoryLocation){
		/*
		 * read directory
		 */
		File file = new File(directoryLocation);
		JSONObject URLMappings = new JSONObject();
		Map<Integer, String> descriptionsList = new HashMap<Integer, String>(); 
		/*
		 * read all files in the directory
		 */
		
		File []files = file.listFiles();

		/*
		 * loop through the files to read its contents
		 */
		int i =1;
		for(File eachFile : files)
		{
			/*
			 * read contents of file
			 */
			BufferedReader reader = null;
			try{
				reader = new BufferedReader(new FileReader(eachFile));
				String line = null;
				final String pattern = "<(\"[^\"]*\"|'[^']*'|[^'\">])*>";
				final String escape = "(?s)<!.*?(/>|<-->)";
				final String scriptPattern = "(?s)<script.*?(/>|</script>)";
				StringBuilder sb = new StringBuilder();
				
				while((line=reader.readLine())!=null)
					sb.append(line);
				line = sb.toString();
				String parsedString = line.replaceAll(scriptPattern, "").replaceAll(escape, "").replaceAll(pattern, " ").replaceAll("&#160;","").replaceAll("\t", "");
				String cleanedText = StringUtility.cleanText(parsedString);
				
				/*
				 * write to file
				 */
				//writeTofile(cleanedText, String.valueOf(i)+".txt");
				descriptionsList.put(i, cleanedText);

				//create a mapping between document name and document ID created.
				URLMappings.put(i, "docs/"+eachFile.getName());
				
			}catch(Exception e){
				e.getStackTrace();
			}finally{
				if(reader !=null){
					try{
						reader.close();
					}catch(IOException io){
						io.printStackTrace();
					}
				}
			}
			i++;
		}
		FileUtility.writeJSONObjectToOutputFile(URLMappings, "URLMappings.json");
		return descriptionsList;
	}


}
