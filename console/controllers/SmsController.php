<?php
namespace console\controllers;

use Yii;
use yii\console\Controller;
use common\models\Comment;

class SmsController extends Controller
{
    public function actionSend()
    
    //����ϵͳ�ϵ�ִ�����
    ///Users/michaelweixi/WWWRoot/blogdemo2/yii sms/send >> /Users/michaelweixi/WWWRoot/blogdemo2/sms.log

    {
   		$newCommentCount=Comment::find()->where(['remind'=>0])->count();//������û��δ���ѵ�������
   		
   		if($newCommentCount>0|| 1)
   		{
   			$content='��'.$newCommentCount.'�������۴���ˡ�';
   			
   			$result = $this->vendorSmsService($content);
   			
   			if($result['status']=='success')
   			{
   				Comment::updateAll(['remind'=>1]); //�����ѱ�־ȫ����Ϊ������
   				echo '['.date("Y-m-d H:i:s",$result['dt']).'] '.$content.'['.$result['length'].']'."\r\n";//��¼��־
   				
   			}else{
   			    echo 'no ���ݸ���';
            }
   			return 0;
   		}
    }

	protected function vendorSmsService($content)
	{
		//ʵ�ֵ��������Ź�Ӧ���ṩ�Ķ��ŷ��ͽӿڡ�
	
		//     	$username = 'companyname';		//�û��˺�
		//     	$password = 'pwdforsendsms';	//����
		//     	$apikey = '577d265efafd2d9a0a8c2ed2a3155ded7e01';	//����
		//     	$mobile	 = $adminuser->mobile;	//���ֻ���
	
		//     	$url = 'http://sms.vendor.com/api/send/?';
		//     	$data = array
		//     	(
		//     			'username'=>$username,				//�û��˺�
		//     			'password'=>$password,				//����
		//     			'mobile'=>$mobile,					//����
		//     			'content'=>$content,				//����
		//     			'apikey'=>$apikey,				    //apikey
		//     	);
		//     	$result= $this->curlSend($url,$data);			//POST��ʽ�ύ
		//     	return $result;    //���ط���״̬������ʱ�䣬�ֽ���������
		//     	}
		 
		$result=array("status"=>"success99999","dt"=>time(),"length"=>43);  //ģ������
		return $result;
		 
	}

}
