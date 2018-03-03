package com.fmgame.ether.net.messageaction;


import com.fmgame.ether.main.user.User;
import com.fmgame.platform.net.current.AbstractTCPMessage;
import com.fmgame.platform.net.current.IMessageAction;
import com.fmgame.platform.net.current.MessageProcessException;

/**
 * 用户消息处理器标识接口
 */
public interface IUserMessageAction<T extends AbstractTCPMessage> extends IMessageAction<T, User> {
	void processMessage(T message, User user) throws MessageProcessException;
}
