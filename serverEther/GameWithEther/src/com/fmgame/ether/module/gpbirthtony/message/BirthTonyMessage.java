package com.fmgame.ether.module.gpbirthtony.message;

import org.apache.mina.core.buffer.IoBuffer;

import com.fmgame.ether.net.MsgIdConstant;
import com.fmgame.platform.net.current.AbstractTCPMessage;
import com.fmgame.platform.net.current.ConfMsgAction;
@ConfMsgAction(msgId = MsgIdConstant.BIRTH_TONY_MESSAGE,action = BirthTonyMessageAction.class)
public class BirthTonyMessage extends AbstractTCPMessage {
    private int[] gens;
    private String tonyOwnerAddress;
    private short character;
    private long asyncId;
    
	private boolean flag;
	private long tonyId;
	@Override
	public void decodeBody(IoBuffer in) throws Exception {
		// TODO Auto-generated method stub
		asyncId = in.getLong();
		tonyOwnerAddress = getString(in);
		character = in.getShort();
		gens = new int[14];
		for(int i = 0; i < 14; i++)
		{
			gens[i] = in.getInt();
		}
	}

	@Override
	public void encodeBody(IoBuffer out) {
		// TODO Auto-generated method stub
		putBoolean(out, flag);
		if(flag)
		{
			putString(out, tonyOwnerAddress);
			out.putLong(tonyId);
			out.putShort(character);
			for(int i = 0; i < 14; i++)
			{
				out.putInt(gens[i]);
			}
		}
	}

	public void setFlag(boolean flag) {
		this.flag = flag;
	}

	public int[] getGens() {
		return gens;
	}

	public void setGens(int[] gens) {
		this.gens = gens;
	}

	public short getCharacter() {
		return character;
	}

	public void setCharacter(short character) {
		this.character = character;
	}

	public String getTonyOwnerAddress() {
		return tonyOwnerAddress;
	}

	public void setTonyOwnerAddress(String tonyOwnerAddress) {
		this.tonyOwnerAddress = tonyOwnerAddress;
	}

	public void setTonyId(long tonyId) {
		this.tonyId = tonyId;
	}

	public long getAsyncId() {
		return asyncId;
	}

	public void setAsyncId(long asyncId) {
		this.asyncId = asyncId;
	}
}
