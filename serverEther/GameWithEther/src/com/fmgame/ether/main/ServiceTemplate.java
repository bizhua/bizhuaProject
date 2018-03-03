package com.fmgame.ether.main;

import com.fmgame.platform.service.IService;
import com.fmgame.platform.service.IServiceException;
import com.fmgame.platform.util.ExceptionLogger;

/**
 * 服务模块,提供统一服务管理
 */
public class ServiceTemplate implements IService {
	
	private static final ServiceTemplate instance = new ServiceTemplate();
	
	public static ServiceTemplate getInstance() {
		return instance;
	}

	@Override
	public void onReady() throws IServiceException {
		// 配置
		ConfigService.getInstance().onReady();
		// 任务调度
		TaskManagerService.getInstance().onReady();
		// 模块
		LoadModuleService.getInstance().onReady();
		// 连接服务
		TCPService.service = new TCPService();
		TCPService.getInstance().onReady();
		// 帧频调度
		FrameTaskService.getInstance().onReady();
		// 停服服务
		StopServerService.getInstance().onReady();
	}

	@Override
	public void onStart() throws IServiceException {
		// 启动配置
		ConfigService.getInstance().onStart();
		// 任务调度
		TaskManagerService.getInstance().onStart();
		// 启动模块
		LoadModuleService.getInstance().onStart();
		// 连接服务
		TCPService.getInstance().onStart();
		// 启动帧频调度
		FrameTaskService.getInstance().onStart();
	}

	@Override
	public void onStop() {
		try {
			StopServerService.getInstance().onStop();
			Thread.sleep(10000);
			FrameTaskService.getInstance().onStop();
			TCPService.getInstance().onStop();
			TaskManagerService.getInstance().onStop();
			LoadModuleService.getInstance().onStop();
		} catch (Exception e) {
			ExceptionLogger.printLogger("关闭各个服务失败：", e);
		}
	}

}
