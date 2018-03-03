package com.fmgame.ether.main;

import com.fmgame.platform.service.GameServiceManager;
import com.fmgame.platform.service.IService;
import com.fmgame.platform.service.IServiceException;

/**
 * 任务调度管理服务
 */
public class TaskManagerService implements IService {
	
	private static final TaskManagerService instance = new TaskManagerService();
	
	public static TaskManagerService getInstance() {
		return instance;
	}

	@Override
	public void onReady() throws IServiceException {
	}

	@Override
	public void onStart() throws IServiceException {
		GameServiceManager.getTaskManager().startService();
	}

	@Override
	public void onStop() throws IServiceException {
		GameServiceManager.getTaskManager().stopService();
	}

}
