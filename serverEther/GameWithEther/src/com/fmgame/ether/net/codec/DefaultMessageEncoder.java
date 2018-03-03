package com.fmgame.ether.net.codec;

import org.apache.mina.core.session.IoSession;
import org.apache.mina.filter.codec.ProtocolEncoderAdapter;
import org.apache.mina.filter.codec.ProtocolEncoderOutput;
import org.slf4j.LoggerFactory;
import org.slf4j.Logger;

/**
 * 基于Mina消息编码类
 */
public class DefaultMessageEncoder extends ProtocolEncoderAdapter {
	
	private static final Logger logger = LoggerFactory.getLogger(DefaultMessageEncoder.class);

	@Override
	public void encode(IoSession session, Object message, ProtocolEncoderOutput out)
			throws Exception {
		logger.error("编码: message=" + message);
		
	}
}// Class DefaultMessageEncoder
//
//public class DefaultMessageEncoder<T extends AbstractMessage> implements
//		MessageEncoder<T> {
//	private static final Logger logger = Logger.getLogger(DefaultMessageEncoder.class);
////	@Override
//	public void encode(IoSession session, T message, ProtocolEncoderOutput out)
//			throws Exception {
//
////		//创建IOBuffer
////		IoBuffer buffer = IoBuffer.allocate(1024);
////		buffer.setAutoExpand(true);
////
////		//设置协议头标志
//////		int length = AbstractMessage.MESSAGE_SPLIT.length;
////		buffer.put(AbstractMessage.MESSAGE_SPLIT);
////		
//////		AbstractMessage m = (AbstractMessage) message;
////		//设置协议头数据
////		message.encodeHeader(buffer);
////		//设置协议体数据
////		message.encodeBody(buffer);
////		
////		int totalLength = buffer.position();
////		int bodyPos=AbstractMessage.TOTALLENGTH_POS;//AbstractMessage.HEADER_LENGTH + AbstractMessage.ROUTER_LENGTH;
////		totalLength -= bodyPos;
////		
////		//设置消息体数据长度
////		buffer.putShort(bodyPos, (short)totalLength);
////		
////		buffer.flip();
////		
////		out.write(buffer);
//		IoBuffer buffer = message.encodeIoBuffer();
//		if(buffer!=null)
//			out.write(buffer);
//		else
//			logger.error("发生消息体编码错误。。" + buffer + "\tbuffer-length: " + buffer);
//			
////		buffer.free();
//	}
//}// Class DefaultMessageEncoder
