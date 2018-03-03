package com.fmgame.ether.module.gpbirthtony.message;

import com.fmgame.ether.main.user.User;
import com.fmgame.ether.module.gpbirthtony.service.GPBirthTonyService;
import com.fmgame.ether.net.messageaction.IUserMessageAction;
import com.fmgame.platform.net.current.MessageProcessException;
import com.fmgame.platform.service.GameServiceManager;

public class BirthTonyMessageAction implements IUserMessageAction<BirthTonyMessage> {

	@Override
	public void processMessage(BirthTonyMessage message, User user)
			throws MessageProcessException {
		// TODO Auto-generated method stub
		GPBirthTonyService service = GameServiceManager.getComponentManager().getService(GPBirthTonyService.class);
		service.birthTony(message.getAsyncId(),user, message.getTonyOwnerAddress(), message.getGens(), message.getCharacter());
	}

}
