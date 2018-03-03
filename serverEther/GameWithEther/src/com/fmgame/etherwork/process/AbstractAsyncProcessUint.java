package com.fmgame.etherwork.process;

import java.util.concurrent.CompletableFuture;

import org.web3j.protocol.core.methods.response.TransactionReceipt;

public abstract class AbstractAsyncProcessUint {
	
    protected boolean _isDone = false;
    protected int _addressUserId;
	protected CompletableFuture<TransactionReceipt> transactionReceiptAsync;
	
	public void updata()
	{
		if(transactionReceiptAsync !=null && !_isDone)
		{
			if(transactionReceiptAsync.isDone())
			{
				process();
				_isDone = true;
			}else if(transactionReceiptAsync.isCompletedExceptionally())
			{
				processException();
				_isDone = true;
			}
		}
	}
	public abstract void excuteAction();
	protected abstract void process();
	protected abstract void processException();
	
	public void release()
	{
		transactionReceiptAsync = null;
		_isDone = false;
	}
	
	public boolean isDone()
	{
		return _isDone;
	}
	
	public void setAddressUserId(int addressUserId)
	{
		_addressUserId = addressUserId;
	}
}
