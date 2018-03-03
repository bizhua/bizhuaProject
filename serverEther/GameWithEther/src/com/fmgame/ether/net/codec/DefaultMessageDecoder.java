package com.fmgame.ether.net.codec;

import org.apache.mina.core.buffer.IoBuffer;
import org.apache.mina.core.session.IoSession;
import org.apache.mina.filter.codec.CumulativeProtocolDecoder;
import org.apache.mina.filter.codec.ProtocolDecoderOutput;

/**
 * 基于Mina消息解码类
 */
public class DefaultMessageDecoder extends CumulativeProtocolDecoder {

//	private static final int MAX_INCOMMING_COMMAND_LENGTH=5120;
	
	public DefaultMessageDecoder()
	{
	}
	@Override
	public boolean doDecode(IoSession session, IoBuffer in,
			ProtocolDecoderOutput out) throws Exception {
		/**
		 * 消息格式定义： 消息头：0x24,0x25 
		 * 消息数据长度（包括serverId+sessionId）: short messageLength
		 * 消息实际接收处理的服务器Id: byte serverId 
		 * 连接会话唯一标识：int sessionId
		 * 
		 * 消息标识: short commandId 消息数据体：byte[messageLength - serverId - sessionId -
		 * commandId]
		 */
//		logger.error("解码: in=" + in);
		
//		short headerFlag=AbstractMessage.MESSAGE_HEADER_FLAG;
		int start=in.position();
		int limit=in.limit();
//		
//		//如果剩余少于2字节就等待消息头标识
//		if(limit-start<2)
//			return false;
//		
//		//查找消息头标识0x2425
		int i=start;
//		while(i<limit-1)
//		{
//			if(in.getShort(i)==headerFlag)
//				break;
//			i++;
//		}
//
//		//设置指针指向消息头标识
//		in.position(i);
//		//如果没有找到则跳过
//		if(i==limit-1)
//		{
//			return true;
//		}
//
//		i++;
//		i++;
//
		//如果剩余少于2字节就等待消息体长度
		if (limit - i < 2)
			return false;
		
		//读取消息体长度
		short messageLength = in.getShort(i);

//		i++;
//		i++;

		//如果messageLength包含长度的2字节就减掉2字节
//		messageLength-=2;
		
		//如果剩余少于消息体长度就等待消息体数据
		if(limit-i<messageLength)
			return false;
		
		//将消息体剪切出来以IoBuffer类型发送给后续逻辑处理
		IoBuffer message = in.getSlice(start, messageLength);
		out.write(message);

		//强制调过本次数据块,矫正指针位置
		in.position(start+messageLength);

//		logger.error("解码: message=" + message);
		
		return true;
	}
}// Class DefaultMessageDecoder

//public class DefaultMessageDecoder implements MessageDecoder {
//	// private int position=0;
//
//	private final static Logger logger = Logger
//			.getLogger(DefaultMessageDecoder.class);
//
//	@Override
//	public MessageDecoderResult decodable(IoSession session, IoBuffer in) {
//		if (session == null || in == null)
//			return MessageDecoderResult.NOT_OK;
//		if (!session.isConnected())
//			return MessageDecoderResult.NOT_OK;
////		System.out.println(in.getHexDump());
//		/** 检查buffer数据长度是否足够用于检查消息头 */
//		int length = AbstractMessage.HEADER_FLAG_LENGTH;
//		int remaining = in.remaining();
////		System.err.println("收到消息长度: " + remaining);
//		if (remaining < length) {
//			return MessageDecoderResult.NEED_DATA;
//		}
//
//		/** 检查buffer数据内容起始是否是我们定义的消息头：0x24, 0x25 */
//		for (int i = 0; i < length; i++) {
//			byte b=in.get();
//			if (AbstractMessage.MESSAGE_SPLIT[i] != b) {
//				logger.error("****强制关闭会话**** : 消息头错误:"+b);
//				session.close(true);
//				return MessageDecoderResult.NOT_OK;
//			}
//		}
//
//		if (in.remaining() < 2) {
//			return MessageDecoderResult.NEED_DATA;
//		}
//
//		short messageLength = in.getShort();
//		if (messageLength > 8192) {
//			logger.error("****强制关闭会话**** : 消息体长度超过8 * 1024字节");
//			session.close(true);
//			return MessageDecoderResult.NOT_OK;
//		}
//
//		if (in.remaining() >= messageLength - 2) {
//			
//			return MessageDecoderResult.OK;
//		} else {
//			return MessageDecoderResult.NEED_DATA;
//		}
//		// // 检验是否有消息头信息
//		// length = AbstractMessage.HEADER_DATA_LENGTH;
//		// if (in.remaining() < length) {
//		// // System.out.println("******消息头消息头数据长度不够,等待数据");
//		// return MessageDecoderResult.NEED_DATA;
//		// }
//		// // 跳过消息头信息
//		// index = 0;
//		// while (index++ < length) {
//		// in.get();
//		// }
//		//
//		// // 检验是否有消息体数据长度
//		// if (in.remaining() < 2) {
//		// // System.out.println("******消息体数据长度2长度不够,等待数据");
//		// return MessageDecoderResult.NEED_DATA;
//		// }
//		// // 读取消息体数据长度
//		// int totalLength = in.getShort();
//		// if (totalLength >= 2000) {
//		// logger.error("****强制关闭会话**** : 消息体长度超过2000字节");
//		// session.close(true);
//		// return MessageDecoderResult.NOT_OK;
//		// }
//		// // 检验是否数据已就绪
//		// if (in.remaining() < totalLength - 2) {
//		// // System.out.println("******消息体数据长度不够,等待数据");
//		// return MessageDecoderResult.NEED_DATA;
//		// } else {
//		// return MessageDecoderResult.OK;
//		// }
//	}
//
//	@Override
//	public MessageDecoderResult decode(IoSession session, IoBuffer in,
//			ProtocolDecoderOutput out) throws Exception {
//		/**
//		 * 消息格式定义： 消息头：0x24,0x25 
//		 * 消息数据长度（包括serverId+sessionId）: short messageLength
//		 * 消息实际接收处理的服务器Id: byte serverId 
//		 * 连接会话唯一标识：int sessionId
//		 * 
//		 * 消息标识: short commandId 消息数据体：byte[messageLength - serverId - sessionId -
//		 * commandId]
//		 */
//
//		if (session == null || in == null)
//			return MessageDecoderResult.NOT_OK;
//		if (!session.isConnected())
//			return MessageDecoderResult.NOT_OK;
//
//		// 读取消息头标志
//		int index = 0;
//		int length = AbstractMessage.HEADER_FLAG_LENGTH;
//		while (index++ < length) {
//			in.get();
//		}
//
//		short messageLength = in.getShort();
//
//		byte serverId = in.get();
//		int sessionId = in.getInt();
//
//		int commandId = in.getShort();
//
//		AbstractMessage message = MessageFactory.getMessage(commandId);
//		if (message == null) {
//			logger.error("****强制关闭会话**** : 没有找到对应的消息[commandId=" + commandId
//					+ "]");
//			session.close(true);
//			return MessageDecoderResult.NOT_OK;
//		}
//
//		message.setServerId(serverId);
//		message.setSessionId(sessionId);
//		// message的数据内容真实长度等于messageLength - serverId - sessionId - commandId
//		// FIXME
//		message.setTotalLength(messageLength - 1 - 4 - 4);
//		message.setCommandId(commandId);
//
//		try {
//			message.decodeBody(in);
//		} catch (Exception e) {
//			logger.error("****强制关闭会话**** : 消息体解析错误[commandId=" + commandId
//					+ ",message=" + message + "]", e);
//			session.close(true);
//			return MessageDecoderResult.NOT_OK;
//		}
//		// 用完后释放IOBuffer
//		// in.free();
//
//		// 发送给对应的MessageAction处理消息
//		out.write(message);
//		// System.out.println("decode: position3="+position+","+in.position());
//		// FIXME YANXIAOPING 2010.9.29延缓读取
////		System.err.println("消息ID: " + commandId + "停止读取");
////		session.suspendRead();
////		session.suspendWrite();
//		return MessageDecoderResult.OK;
//		// int sessionId;
//		// byte serverId = 0;
//		//
//		// serverId = in.get();
//		// sessionId = in.getInt();
//		//
//		// int totalLength = in.getShort();
//		// int commandId = in.getShort();
//		//
//		// message = MessageFactory.getMessage(commandId);
//		//
//		// if (message == null) {
//		// // in.free();
//		// // in.rewind();
//		// logger.error("****强制关闭会话**** : 没有找到对应的消息[commandId=" + commandId
//		// + "]");
//		// session.close(true);
//		// return MessageDecoderResult.NOT_OK;
//		// }
//		//
//		// message.setServerId(serverId);
//		// message.setSessionId(sessionId);
//		// message.setTotalLength(totalLength);
//		// message.setCommandId(commandId);
//		//
//		// // message.decodeHeader(in);
//		// try {
//		// message.decodeBody(in);
//		// } catch (Exception e) {
//		// logger.error("****强制关闭会话**** : 消息体解析错误[commandId=" + commandId
//		// + ",message=" + message + "]");
//		// session.close(true);
//		// return MessageDecoderResult.NOT_OK;
//		// }
//		// // 用完后释放IOBuffer
//		// // in.free();
//		//
//		// // 发送给对应的MessageAction处理消息
//		// out.write(message);
//		// //
//		// System.out.println("decode: position3="+position+","+in.position());
//		//
//		// return MessageDecoderResult.OK;
//	}
//
//	@Override
//	public void finishDecode(IoSession arg0, ProtocolDecoderOutput arg1)
//			throws Exception {
//	}
//}// Class DefaultMessageDecoder
