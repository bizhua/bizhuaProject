package com.fmgame.ether.main;

import org.slf4j.Logger;
import org.slf4j.LoggerFactory;

import com.fmgame.etherwork.process.AsyncProcessUintManager;
import com.fmgame.platform.scheduling.task.PeriodicTaskHandle;
import com.fmgame.platform.scheduling.task.Task;
import com.fmgame.platform.scheduling.task.TaskConstants;
import com.fmgame.platform.service.GameServiceManager;
import com.fmgame.platform.service.IService;
import com.fmgame.platform.service.IServiceException;
import com.fmgame.platform.util.ExceptionLogger;
import com.fmgame.platform.util.time.DateTimeUtil;

/**
 * 帧频调度服务
 */
public class FrameTaskService implements IService {

	private static final Logger logger = LoggerFactory.getLogger(ConfigService.class);

	private static final FrameTaskService service = new FrameTaskService();
	
	private PeriodicTaskHandle handle;
//	private int noUserRoleUpdateTick = 0;
//	private static final FrameUpateManager<IFrameUpdatable> frameUpdateManager = new FrameUpateManager<IFrameUpdatable>();

	private FrameTaskService() {
	}

	public static FrameTaskService getInstance() {
		return service;
	}
	
	@Override
	public void onReady() throws IServiceException {
	//	frameUpdateManager.add(ChatDataBufferManager.getInstance());
	}

	@Override
	public void onStart() throws IServiceException {
		handle = GameServiceManager.getTaskManager().schedulePeriodicTask(TaskConstants.TASK_SERVICE_COMMON, new Task() {
			@Override
			public void run() {
				updateFrame();
			}
		}, 5000L, DateTimeUtil.SECOND);

		if (logger.isInfoEnabled()) {
			if (handle == null) {
				logger
						.error("FrameTaskService	startup	startup	failed.the	service	will	run	without	FrameTaskService");
			} else {
				logger.info("游戏帧事件服务启动！");
			}
		}
	}
	
	@Override
	public void onStop() throws IServiceException {
		if (handle != null) {
			handle.cancel();
			logger.error("游戏帧事件服务关闭！");
		}
	}

	private void updateFrame() {
		try {
			//帧更新
			AsyncProcessUintManager.getInstance().updata();
			
		} catch (Throwable e) {
			ExceptionLogger.printLogger("FrameTaskService Exception.", e);
		}
	}
	
}
