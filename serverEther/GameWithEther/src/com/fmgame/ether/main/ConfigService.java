package com.fmgame.ether.main;

import org.slf4j.Logger;
import org.slf4j.LoggerFactory;

import com.fmgame.ether.support.config.GameConfig;
import com.fmgame.platform.service.IService;
import com.fmgame.platform.service.IServiceException;

/**
 * 配置信息服务
 */
public class ConfigService implements IService {
	
	private static final Logger logger = LoggerFactory.getLogger(ConfigService.class);

	private static final ConfigService service = new ConfigService();

	private ConfigService() {
	}

	public static ConfigService getInstance() {
		return service;
	}

	@Override
	public void onReady() throws IServiceException {
		if (logger.isInfoEnabled())
			logger.info("配置服务信息准备");
		
		// 配置加载
		GameConfig.initConfig(); 

	}

	@Override
	public void onStart()  throws IServiceException{
	}

	@Override
	public void onStop()  throws IServiceException{
	}
}
