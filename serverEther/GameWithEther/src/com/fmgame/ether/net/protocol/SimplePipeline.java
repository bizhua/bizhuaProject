package com.fmgame.ether.net.protocol;
import org.apache.mina.core.session.IoSession;
import com.fmgame.ether.net.parameterresolver.UserMessageActionParameterResolver;
import com.fmgame.platform.net.current.AbstractPipeline;
import com.fmgame.platform.net.current.AbstractTCPMessage;
import com.fmgame.platform.net.current.ConfMsgAction;
import com.fmgame.platform.net.current.IMessageAction;
import com.fmgame.platform.net.current.IMessageActionHelper;
import com.fmgame.platform.net.current.IMessageActionParameterResolver;
import com.fmgame.platform.util.ExceptionLogger;
import com.fmgame.platform.util.GameLog;

public class SimplePipeline extends AbstractPipeline {
	
    @Override
	@SuppressWarnings({ "unchecked", "rawtypes" })
    public void dispatchAction(IoSession session, AbstractTCPMessage message) {
        try {
            // 标识下样本采集
            if (session == null) {
            	GameLog.MSG.error("session can not null.");
                return;
            }
            
            if (message == null) {
            	GameLog.MSG.error("message can not null.");
                return;
            }

			int commandId = message.getCommandId();
			ConfMsgAction confMsgAction = message.getConfMsgAction();
			if (GameLog.MSG.isInfoEnabled()) {
				if (confMsgAction.printLog())
					GameLog.MSG.info("[session]" + session.getRemoteAddress() + "[ClientMsg]" + message);
			}
            
            if (!session.isConnected()) {
            	GameLog.MSG.error("session is not connected." + ",commandId=" + message.getClass().getSimpleName() + "(" + commandId + "),message=" + message + "]");
                return;
            }

//            long timestampBeforeUpdate = System.currentTimeMillis();

            IMessageActionHelper helper = messageFactory.getMessageActionHelper(commandId);
            IMessageAction action = helper != null ? helper.getAction() : null;
            IMessageActionParameterResolver resolver = helper != null ? helper.getResolver() : UserMessageActionParameterResolver.getInstance();
            Object secondParameter = resolver.resolveParameter(session, message);
            
            if (secondParameter != null) {
                try {
                		action.processMessage(message, secondParameter);
                	} catch (Exception e) {
	                    String str = e.getMessage();
	                    if (str == null)
	                        str = "";
	                    StringBuilder sb = new StringBuilder(str);
	                    sb.append("\n参数：").append(secondParameter);
	                    sb.append("\n消息：").append(message);
	                    ExceptionLogger.printLogger(sb.toString(), e);
                	}
            } else {
            	GameLog.MSG.error("****强制关闭会话**** : 消息处理时参数对象为空[session=" + session + ",commandId=" + message.getClass().getSimpleName() + "(" + commandId + "),message=" + message + "]");
                session.closeNow();
                return;
            }
        } catch (Exception e) {
        	ExceptionLogger.printLogger("dispatchAction(IoSession, AbstractMessage) -消息处理错误- message=" + message, e); //$NON-NLS-1$
        }
    }

}