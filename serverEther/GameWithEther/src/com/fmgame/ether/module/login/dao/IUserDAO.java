package com.fmgame.ether.module.login.dao;

import com.fmgame.platform.dao.IDAO;

public interface IUserDAO extends IDAO {
     
	UserDBData getUsetById(int userId);
	
	void addUser(UserDBData udb);
}
