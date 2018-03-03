package com.fmgame.ether.module.gpbirthtony.service;

import java.util.List;

import com.fmgame.ether.main.user.User;
import com.fmgame.ether.main.user.UserCacheManagerService;
import com.fmgame.ether.module.gpbirthtony.message.BirthTonyMessage;
import com.fmgame.ether.net.MsgIdConstant;
import com.fmgame.etherwork.process.AsyncProcessUintManager;
import com.fmgame.etherwork.process.BirthTonyAsyncProcessUint;
import com.fmgame.platform.component.ComponentServiceAdapter;
import com.fmgame.platform.net.current.MessageFactory;

public class GPBirthTonyService extends ComponentServiceAdapter {

      public void birthTony(long asyncId,User user,String tonyOwnerAddress,int[] gens,short character)
      {
    	  BirthTonyAsyncProcessUint uint = new BirthTonyAsyncProcessUint();
    	  uint.setAsyncId(asyncId);
    	  uint.setAddressUserId(user.get_id());
    	  uint.setCharacter(character);
    	  uint.setGens(gens);
    	  uint.setTonyOwnerAddress(tonyOwnerAddress);
    	  AsyncProcessUintManager.getInstance().addProcessUint(uint);
      }
      
      public void birthTonyResp(long asyncId,boolean flag,int userId,String tonyOwnerAddress,int[] gens,short character,long tonyId)
      {
    	  BirthTonyMessage message = MessageFactory.getInstance().getMessage(MsgIdConstant.BIRTH_TONY_MESSAGE);
    	  message.setFlag(flag);
    	  message.setTonyOwnerAddress(tonyOwnerAddress);
    	  if(flag)
    	  {
    		  message.setCharacter(character);
    		  message.setGens(gens);
    		  message.setTonyId(tonyId);
    	  }
    	  User user = UserCacheManagerService.getUserById(userId);
    	  if(user != null)
    	  {
    		  user.sendMsg(message);
    	  }
      }
      
      public void birthTonyRespByObsever(long asyncId,String ownerAddress,int[] gens,short character,long tonyId)
      {
    	  BirthTonyMessage message = MessageFactory.getInstance().getMessage(MsgIdConstant.BIRTH_TONY_MESSAGE);
    	  message.setFlag(true);
    	  message.setTonyOwnerAddress(ownerAddress);
    	  message.setCharacter(character);
    	  message.setGens(gens);
    	  message.setTonyId(tonyId);
    	  List<User> uList = UserCacheManagerService.getAllUser();
    	  if(uList != null&& uList.size() > 0)
    	  {
    		  for(User u : uList)
    		  {
    			  if(u != null)
    			  {
    				  u.sendMsg(message);
    			  }
    		  }
    	  }
      }
      
}
