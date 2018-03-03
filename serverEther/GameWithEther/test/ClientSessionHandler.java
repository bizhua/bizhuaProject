
import org.apache.mina.core.buffer.IoBuffer;
import org.apache.mina.core.service.IoHandlerAdapter;  
import org.apache.mina.core.session.IoSession;  
  
/** 
 * 处理客户端IO事件 
 * 
 */  
public class ClientSessionHandler extends IoHandlerAdapter {  
  
    public ClientSessionHandler(){  
    }  
  
    //当与服务端链接成功时Session会被创建，同时会触发该方法  
    public void sessionOpened(IoSession session) throws Exception {  
        //发送消息给服务端 
    	TestLoginMessage msg = new TestLoginMessage();
    	msg.setUserId(100);
    	msg.setPass("100_power1");
    	IoBuffer out = msg.encodeIoBuffer();
        session.write(out) ;  
        System.out.println("发送消息给服务端成功");  
    }  
  
    //当接收到服务端发送来的消息时，会触发该方法  
    @Override  
    public void messageReceived(IoSession session, Object message)  
    throws Exception {  
    	 System.out.println("接受成功");  
    }  
  
    @Override  
    public void exceptionCaught(IoSession session, Throwable cause)  
    throws Exception {  
        session.closeOnFlush();  //当发送异常，就关闭session  
    }  
  
}  