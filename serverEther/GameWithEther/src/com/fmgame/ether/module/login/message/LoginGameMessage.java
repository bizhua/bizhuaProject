package com.fmgame.ether.module.login.message;

import org.apache.mina.core.buffer.IoBuffer;

import com.fmgame.ether.net.MsgIdConstant;
import com.fmgame.platform.net.current.AbstractTCPMessage;
import com.fmgame.platform.net.current.ConfMsgAction;
@ConfMsgAction(msgId = MsgIdConstant.LOGIN_MESSAGE,action = LoginGameMessageAction.class)
public class LoginGameMessage extends AbstractTCPMessage {

	// 用户ID
	public int userId;
	public String pass;
	
	public boolean returnFlag;
	public void setReturnFlag(boolean returnFlag) {
		this.returnFlag = returnFlag;
	}

	@Override
	public void decodeBody(IoBuffer in) throws Exception {
		// TODO Auto-generated method stub
		userId = in.getInt();
		pass = getString(in);
	}

	@Override
	public void encodeBody(IoBuffer out) {
		// TODO Auto-generated method stub
		putBoolean(out, returnFlag);
	}

}
