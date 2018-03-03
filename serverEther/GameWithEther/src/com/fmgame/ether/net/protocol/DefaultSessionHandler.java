package com.fmgame.ether.net.protocol;

import org.apache.mina.core.buffer.IoBuffer;
import org.apache.mina.core.service.IoHandlerAdapter;
import org.apache.mina.core.session.IdleStatus;
import org.apache.mina.core.session.IoSession;

import com.fmgame.ether.EtherServer;
import com.fmgame.platform.net.current.IPipeline;
import com.fmgame.platform.util.ExceptionLogger;
import com.fmgame.platform.util.GameLog;

public class DefaultSessionHandler extends IoHandlerAdapter {
	
	private static final String Exception_Key = "ExceptionCounts";

	private IPipeline pipeline;

	@Override
	public void sessionCreated(IoSession session) {
		session.setAttribute(Exception_Key, 0);
		
		if (GameLog.MSG.isDebugEnabled())
			GameLog.MSG.debug("createted session!");
	}

	@Override
	public void sessionOpened(IoSession session) {
		if (GameLog.MSG.isDebugEnabled())
			GameLog.MSG.debug("opened session!");
		// set idle time to 60 seconds
		session.getConfig().setIdleTime(IdleStatus.BOTH_IDLE, 60);
	}

	@Override
	public void sessionClosed(IoSession session) {
		if (GameLog.MSG.isDebugEnabled())
			GameLog.MSG.debug("closed session!");

		EtherServer.onSessionClosed(session);
		// GameServer.unbindSession(session);
	}

	@Override
	public void messageReceived(IoSession session, Object message) {
		if (GameLog.MSG.isDebugEnabled())
			GameLog.MSG.debug("server receive message: " + message);

		pipeline.dispatchAction(session, (IoBuffer)message);
	}

	@Override
	public void sessionIdle(IoSession session, IdleStatus status) {
		// session.close(false);
		// logger.info("sessionIdle: " + session);
	}

	@Override
	public void exceptionCaught(IoSession session, Throwable cause) {
		if(cause instanceof java.io.IOException)
			return;
		ExceptionLogger.printLogger(cause.getMessage(), cause);
//		InetSocketAddress remoteAddress = (InetSocketAddress)session.getRemoteAddress();
//		String error="session";
//		if (remoteAddress != null) {
//			error+="(" + remoteAddress.getHostName() + ":" + remoteAddress.getPort() + ")";
//		}
//		logger.error(error+" : "+cause);

//		int exceptionCounts = (Integer) session.getAttribute(Exception_Key);
//		if (exceptionCounts >= MaxExceptionCounts) {
//			InetSocketAddress remoteAddress = (InetSocketAddress)session.getRemoteAddress();
//			
//			if (remoteAddress != null) {
//				String hostName = remoteAddress.getHostName();
//				int port = remoteAddress.getPort();
//				
//				logger.error("the session(" + remoteAddress.getHostName() + ":" + remoteAddress.getPort() + " )" + exceptionCounts + "exceptions more than "
//						+ MaxExceptionCounts + " will close the session " + session,
//						cause);
//			}
//			else {
//				logger.error(session + "the session" + exceptionCounts + "exceptions more than "
//						+ MaxExceptionCounts + " will close the session",
//						cause);
//			}
//			session.close();
//			
//		} else {
//			session.setAttribute(Exception_Key, ++exceptionCounts);
//		}
	}

	public IPipeline getPipeline() {
		return pipeline;
	}

	public void setPipeline(IPipeline pipeline) {
		this.pipeline = pipeline;
	}
}// Class DefaultSessionHandler
