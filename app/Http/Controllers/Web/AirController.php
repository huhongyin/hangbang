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
	private $addUrl = 'http://www.gzairports.com:11111/apartPassengerAppointment.action';
	private $ocrUrl = 'http://apigateway.jianjiaoshuju.com/api/v_1/yzm.html';
	private $cookie = '';

	public function getList(Request $request)
    {
        $list = Plan::paginate(10);

        return view('list', compact('list'));
    }

	public function add()
	{
		return view('air');
	}

	public function doAdd(Request $request)
    {
        try {
            DB::beginTransaction();

            $data = $request->all();
            unset($data['_token']);
            $keys = ["userName", "idCard", "airways", "flightNo", "startStation", "terminalStation", "flightDate", "telNumber", "appointCount"];
            foreach ($data['userName'] as $key => $value) {
                $info = new Plan();
                $info->userName = $value;
                foreach ($keys as $v) {
                    $info->{$v} = $data[$v][$key];
                }
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
        $config = Config::first();
        $startTime = strtotime($config->start_time);
		info('配置文件:'.print_r($config, true));
		info('当前时间:'. date('Y-m-d H:i:s', time()));
        if($startTime > time()){
            info('未到执行时间');
            exit;
        }

		info('达到执行时间');
        if($config->is_start == 0){
        	info('启动任务');
            //未启动,启动任务
            $config->is_start = 1;
            $config->save();
            $this->shell();
        }else{
        	info('更新其他');
            //判断是否有其他数据需要更新
            if(Plan::where('status', 0)->count() > 0){
                $this->shell();
            }
        }
	}

	public function shell()
    {
        $list = Plan::where('status', 0)->get();

        if(!empty($list)){
            info('请求数据有:' . print_r($list, true));
            foreach ($list as $value){
                $codeStr = $this->getCodeStr();

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
                ];

                $bodys = '';
                foreach ($data as $key => $v) {
                    $bodys .= '&' . $key . '=' . $v;
                }
                $bodys = ltrim($bodys, '&');
                info('请求参数:'. print_r($data, true));
                info('请求body:'. $bodys);
                $res = $this->post($this->addUrl, [], $bodys);

                info('请求结果:'. print_r($res, true));
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
                            default:
                                    $config = Config::first();
                                    $config->is_start = 0;
                                    $config->save();
                                break;
                        }
                        break;
                }
            }
        }else{
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
