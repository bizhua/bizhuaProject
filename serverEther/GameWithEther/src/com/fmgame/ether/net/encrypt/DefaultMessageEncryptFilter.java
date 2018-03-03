package com.fmgame.ether.net.encrypt;

import org.apache.mina.core.buffer.IoBuffer;
import org.apache.mina.core.filterchain.IoFilterAdapter;
import org.apache.mina.core.session.IoSession;
import org.apache.mina.core.write.WriteRequest;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;

import com.fmgame.ether.util.SessionKey;
import com.fmgame.platform.net.current.AbstractTCPMessage;

/**
 * 默认消息加密过滤器
 */
public class DefaultMessageEncryptFilter extends IoFilterAdapter {
	
	private static final Logger logger = LoggerFactory.getLogger(DefaultMessageEncryptFilter.class);
	
	@Override
	public void messageReceived(NextFilter nextFilter, IoSession session,Object message) throws Exception {
		IoBuffer in = (IoBuffer)message;
		short commandId = in.getShort(AbstractTCPMessage.MESSAGE_DECODE_COMMANDID_INDEX);
		
		Object o = session.getAttribute(SessionKey.MSG_SEQUENCE);
		if (o == null) {
			
			session.setAttribute(SessionKey.MSG_SEQUENCE, commandId);
		}
		
		nextFilter.messageReceived(session, message);
	}
	
	@Override
	public void filterWrite(NextFilter nextFilter, IoSession session, WriteRequest writeRequest) throws Exception {
		nextFilter.filterWrite(session, writeRequest);
	}
	
}