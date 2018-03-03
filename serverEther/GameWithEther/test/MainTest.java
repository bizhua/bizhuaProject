import org.web3j.abi.datatypes.generated.Uint256;

import com.fmgame.etherwork.EtherUtilTools;


public class MainTest {

	public static void main(String[] args) {
		// TODO Auto-generated method stub
      int[] ss = {1,1,1,1,1,1,1,1,1,1,1,1,1,1};
      
      int[] ss1 = {1,3,1,4,1,5,1,1,178873,1,255155,1,222894,1};
      
      Uint256 a = EtherUtilTools.getGensUint256Fast(ss1);
      System.out.println(a.getValue().toString());
      int[] zz = EtherUtilTools.getGensintFast(a);
      for(int i : zz)
      {
    	  System.out.println(i);
      }
      
      

      Uint256 a1 = EtherUtilTools.getGensUint256Fast(ss1);
      System.out.println(a.getValue().toString());
      int[] zz1 = EtherUtilTools.getGensintFast(a1);
      for(int i1 : zz1)
      {
    	  System.out.println(i1);
      }
	}

}
