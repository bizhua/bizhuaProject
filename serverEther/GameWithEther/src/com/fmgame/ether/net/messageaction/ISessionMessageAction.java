package com.fmgame.ether.net.messageaction;

import org.apache.mina.core.session.IoSession;
import com.fmgame.platform.net.current.AbstractTCPMessage;
import com.fmgame.platform.net.current.IMessageAction;

/**
 * 会话消息处理器标识接口
 */
public interface ISessionMessageAction<T extends AbstractTCPMessage> extends
		IMessageAction<T, IoSession> {
}
