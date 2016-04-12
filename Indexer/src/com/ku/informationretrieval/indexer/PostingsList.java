package com.ku.informationretrieval.indexer;

import java.util.Map;
import java.util.TreeMap;

public class PostingsList 
{
	private int termFrequency;
	private int documentFrequency;
	private double idf;
	private Map<Integer,Integer> postingsList;
	
	public PostingsList(int termFrequency,int docFrequency,double idf,Map<Integer,Integer> postingsList)
	{
		this.termFrequency = termFrequency;
		this.documentFrequency = docFrequency;
		this.idf = idf;
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
	public double getIdf() {
		return idf;
	}
	public void setIdf(double idf) {
		this.idf = idf;
	}
	public Map<Integer,Integer> getPostingsList() {
		return postingsList;
	}
	public void setPostingsList(Map<Integer,Integer> postingsList) {
		this.postingsList = postingsList;
	}
}
