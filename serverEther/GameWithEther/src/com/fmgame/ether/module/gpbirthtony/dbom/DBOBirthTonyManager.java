package com.fmgame.ether.module.gpbirthtony.dbom;

import java.util.Map;
import java.util.concurrent.ConcurrentHashMap;

import com.fmgame.ether.module.gpbirthtony.dao.BirthTonyData;

public class DBOBirthTonyManager {
	private static DBOBirthTonyManager _instance;
	public static DBOBirthTonyManager getInstance()
	{
		if(_instance == null)
		{
			_instance = new DBOBirthTonyManager();
		}
		return _instance;
	}
	
	private Map<Long, BirthTonyData> birthTonyDataMap = new ConcurrentHashMap<Long, BirthTonyData>();
	
	public void addData(long asyncId,BirthTonyData data)
	{
		if(!birthTonyDataMap.containsKey(asyncId))
		{
			birthTonyDataMap.put(asyncId, data);
		}
	}
	
	public void removeData(long asyncId)
	{
		birthTonyDataMap.remove(asyncId);
	}
	
	public BirthTonyData getData(long asyncId)
	{
		return birthTonyDataMap.get(asyncId);
	}
}
