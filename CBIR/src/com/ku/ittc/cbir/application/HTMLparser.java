package com.ku.ittc.cbir.application;

import java.io.BufferedReader;
import java.io.File;
import java.io.FileNotFoundException;
import java.io.FileOutputStream;
import java.io.FileReader;
import java.io.IOException;
import java.io.PrintWriter;
import java.io.UnsupportedEncodingException;
import java.util.HashMap;
import java.util.Map;
import java.util.Properties;

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
	public static Map<Integer,String> ParseDocs(String directoryLocation){
		/*
		 * read directory
		 */
		File file = new File(directoryLocation);
		Properties fileMapping = new Properties();
		Map<Integer, String> handler = new HashMap<Integer, String>(); 
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
				writeTofile(cleanedText, String.valueOf(i)+".txt");
				handler.put((Integer)i, readFromFile(String.valueOf(i)+".txt"));

				//create a mapping between document name and document ID created.
				fileMapping.setProperty(String.valueOf(i),eachFile.getPath());

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
		try{
			FileOutputStream out = new FileOutputStream("FileMappings.properties");
			fileMapping.store(out, null);
			out.close();
		}catch(IOException ioe){
			ioe.printStackTrace();
		}
		return handler;
	}


}
