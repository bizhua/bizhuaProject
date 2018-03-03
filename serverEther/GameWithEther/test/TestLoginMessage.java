import org.apache.mina.core.buffer.IoBuffer;

import com.fmgame.ether.net.MsgIdConstant;
import com.fmgame.platform.net.current.AbstractTCPMessage;
import com.fmgame.platform.net.current.ConfMsgAction;

@ConfMsgAction(msgId = MsgIdConstant.LOGIN_MESSAGE)
public class TestLoginMessage extends AbstractTCPMessage {

	public int userId;
	public void setUserId(int userId) {
		this.userId = userId;
	}

	public String pass;
	public void setPass(String pass) {
		this.pass = pass;
	}

	@Override
	public void release() {
		// TODO Auto-generated method stub

	}

	@Override
	public void decodeBody(IoBuffer arg0) throws Exception {
		// TODO Auto-generated method stub
		
	}

	@Override
	public void encodeBody(IoBuffer out) {
		// TODO Auto-generated method stub
       out.putInt(userId);
       putString(out, pass);
	}

}
