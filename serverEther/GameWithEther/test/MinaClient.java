import java.net.InetSocketAddress;  

import org.apache.mina.core.future.ConnectFuture;  
import org.apache.mina.core.session.IoSession;  
import org.apache.mina.filter.codec.ProtocolCodecFilter;  
import org.apache.mina.transport.socket.nio.NioSocketConnector;  

import com.fmgame.ether.net.codec.DefaultMessageDecoder;
import com.fmgame.ether.net.codec.DefaultMessageEncoder;
import com.fmgame.platform.net.mina.DefaultMinaProtocolCodecFactory;
  
public class MinaClient {  
      
    public static void main(String[] args) {  
          
        //首先创建一个NioSocketConnector 用于链接服务端  
        NioSocketConnector connector = new NioSocketConnector() ;  
        //加入编码/解码Filter  new ProtocolCodecFilter(protocolCodeFactory)
        connector.getFilterChain().addLast("codec", new ProtocolCodecFilter(new DefaultMinaProtocolCodecFactory(
				new DefaultMessageDecoder(), new DefaultMessageEncoder()))) ;  
          
        //设置IO处理器  
        connector.setHandler(new ClientSessionHandler());  
          
        //链接服务端  
        ConnectFuture connectFuture =  
            connector.connect(new InetSocketAddress("localhost", 2888)) ;  
      
          
        //阻塞等待，知道链接服务器成功，或被中断  
        connectFuture.awaitUninterruptibly() ;  
          
        IoSession session = connectFuture.getSession() ;  
          
        //阻塞，知道session关闭  
        session.getCloseFuture().awaitUninterruptibly() ;  
          
        //关闭链接  
        connector.dispose() ;  
    }  
  
}  