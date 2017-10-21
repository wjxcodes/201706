<?php
/**
 * @author demo
 * @date 2015年8月11日
 */
/**
 * 用于调用APP平台的统计数据以及操作推送版本更新等
 */
namespace Common\Controller;
class AppPlatformLayerController extends CommonController{
    /**
     * 描述：
     * 返回字段描述installations 总⽤用户数 new_users 新增⽤用户 active_users 活跃⽤用户 launches 启动次数 date 日期
     * @param array $platform 需要统计的平台，全平台为['android','ios','androidPad','iosPad']
     * @return array 返回统计数据
     * @author demo
     */
    private function getUMData($platform) {
        $result = [];
        foreach ($platform as $iPlatform) {
            if ($iPlatform === 'android') {
                $androidAppKey = C('APP_PLATFORM_KEY')['YOUMENG']['ANDROID_APP_KEY'];
                $authToken = C('APP_PLATFORM_KEY')['YOUMENG']['AUTH_TOKEN'];
                $url = 'http://api.umeng.com/today_data?appkey=' . $androidAppKey . '&auth_token=' . $authToken;
                $curlResult = simpleCurl($url, $paramData = null, $header = null, $isPost = false);
                if ($curlResult !== false) {
                    $curlResult = formatString('object2Array',json_decode($curlResult));
                    if ($curlResult && !array_key_exists('error', $curlResult)) {
                        $result['android'] = $curlResult;
                    }
                }
            }
        }
        return $result;
    }

    /**
     * 描述：
     * devicesCount    设备总数
     * newRegsCo    nt	当天新注册用户数
     * newUpdat    Count	当天新升级用户数
     * activeCou    tInToday	当天活跃用户数
     * activeCoun    InSevenDays	七日内活跃用户数
     * activeCo    ntInThirtyDays	三十日内活跃用户数
     * @param $platform
     * @return array|bool
     * @author demo
     */
    /*
    private function getApiCloudData($platform){
        if(!is_array($platform)||count($platform)<1){
            return false;
        }
        $start = date('Y-m-d',time());
        $end = date('Y-m-d',time());
        $result = [];
        foreach($platform as $iPlatform){
            if($iPlatform === 'android'){
                $url = 'https://r.apicloud.com/analytics/getAppStatisticDataById';
                $config = C('APP_PLATFORM_KEY')['APICLOUD']['ANDROID'];
                $microTime = str_replace(microtime(true),'.','');
                $header = [
                    'X-APICloud-AppId:'.$config['APP_ID'],
                    'X-APICloud-AppKey:'.sha1($config['APP_ID'].'UZ'.$config['APP_KEY'].'UZ'.$microTime).'.'.$microTime,
                ];
                $paramData = [
                    'startDate'=>$start,
                    'endDate'=>$end,
                ];
                $curlResult = $this->simpleCurl($url,$paramData,$header,$isPost=true);
                if($curlResult!==false){
                    $curlResult = objectToArray(json_decode($curlResult));
                    if(array_key_exists('st',$curlResult)&&$curlResult['st']===1){
                        $result['android'] = $curlResult['msg'][0];
                    }
                }
            }
        }
        return $result;
    }*/

    /**
     * 获取APP平台统计信息
     * 调取最新的安装量方法：R('Common/AppPlatformLayer/getStatistic',[$platform=['android']])
     * @param array $platform 需要统计的平台，全平台为['android','ios','androidPad','iosPad']
     * @return array ['android'=>[getUMData返回的数组]]
     * @author demo
     */
    public function getStatistic($platform){
        $time = S('appUMStatistic')['time'];
        if($time!=date('YmdH',time())){
            $result = $this->getUMData($platform);
            $result&&S('appUMStatistic',['data'=>$result,'time'=>date('YmdH',time())]);
        }
        return S('appUMStatistic')['data'];
    }
}

