package com.fmgame.etherwork.process;

import java.math.BigInteger;
import java.util.List;



import java.util.concurrent.ExecutionException;

import org.web3j.protocol.core.methods.response.TransactionReceipt;

import com.fmgame.ether.module.gpbirthtony.service.GPBirthTonyService;
import com.fmgame.etherwork.ContractManager;
import com.fmgame.etherwork.EtherUtilTools;
import com.fmgame.etherwork.contract.CoinPawsGame;
import com.fmgame.platform.service.GameServiceManager;

public class BirthTonyAsyncProcessUint extends AbstractAsyncProcessUint {
	private long asyncId;
	private int[] gens;
    private String tonyOwnerAddress;
    private short character;
	public void setGens(int[] gens) {
		this.gens = gens;
	}
	public void setTonyOwnerAddress(String tonyOwnerAddress) {
		this.tonyOwnerAddress = tonyOwnerAddress;
	}
	public void setCharacter(short character) {
		this.character = character;
	}
	public void setAsyncId(long asyncId) {
		this.asyncId = asyncId;
	}
	@Override
	public void excuteAction() {
		// TODO Auto-generated method stub
		if(gens!= null)
		{
			transactionReceiptAsync = ContractManager.getInstance().getRobotContract().createTonyByRobot(BigInteger.valueOf(asyncId),tonyOwnerAddress, 
				BigInteger.valueOf(character), EtherUtilTools.getGensUint256BigIntegerFast(gens)).sendAsync();
		}
	}
	@Override
	protected void process() {
		// TODO Auto-generated method stub
		TransactionReceipt resp;
		try {
			resp = transactionReceiptAsync.get();
	        List<CoinPawsGame.EBirthRctlEventResponse> respList = ContractManager.getInstance().getRobotContract().getEBirthRctlEvents(resp);
	        if(respList.size() > 0)
	        {
	        	for(CoinPawsGame.EBirthRctlEventResponse r : respList)
	        	{
	        		GPBirthTonyService service = GameServiceManager.getComponentManager().getService(GPBirthTonyService.class);
	        		service.birthTonyResp(r._asyncId.longValue(),true,_addressUserId, r._owner,EtherUtilTools.getGensintFast(r._outline), r._character.shortValue(), r._tonyId.longValue());
	        	}
	        }
		} catch (InterruptedException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
			processException();
		} catch (ExecutionException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
			processException();
		}
	}

	@Override
	protected void processException() {
		// TODO Auto-generated method stub
		GPBirthTonyService service = GameServiceManager.getComponentManager().getService(GPBirthTonyService.class);
		service.birthTonyResp(asyncId,false,_addressUserId,tonyOwnerAddress, gens, character, 0);
	}

}
