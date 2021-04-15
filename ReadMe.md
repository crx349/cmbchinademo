### 招商银行一网通H5支付测试例子

本测试代码用于招行一网通测试环境调试（测试报告使用）

注意事项：
    'branch_no' => 'xxxx',  // 商户分行号，4位数字
    'mch_id'    => 'xxxx', // 商户号，6位数字
    'mer_key'   => '1234567890123456', // 秘钥16位，包含大小写字母 数字
    
    这3个参数需要联系一网通获取，服务器ip需要过一网通白名单ip，否则测试程序无效
    
    查询订单中的：$tradeNo 请使用第二部分提交成功的订单号，订单号在运行目录下order_id开头的txt文件里面。
	
题外话：
招行一网通主要测试项目有4个，正常这4个测试通过（H5支付商户测试验收报告），就可以申请正式账号了。
分别是：
CMB_test003~CMB_test004 和 CMB_test008~CMB_test009

3和4分别通过步骤2 提交2个订单，分别用储蓄卡和信用卡进行提交（一网通提供测试卡号）。
8输入3或4提交通过的订单（订单号在运行目录下order_id开头的txt文件里面），查询返回并截图。
9输入任意不存在订单号，截图就可以了

如果对您有帮助 麻烦给给star 谢谢

代码不足部分 欢迎点击QQ号指导交流 ![842062626](http://www.xmspace.net/qq.gif "QQ联系")  
    
    