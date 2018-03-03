package com.fmgame.ether.main;

import java.util.LinkedList;

import org.apache.mina.core.filterchain.IoFilter;

import com.fmgame.ether.net.codec.DefaultMessageDecoder;
import com.fmgame.ether.net.codec.DefaultMessageEncoder;
import com.fmgame.ether.net.encrypt.DefaultMessageEncryptFilter;
import com.fmgame.ether.net.protocol.DefaultSessionHandler;
import com.fmgame.ether.net.protocol.SimplePipeline;
import com.fmgame.ether.support.config.GameConfig;
import com.fmgame.platform.net.TCPServerConfig;
import com.fmgame.platform.net.mina.DefaultMinaProtocolCodecFactory;
import com.fmgame.platform.net.mina.DefaultMinaTCPServer;
import com.fmgame.platform.service.IService;
import com.fmgame.platform.service.IServiceException;

/**
 * 游戏TCP服务
 */
public class TCPService extends DefaultMinaTCPServer implements IService {
	
	public static TCPService service = null;
	
	public static TCPService getInstance() {
		return service;
	}

	public TCPService() {
		super(new TCPServerConfig(GameConfig.SERVER_PORT, Runtime.getRuntime().availableProcessors() * 2, 100, 60, 4096, true));
	}

	@Override
	public void onReady() throws IServiceException {
		// 添加会话处理器
		DefaultSessionHandler handler = new DefaultSessionHandler();
		handler.setPipeline(new SimplePipeline());
		this.sessionHandler = handler;
		// 添加协议编解码器
		this.protocolCodeFactory = new DefaultMinaProtocolCodecFactory(
				new DefaultMessageDecoder(), new DefaultMessageEncoder());
		// 添加过滤器
		LinkedList<IoFilter> filters = new LinkedList<IoFilter>();
		filters.add(new DefaultMessageEncryptFilter()); // 加解密
		this.filters = filters;
	}

	@Override
	public void onStart() throws IServiceException {
		this.startup();
	}
	
	@Override
	public void onStop() throws IServiceException {
		this.shutdown();
	}

	public ThreadGroup getThreadGroup() {
		return threadFactory.getGroup();
	}
	
}
