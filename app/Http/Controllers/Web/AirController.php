<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Config;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AirController extends Controller
{
	private $indexUrl = 'http://www.gzairports.com:11111/order/index.html';
	private $codeUrl = 'http://www.gzairports.com:11111/order/creatImgCode';
	private $airConfig = 'http://www.gzairports.com:11111/searchAppointmentSettings.action';
    private $addUrl = 'http://www.gzairports.com:11111/departPassengerAppointment.action';
    private $arriveUrl = 'http://www.gzairports.com:11111/apartPassengerAppointment.action';
	private $ocrUrl = 'http://apigateway.jianjiaoshuju.com/api/v_1/yzm.html';
	private $cookie = '';
	private $lockFilePath = '/www/wwwroot/hangbang/storage/app/public/air_lock';

	public function getList(Request $request)
    {
        $list = Plan::paginate(10);

        return view('list', compact('list'));
    }

	public function add($id = 0)
	{
		$info = Plan::find($id);

		return view('air', compact('info'));
	}

	private function getCodeStrAndCookie()
	{
		$time = $this->msectime();
		$url = $this->codeUrl . '?d=' . $time;

		$res = $this->get($url);
		$cookie = $res[0]['Set-Cookie'];
		$res = $res[1];

		$imgBase64 = 'data:image/jpeg;base64,' . base64_encode($res);

		info('图片base:' . $imgBase64);

		$res = $this->getCode($imgBase64);

		info('ocr结果:' . print_r($res, true));
		return [ $res['v_code'], $cookie];
	}

	public function doAdd(Request $request)
    {
        try {
            DB::beginTransaction();

            $data = $request->all();
            unset($data['_token']);
            $keys = ["type", "userName", "idCard", "airways", "flightNo", "startStation", "terminalStation", "flightDate", "telNumber", "appointCount"];
//            $cookieRes = $this->getCodeStrAndCookie();
            foreach ($data['userName'] as $key => $value) {
//		        $code  = $cookieRes[0];
//		        $cookie = $cookieRes[1];
            	if(!empty($data['id'][$key])){
            		$info = Plan::find($data['id'][$key]);
            	}else{
	                $info = new Plan();
	            }
                $info->userName = $value;
                foreach ($keys as $v) {
                    $info->{$v} = $data[$v][$key];
                }
//                $info->cookie = $cookie;
//                $info->code = $code;
                $res = $info->save();
                if (!$res) {
                    throw new \Exception('创建失败');
                }
            }

            DB::commit();
            return ['code' => 1, 'msg' => '创建成功'];
        }catch (\Exception $exception){
            DB::rollBack();
            return ['code' => 0, 'msg' => $exception->getMessage()];
        }
    }

	public function index()
	{
	    $date = date("Y-m-d");

        $config = Config::first();
        $startTime = strtotime($date . ' ' .$config->start_time);
		info('配置文件:'.print_r($config, true));
		info('当前时间:'. date('Y-m-d H:i:s', time()));
        if($startTime > time()){
    		$out = system("rm -rf " . $this->lockFilePath, $res);
	    	info('删除文件锁111:' . print_r($res, true));
            info('未到执行时间');
            exit;
        }

		info('达到执行时间');
        if($config->is_start == 0){
        	info('启动任务');
            //未启动,启动任务
            $config->is_start = 1;
            $config->save();
            $this->shell($date);
        }else{
        	info('更新其他');
            //判断是否有其他数据需要更新
            if(Plan::where('status', 0)->where('flightDate', $date)->where('counts', '<', env('MAX_COUNT', 3))->count() > 0){
                $this->shell($date);
            }
        }
        
    	$out = system("rm -rf " . $this->lockFilePath, $res);
    	
    	info('删除文件锁222:' . print_r($res, true));
	}

	public function delete($id)
    {
        if(!Plan::find($id)->delete())
            return ['code' => 0, 'msg' => '删除失败'];

        return ['code' => 1, 'msg' => '删除成功'];
    }

	public function shell($date)
    {
        $list = Plan::where('status', 0)->where('flightDate', $date)->where('counts', '<', env('MAX_COUNT', 3))->get();

        if(!empty($list)){
            info('请求数据有:' . print_r($list, true));
            foreach ($list as $value){
                $codeStr = $this->getCodeStr();
				if(!$codeStr){
					info($value->userName . 'OCR失败');
					continue;
				}
                $userName = $value->userName;
                $idCard = $value->idCard;
                $airways = $value->airways;
                $flightNo = $value->flightNo;
                $startStation = $value->startStation;
                $terminalStation = $value->terminalStation;
                $flightDate = $value->flightDate;
                $telNumber = $value->telNumber;
                $appointCount = $value->appointCount;

                $data = [
                    'userName' => $userName,
                    'idCard' => $idCard,
                    'airways' => $airways,
                    'flightNo' => $flightNo,
                    'startStation' => $startStation,
                    'terminalStation' => $terminalStation,
                    'flightDate' => $flightDate,
                    'telNumber' => $telNumber,
                    'appointCount' => $appointCount,
                    'validateCode' => $codeStr,
                    // 'validateCode' => $value->code,
                ];

                $bodys = '';
                foreach ($data as $key => $v) {
                    $bodys .= '&' . $key . '=' . $v;
                }
                $bodys = ltrim($bodys, '&');
                info('请求参数:'. print_r($data, true));
                info('请求body:'. $bodys);
                $requestUrl = ($value->type == 1) ? $this->addUrl : $this->arriveUrl;
                $res = $this->post($requestUrl, [], $bodys, ["Cookie:" . $this->cookie]);

                info('请求结果:'. print_r($res, true));
                $value->increment('counts');
//                Plan::find($value->id)->increment('counts');
                switch ($res['result']['success']){
                    case 1:
                        //预约成功,不继续请求
                        $value->status = 1;
                        $value->result = json_encode($res);
                        $value->save();
                        break;
                    default :
                        switch ($res['result']['msg']){
                            case '本次预约与上次购买时间过近，请留一些机会给其他旅客，谢谢':
                                //已经预约过，不继续请求
                                    $value->status = 1;
                                    $value->result = json_encode($res);
                                    $value->save();
                                break;
                            case '非常抱歉，该航班本次额度已预约满，欢迎您下次光临':
                                    $value->status = 1;
                                    $value->result = json_encode($res);
                                    $value->save();
                                break;
                            case '现在不在可预约时间段内，请在指定时间段预约':
                                    $value->status = 1;
                                    $value->result = json_encode($res);
                                    $value->save();
                            	break;
                            default:
                                    $config = Config::first();
                                    $config->is_start = 0;
                                    $config->save();
                                break;
                        }
                        break;
                }
            }
    		$out = system("rm -rf " . $this->lockFilePath, $res);
	    	info('删除文件锁333:' . print_r($res, true));
        }else{
    		$out = system("rm -rf " . $this->lockFilePath, $res);
	    	info('删除文件锁333:' . print_r($res, true));
            info('查询出无数据');
        }
    }

	private function getCodeStr()
	{
		// return '231d';
		$time = $this->msectime();
		$url = $this->codeUrl . '?d=' . $time;

		$res = $this->get($url);
		$this->cookie = $res[0]['Set-Cookie'];
		$res = $res[1];

		$imgBase64 = 'data:image/jpeg;base64,' . base64_encode($res);

		info('图片base:' . $imgBase64);

		$res = $this->getCode($imgBase64);

		info('ocr结果:' . print_r($res, true));
		if($res['errCode']){
			return false;
		}
		return $res['v_code'];
	}

	private function msectime() 
	{
	    list($msec, $sec) = explode(' ', microtime());
	    $msectime =  (float)sprintf('%.0f', (floatval($msec) + floatval($sec)) * 1000);
	    return $msectime;
	}
	
	private function getCode($content)
	{
	 	$host = "http://apigateway.jianjiaoshuju.com";
	    $path = "/api/v_1/yzm.html";
	    $method = "POST";
	    $appcode = "D5EB8B28B098066DD2AF81DA82E9B9C3";
	    $appKey = "AKIDa4ca85b25e4e7b9d2f749c4973ca1bb0";
	    $appSecret = "4d2821f1bad3e8e5055d2588088fe01c";
	    $headers = array();
	    array_push($headers, "appcode:" . $appcode);
	    array_push($headers, "appKey:" . $appKey);
	    array_push($headers, "appSecret:" . $appSecret);
	    array_push($headers, "Content-Type".":"."application/x-www-form-urlencoded; charset=UTF-8");
	    $bodys = "v_pic=".$content."&v_type=ne4";
	    $url = $host . $path;

	    $res = $this->post($url, [], $bodys, $headers);

	    return $res;
	}

	private function post($url, $data, $bodys = [], $headers = [])
	{
	    $method = "POST";
	    array_push($headers, "Content-Type".":"."application/x-www-form-urlencoded; charset=UTF-8");
	    $querys = "";

	    array_push($headers, "Cookie:" . rtrim($this->cookie));
	    $curl = curl_init();
	    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
	    curl_setopt($curl, CURLOPT_URL, $url);
	    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
	    curl_setopt($curl, CURLOPT_FAILONERROR, false);
	    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($curl, CURLOPT_HEADER, false);
	    if (1 == strpos("$".$url, "https://"))
	    {
	        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
	    }
	    curl_setopt($curl, CURLOPT_POSTFIELDS, $bodys);
        // 在尝试连接时等待的秒数
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT , 30);
        // 最大执行时间
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);

	    return json_decode(curl_exec($curl), true);
	}


	private function get($url)
	{
	    $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);         
        curl_setopt($curl, CURLOPT_HEADER, 1);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $data = curl_exec($curl);

        // 获得响应结果里的：头大小
	    $headerSize = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
	    $headerTotal = strlen($data);
	    $bodySize = $headerTotal - $headerSize;

	    // 根据头大小去获取头信息内容
	    $header = substr($data, 0, $headerSize);
	    $comma_separated = explode("\r\n", $header);
	    $arr = array();

	    foreach ($comma_separated as $value) {
	        if (strpos($value, ':') !== false) {
	            $a = explode(":", $value);
	            $key = $a[0];
	            $v = $a[1];
	            $arr[$key] = $v;
	        } else {
	            array_push($arr, $value);
	        }
	    }
	    $body = substr($data, $headerSize, $bodySize);

        curl_close($curl);
        return  [$arr, $body];
	}
}
