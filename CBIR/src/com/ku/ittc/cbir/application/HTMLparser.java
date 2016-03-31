package com.ku.ittc.cbir.application;

import java.io.BufferedReader;
import java.io.File;
import java.io.FileNotFoundException;
import java.io.FileOutputStream;
import java.io.FileReader;
import java.io.IOException;
import java.io.PrintWriter;
import java.io.UnsupportedEncodingException;
import java.util.ArrayList;
import java.util.Arrays;
import java.util.HashMap;
import java.util.Map;
import java.util.Properties;

public class HTMLparser {
	public static void writeTofile(ArrayList<String> content,String file) throws FileNotFoundException, UnsupportedEncodingException{
		PrintWriter writer = new PrintWriter(file, "UTF-8");
		for(String item: content){
			writer.println(item);
		}
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
				while (( line = input.readLine()) != null) {
					contents.append(line);
					contents.append(System.getProperty("line.separator"));
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
	public static Map<Integer,String> ParseDocs(){
		String directoryLocation="/Users/Anil/Downloads/docsnew/";
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
		//for(File indi: files){
		for(int i=1;i<92;i++){
			//	System.out.println(files[i]);

			/*
			 * read contents of file
			 */
			BufferedReader reader = null;
			try{
				reader = new BufferedReader(new FileReader(files[i]));
				String line = null;
				final String pattern = "<(\"[^\"]*\"|'[^']*'|[^'\">])*>";
				final String escape = "(?s)<!.*?(/>|<-->)";
				final String scriptPattern = "(?s)<script.*?(/>|</script>)";
				StringBuilder sb = new StringBuilder();

				while(true){
					line = reader.readLine();
					sb.append(line);
					if(line ==null){
						//System.out.println("No contents in the file");
						break;
					}
				}
				line = sb.toString();
				//System.out.println(line.replaceAll(scriptPattern, "").replaceAll(escape, "").replaceAll(pattern, " ").replaceAll("\t", ""));
				ArrayList<String> wordList = new ArrayList<String>(Arrays.asList(line.replaceAll(scriptPattern, "").replaceAll(escape, "").replaceAll(pattern, " ").replaceAll("&#160;","").replaceAll("\t", "").split("\\s+")));

				/*for(int x=0;x<wordList.size();x++){
					System.out.println(wordList.get(x));
				}*/
				/*
				 * write to file
				 */
				writeTofile(wordList, String.valueOf(i)+".txt");
				handler.get((Integer)i);

				handler.put((Integer)i, readFromFile(String.valueOf(i)+".txt"));


				fileMapping.setProperty(String.valueOf(files[i]),String.valueOf(i));

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
