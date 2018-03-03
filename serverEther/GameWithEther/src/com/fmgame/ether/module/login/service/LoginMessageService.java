package com.fmgame.ether.module.login.service;

import com.fmgame.ether.module.login.dao.IUserDAO;
import com.fmgame.ether.module.login.dao.UserDBData;

import com.fmgame.platform.component.ComponentServiceAdapter;
import com.fmgame.platform.service.GameServiceManager;

public class LoginMessageService extends ComponentServiceAdapter {

    public boolean validate(int userId, String pass)
    {
    	IUserDAO dao = GameServiceManager.getDAOManager().getDAO(IUserDAO.class);
    	UserDBData userData = dao.getUsetById(userId);
    	return userData.getPassword().equals(pass);
    }
}
