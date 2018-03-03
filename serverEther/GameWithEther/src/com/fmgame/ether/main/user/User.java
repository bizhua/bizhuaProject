package com.fmgame.ether.main.user;

import org.apache.mina.core.buffer.IoBuffer;
import org.apache.mina.core.session.IoSession;

import com.fmgame.platform.net.current.AbstractTCPMessage;
import com.fmgame.platform.util.ExceptionLogger;
import com.fmgame.platform.util.time.DateTimeUtil;

public class User {
	private int _id;

	public boolean activeFlag;
	
	public int get_id() {
		return _id;
	}

	public void set_id(int _id) {
		this._id = _id;
	}
	/**
	 * 心跳消息发送时间间隔，以秒为单位
	 */
	public static long ACK_TIME_INTERVAL = DateTimeUtil.SECOND * 15;
	
	/**
	 * 心跳消息
	 */
//	public static ACKMessage ackMsg = new ACKMessage();
	/**
	 * 会话
	 */
	private transient IoSession session;

	/**
	 * 登录时间戳.
	 */
	private long loginTimeMillis;
	
	/**
	 * 心跳时间戳
	 */
	private long heartBeatTime = System.currentTimeMillis() + ACK_TIME_INTERVAL;
	
	
	public User(int userId) {
		this._id = userId;
		this.loginTimeMillis = System.currentTimeMillis();
	}
	

	public IoSession getSession() {
		return session;
	}

	/**
	 * 本次登录时间
	 * @return
	 */
	public long getLoginTimeMillis() {
		return loginTimeMillis;
	}
	
	/**
	 * 用户加入游戏
	 */
	public void onJoinGame(IoSession session) {
		this.session = session;
	}
	
	/**
	 * 用户离开游戏
	 */
	public void onLeaveGame() {
		try {
			session = null;
		} catch (Exception e) {
			ExceptionLogger.printLogger("用户退出游戏错误:id=" + _id, e);
		}
	}
	
	// ------------------------------------------------------------------------------------- 更新
	
	public void update(long currentTime) {
		if (session == null) 
			return;
		
		try {
			// 15秒处理
			if (currentTime >= heartBeatTime) {
				heartBeatTime = currentTime + ACK_TIME_INTERVAL;

				// 发送心跳消息
//				sendMsg(ackMsg);
			}
		} catch (Exception e) {
			ExceptionLogger.printLogger("userID="+get_id() + "更新出错:" + e.getMessage(), e);
		}
	}

	// -------------------------------------------------------------------------------------- 发送消息
	
	/**
	 * 用户发送消息主函数
	 * @param message
	 */
	public void sendMsg(AbstractTCPMessage message) {
		if (message == null) {
			ExceptionLogger.printLogger("用户发送消息错误-消息为空:id=" + _id, new RuntimeException());
			return;
		}
		
		if (session == null) {
			 // logger.error("Server Send Msg To User-:id="+id+",spId="+spId+",command="+message.getCommandId()+",message="+message);
			return;
		} else if (!session.isConnected()) {
			// logger.error("用户发送消息错误-Session已关闭:id="+id+",spId="+spId+",command="+message.getCommandId()+",message="+message);
			return;
		}

		try {
			IoBuffer out = message.encodeIoBuffer();

			session.write(out);
			
		} catch (Exception e) {
			ExceptionLogger.printLogger("发送消息时出错:" + message, e);
		}
	}
	
	@Override
	public String toString() {
		return "[User: id=" + _id + ", session=" + session + ", loginTimeMillis="
				+ loginTimeMillis  + "]";
	}
}
