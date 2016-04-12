package com.ku.informationretrieval.indexer;

import java.util.LinkedHashMap;
import java.util.Map;


@SuppressWarnings("rawtypes")
public class JSONObject extends LinkedHashMap implements Map{
	
	private static final long serialVersionUID = -503443796854799292L;
	
	
	public JSONObject() {
		super();
	}

	/**
	 * Allows creation of a JSONObject from a Map. After that, both the
	 * generated JSONObject and the Map can be modified independently.
	 * 
	 * @param map
	 */
	@SuppressWarnings("unchecked")
	public JSONObject(Map map) {
		super(map);
	}
}
