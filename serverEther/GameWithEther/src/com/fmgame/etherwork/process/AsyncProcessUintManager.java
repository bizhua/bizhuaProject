package com.fmgame.etherwork.process;

import java.util.ArrayList;
import java.util.Collections;
import java.util.Iterator;
import java.util.List;


public class AsyncProcessUintManager {
	private static AsyncProcessUintManager _instance;
	
	public static AsyncProcessUintManager getInstance()
	{
		if(_instance == null)
		{
			_instance = new AsyncProcessUintManager();
		}
		return _instance;
	}
	
	private List<AbstractAsyncProcessUint> _list = Collections.synchronizedList(new ArrayList<AbstractAsyncProcessUint>());
	
//	public void excuteSetsss(int address,long num,String arg)
//	{
//		AsyncProcessUintSetsss sss = new AsyncProcessUintSetsss();
//		sss.setAddress(address);
//		sss.set_setNum(num);
//		sss.set_arg(arg);
//		sss.excuteAction();
//		_list.add(sss);
//	}
	public void addProcessUint(AbstractAsyncProcessUint uint)
	{
		_list.add(uint);
		uint.excuteAction();
	}
	
	public void updata()
	{
		AbstractAsyncProcessUint next;
		synchronized ( _list ) {
			if(_list.size() > 0)
			{
	            Iterator<AbstractAsyncProcessUint> iter = _list.iterator();
	            while ( iter.hasNext() ) {
	            	next = iter.next();
	            	next.updata();
	            }
	            for(int i = 0; i< _list.size();)
	            {
	            	next = _list.get(i);
	            	if(next.isDone())
	            	{
	            		next.release();
	            		_list.remove(i);
	            	}else
	            	{
	            		i++;
	            	}
	            }
			}
        }
	}
}
