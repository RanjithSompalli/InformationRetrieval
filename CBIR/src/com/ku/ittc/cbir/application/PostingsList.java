package com.ku.ittc.cbir.application;

import java.util.Map;
import java.util.TreeMap;

public class PostingsList 
{
	private int termFrequency;
	private int documentFrequency;
	private Map<Integer,Integer> postingsList;
	
	public PostingsList(int termFrequency,int docFrequency,Map<Integer,Integer> postingsList)
	{
		this.termFrequency = termFrequency;
		this.documentFrequency = docFrequency;
		this.postingsList = postingsList;
	}
	public PostingsList()
	{
		postingsList = new TreeMap<Integer,Integer>();
	}
	public int getTermFrequency() {
		return termFrequency;
	}
	public void setTermFrequency(int termFrequency) {
		this.termFrequency = termFrequency;
	}
	public int getDocumentFrequency() {
		return documentFrequency;
	}
	public void setDocumentFrequency(int documentFrequency) {
		this.documentFrequency = documentFrequency;
	}
	public Map<Integer,Integer> getPostingsList() {
		return postingsList;
	}
	public void setPostingsList(Map<Integer,Integer> postingsList) {
		this.postingsList = postingsList;
	}
}
