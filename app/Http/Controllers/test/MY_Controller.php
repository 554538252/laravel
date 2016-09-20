<?php

namespace App\Http\Controllers\test;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
class MY_Controller extends Controller
{
    public function index(Request $request){
        $type = $request->type;
        switch($type) {
            case 'open' :
                return $this->open($request);
                break;
            case 'close' :
                return $this->close();
                break;
            case 'config' :
                $this->config($request);
                break;
            case 'monitor' :
               $this->monitor();
                break;
            case 'trigger' :
               $this->trigger();
                break;
            default:
                break;
        }
    }
    public function open($request){
        $content = $request->getContent();
        $param_array = json_decode($content,1);
		$sign = $param_array['sign'];
		unset($param_array['sign']);
		$cal_sign = $this->_cal_sign($param_array);
		/** 验证签名 */
		if ($cal_sign == $sign) {
			$interval = time() - $param_array['timestamp'];

			/** 检查时间戳 */
			if ($interval >= -100 && $interval < 100) {

				/** OK返回正确的json格式数据 */
                return json_encode(array(
                    'errcode' => 0,
                    'errmsg' => '',
                    'token' => '',
                    'is_config' => 1,
                    'custom' => array(
                        'school' => 'SCHOOL_NAME',
                    ),
                ));
			} else {
                return json_encode(array('errcode' => 5003,'errmsg' => '请求接口失败')); //可能是重放攻击
			}
		} else {
            return json_encode(array('errcode' => 5004,'errmsg' => '签名错误')); //签名错误
		}
    }
    /**
	 * 关闭应用
	 */
    public function close($request) {
        $content = $request->getContent();
        $param_array = json_decode($content,1);

		$sign = $param_array['sign'];
		unset($param_array['sign']);
		$cal_sign = $this->_cal_sign($param_array);

		/** 验证签名 */
		if ($cal_sign == $sign) {
			$interval = time() - $param_array['timestamp'];

			/** 检查时间戳 */
			if ($interval >= 0 && $interval < 10) {
				// 此处做自己想做的操作...
                return json_encode(array('errcode' => 0,'errmsg' => 'OK'));
			} else {
                return json_encode(array('errcode' => 5003,'errmsg' => '请求接口失败')); //可能是重放攻击
			}
		} else {
            return json_encode(array('errcode' => 5004,'errmsg' => '签名错误')); //签名错误
		}
    }
    /**
	 * 应用配置页
	 */
	public function config($request) {
        $param_array = $request->input();
		$sign = $param_array['sign'];
		unset($param_array['sign']);
        unset($param_array['type']);
		$cal_sign = $this->_cal_sign($param_array);

		/** 验证签名 */
		if ($cal_sign == $sign) {
            // $media_id = trim($param_array['media_id']);
            // $token = $this->get_session_token($media_id);
            //
            // //将$token放入cookie
            // setcookie($media_id, $token);
            echo "string";

            //加载配置页面
		} else {
			return json_encode(array('errcode' => 5004,'errmsg' => '签名错误')); //签名错误
		}
	}
    /**
	 * 应用监控
	 */
    public function monitor() {
        echo $_GET['echostr'];
        exit();
    }

	/**
	 * 应用触发
	 */
    public function trigger() {
        //加载应用的页面
    }
    /**
	 * 计算签名
	 * @param array $param_array
	 */
	private static function _cal_sign($param_array) {
		$names = array_keys($param_array);
		sort($names, SORT_STRING);

		$item_array = array();
		foreach ($names as $name) {
			$item_array[] = "{$name}={$param_array[$name]}";
		}

		$api_secret = '0622C9DDC7CF019A525B69FE92ADCBA7'; //微校时提交给微校，32位字符串)
		$str = implode('&', $item_array) . '&key=' . $api_secret;
		return strtoupper(md5($str));
	}
}
