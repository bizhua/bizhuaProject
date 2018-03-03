package com.fmgame.etherwork.eventlisten;

import org.web3j.protocol.core.DefaultBlockParameterName;

import com.fmgame.ether.module.gpbirthtony.service.GPBirthTonyService;
import com.fmgame.etherwork.ContractManager;
import com.fmgame.etherwork.EtherUtilTools;
import com.fmgame.platform.service.GameServiceManager;

import rx.Subscription;

public class CoinPawsGameEventManager {
	
	private static CoinPawsGameEventManager _instance;
	
	public static CoinPawsGameEventManager getInstance()
	{
		if(_instance == null)
		{
			_instance = new CoinPawsGameEventManager();
		}
		return _instance;
	}
	
	private Subscription subscriptionEventBirthRctl;
	public void startListen()
	{
		subscriptionEventBirthRctl = ContractManager.getInstance().getRobotContract()
				.eBirthRctlEventObservable(DefaultBlockParameterName.EARLIEST, DefaultBlockParameterName.LATEST).subscribe(
						tx->{
							GPBirthTonyService service = GameServiceManager.getComponentManager().getService(GPBirthTonyService.class);
							service.birthTonyRespByObsever(tx._asyncId.longValue(),tx._owner, EtherUtilTools.getGensintFast(tx._outline), 
									tx._character.shortValue(), tx._tonyId.longValue());;
						});
	}
	
	public void removeListen()
	{
		if(subscriptionEventBirthRctl != null)
		{
			subscriptionEventBirthRctl.unsubscribe();
		}
	}
}
