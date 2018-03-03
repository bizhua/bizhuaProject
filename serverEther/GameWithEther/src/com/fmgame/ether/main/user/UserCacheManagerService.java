package com.fmgame.ether.main.user;
import java.util.ArrayList;
import java.util.List;
import java.util.Map;
import java.util.concurrent.ConcurrentHashMap;

import org.apache.mina.core.session.IdleStatus;
import org.apache.mina.core.session.IoSession;
import org.apache.mina.transport.socket.SocketSessionConfig;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;

import com.fmgame.ether.util.SessionKey;
import com.fmgame.platform.util.ExceptionLogger;

/**
 * 用户缓存管理服务
 */
public class UserCacheManagerService {
	
	private static final Logger logger = LoggerFactory.getLogger(UserCacheManagerService.class);

	/**
	 * 会话与用户对应关系
	 */
	private static Map<IoSession, User> sessionToUserMap = new ConcurrentHashMap<IoSession, User>();
    /**
     * 用户Id和回话对应
     */
	private static Map<Integer, IoSession> userSessionMap = new ConcurrentHashMap<Integer, IoSession>();
	
	/**
	 * 游戏中用户MAP
	 */
	private static Map<Integer, User> userMap = new ConcurrentHashMap<Integer, User>();
	
	public static List<User> getAllUser()
	{
		return new ArrayList<User>(userMap.values());
	}
	
	/**
	 * 通过用户会话获取用户对象
	 * @param session
	 * @param message
	 * @return
	 */
	public static final User getUserBySession(IoSession session) {
		return sessionToUserMap.get(session);
	}

	/**
	 * 通过用户会话移除用户对象
	 * @param session
	 * @return
	 */
	private static final User removeUserBySession(IoSession session) {
		return sessionToUserMap.remove(session);
	}

	private static final void putSessionUser(IoSession session, User user) {
		sessionToUserMap.put(session, user);
	}
	
	
	

	private static void putUserMap(int id, User user) {
		// TODO Auto-generated method stub
		userMap.put(id, user);
	}


	public static User getUserById(int userId) {
		// TODO Auto-generated method stub
		return userMap.get(userId);
	}
	private static void removeUserById(int userId)
	{
		userMap.remove(userId);
	}
	
	/**
	 * 其他玩家使用此账号登录.如果同一账号登录过程未完成.不允许进行下一次登录.
	 * @param userId
	 * @param session
	 */
	public static void putUserSessionMap(int userId, IoSession session) {
		IoSession oldSession = userSessionMap.get(userId);
		if (oldSession != null && oldSession.getAttribute(SessionKey.CUR_IP) != session.getAttribute(SessionKey.CUR_IP))
			return;
	
		userSessionMap.put(userId, session);
	}
	
	// ------------------------------------------------------------------------------------- 用户加入/离开
	
	public static User userJoin(IoSession session, User loginUser) {
        User user = null;
        int userId = loginUser.get_id();
        User user1 = UserCacheManagerService.getUserById(userId);
        if (user1 != null) {
            IoSession oldSession = user1.getSession();// 用户已存在
            if (oldSession != null) {
                if (oldSession != session) {
                    user = loginUser;// 如果用户的会话连接存在差异,则关闭旧的会话
                    if (user == null) {
                        logger.error("用户会话存在差异,但是从认证服务器获得的用户为空!" + userId);
                        return null;
                    }
        			// 立即从游戏服务中移除用户
                    UserCacheManagerService.userLeave(user1, false);
                    user1 = null;
                } else {
                    user = user1;
                }
            } else {
                if (logger.isDebugEnabled()) {// 从用户MAP中移除用户
                    logger.debug("用户数据库登录时存在会话连接为空的垃圾用户对象:user=" + user1);
                }
                UserCacheManagerService.userLeave(user1, false);
                user1 = null;
            }
        }
        
        if (user == null) {
            user = loginUser;
            if (user == null) {
                logger.error("最后判断用户为空.UserID:" + userId);
                return null;
            }
        }
       
        
        UserCacheManagerService.putUserMap(user.get_id(), user);
        UserCacheManagerService.putSessionUser(session, user);// 新绑定

        // 设置会话连接的空闲时间为无限
        session.getConfig().setIdleTime(IdleStatus.BOTH_IDLE, 0);
    	try {
			((SocketSessionConfig) (session.getConfig())).setSoLinger(0);
		} catch (Exception e) {
			ExceptionLogger.printLogger("socket错误", e);
		}
    	
        // 用户加入游戏
        user.onJoinGame(session);
        
        return user;
	}


	public static void userLeave(User user, boolean quitType) {
		// TODO Auto-generated method stub
		if (logger.isDebugEnabled()) {
			logger.debug("GameServer.userLeave():  " + user.get_id());
		}
		
		// 从用户MAP中移除用户
		UserCacheManagerService.removeUserById(user.get_id());
		// 获取缓存信息
		IoSession session = user.getSession();
		if (session != null) {
			// 从用户会话MAP中移除用户对象
			UserCacheManagerService.removeUserBySession(session);
		}
		
		// 用户数据保存处理,用户对象释放
		user.onLeaveGame();	
	}
	
}
