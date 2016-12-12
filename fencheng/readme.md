1、uids.txt说明：每行放置一个yyuid

2、fencheng.php说明：
    修改前两行代码：
                    define('START_TIME', 20160601);
                    define('END_TIME', 20160630);
    即可设定日期范围

3、执行计算：
    在uids.txt中填好需要计算的yyuid集合，并在fencheng.php中设定好日期范围，双击文件“start.bat”即可。
    执行耗时：受yyuid个数和日期范围影响
    
4、计算结果分析：
    （1）程序会自动在当前目录创建data目录，并创建data/upload_uid目录和data/video_uid目录
    （2）data/upload_uid目录：存放多个${yyuid}.csv和total.csv，用于记录每个yyuid在指定日期范围内、所上传视频的日播放量和汇总
    （3）data/video_uid目录：存放多个${yyuid}.csv和total.csv，用于记录每个yyuid在指定日期范围内、存在于其个人主页的日播放量和汇总
    （4）${yyuid}.csv文件说明： 
        文件名以具体yyuid和csv后缀组成，文件中：第一列为vid，第二列为视频时长秒数，第三列至最后列为指定的日期范围，每一天都有WEB/WAP/APP播放量（如20160401_web,20160401_wap,20160401_app,20160402_web,20160402_wap,20160402_app,...）
    （5）total.csv文件说明：包含汇总数据，分别有WEB全播、WEB全收、WEB两分以上全播、WEB两分以上全收、WEB两分以下全播、WEB两分以下全收；WAP全播、WAP全收、WAP两分以上全播、WAP两分以上全收、WAP两分以下全播、WAP两分以下全收；APP全播、APP全收、APP两分以上全播、APP两分以上全收、APP两分以下全播、APP两分以下全收。
    （6）total-ad.csv文件说明：包含汇总数据，分别有WEB效播、WEB效收、WEB两分以上效播、WEB两分以上效收、WEB两分以下效播、WEB两分以下效收；WAP效播、WAP效收、WAP两分以上效播、WAP两分以上效收、WAP两分以下效播、WAP两分以下效收；APP效播、APP效收、APP两分以上效播、APP两分以上效收、APP两分以下效播、APP两分以下效收。

5、调用第三方接口说明：
    （1）获取原始vid与yyuid对应关系
        a、接口URL：http://v.huya.com/?r=test/GetVidByUid
        b、说明：每个yyuid对应的vid集合，分为上传者视频集合与个人主页视频集合
        c、源码：由于接口是放在虎牙前台的TestController.php，随时可能会被删除，故在此处列出源码。
                ---------------------------------------------------------------------------------------
                public function actionGetVidByUid(){
                    $uid = arg('uid');
                    $calcMore = (int)arg('calcMore', 1) == 1 ? true : false;//默认计算更多的分月数据
                    $sql0 = "select vid,duration video_duration from upload_list where yyuid=:uid and can_play=1 and status!=-9 order by vid desc";
                    $sql1 = "select u.vid,duration video_duration from upload_list u left join v_video v on u.vid=v.vid where v.user_id=:uid and video_delete=0 and can_play=1 and status!=-9 order by u.vid desc";
                    $ret0 = obj('Video')->query($sql0, array('uid'=>$uid));
                    $ret1 = obj('Video')->query($sql1, array('uid'=>$uid));
                    $rs = array(
                        'upload_uid'=>$ret0,
                        'video_uid'=>$ret1,
                    );
                    if ($calcMore) {
                        $sql2 = "select vid,duration video_duration from upload_list where yyuid=:uid and can_play=1 and status!=-9 and upload_start_time < ".strtotime('20151201')." order by vid desc";//2015/12之前upload_list
                        $sql3 = "select vid,duration video_duration from upload_list where yyuid=:uid and can_play=1 and status!=-9 and upload_start_time >= ".strtotime('20151201')." and upload_start_time < ".strtotime('20160101')." order by vid desc";//2015/12月份upload_list
                        $sql4 = "select vid,duration video_duration from upload_list where yyuid=:uid and can_play=1 and status!=-9 and upload_start_time >= ".strtotime('20160101')." and upload_start_time < ".strtotime('20160201')." order by vid desc";//2016/1月份upload_list
                        $sql5 = "select vid,duration video_duration from upload_list where yyuid=:uid and can_play=1 and status!=-9 and upload_start_time >= ".strtotime('20160201')." and upload_start_time < ".strtotime('20160301')." order by vid desc";//2016/2月份upload_list
                        $sql6 = "select vid,duration video_duration from upload_list where yyuid=:uid and can_play=1 and status!=-9 and upload_start_time >= ".strtotime('20160301')." and upload_start_time < ".strtotime('20160401')." order by vid desc";//2016/3月份upload_list
                        $sql7 = "select vid,duration video_duration from upload_list where yyuid=:uid and can_play=1 and status!=-9 and upload_start_time >= ".strtotime('20160401')." and upload_start_time < ".strtotime('20160501')." order by vid desc";//2016/4月份upload_list
                        $sql8 = "select vid,duration video_duration from upload_list where yyuid=:uid and can_play=1 and status!=-9 and upload_start_time >= ".strtotime('20160501')." and upload_start_time < ".strtotime('20160601')." order by vid desc";//2016/5月份upload_list
                        $sql9 = "select vid,duration video_duration from upload_list where yyuid=:uid and can_play=1 and status!=-9 and upload_start_time >= ".strtotime('20160601')." and upload_start_time < ".strtotime('20160701')." order by vid desc";//2016/6月份upload_list
                        $sql10 = "select vid,duration video_duration from upload_list where yyuid=:uid and can_play=1 and status!=-9 and upload_start_time >= ".strtotime('20160701')." and upload_start_time < ".strtotime('20160801')." order by vid desc";//2016/7月份upload_list
                        $sql11 = "select vid,duration video_duration from upload_list where yyuid=:uid and can_play=1 and status!=-9 and upload_start_time >= ".strtotime('20160801')." and upload_start_time < ".strtotime('20160901')." order by vid desc";//2016/8月份upload_list
                        $sql12 = "select vid,duration video_duration from upload_list where yyuid=:uid and can_play=1 and status!=-9 and upload_start_time >= ".strtotime('20160901')." and upload_start_time < ".strtotime('20161001')." order by vid desc";//2016/9月份upload_list
                        $sql13 = "select vid,duration video_duration from upload_list where yyuid=:uid and can_play=1 and status!=-9 and upload_start_time >= ".strtotime('20161001')." and upload_start_time < ".strtotime('20161101')." order by vid desc";//2016/10月份upload_list
                        $ret2 = obj('Video')->query($sql2, array('uid'=>$uid));
                        $ret3 = obj('Video')->query($sql3, array('uid'=>$uid));
                        $ret4 = obj('Video')->query($sql4, array('uid'=>$uid));
                        $ret5 = obj('Video')->query($sql5, array('uid'=>$uid));
                        $ret6 = obj('Video')->query($sql6, array('uid'=>$uid));
                        $ret7 = obj('Video')->query($sql7, array('uid'=>$uid));
                        $ret8 = obj('Video')->query($sql8, array('uid'=>$uid));
                        $ret9 = obj('Video')->query($sql9, array('uid'=>$uid));
                        $ret10 = obj('Video')->query($sql10, array('uid'=>$uid));
                        $ret11 = obj('Video')->query($sql11, array('uid'=>$uid));
                        $ret12 = obj('Video')->query($sql12, array('uid'=>$uid));
                        $ret13 = obj('Video')->query($sql13, array('uid'=>$uid));
                        $rs += array(
                            'upload_uid_before'=>$ret2,
                            'upload_uid_201512'=>$ret3,
                            'upload_uid_201601'=>$ret4,
                            'upload_uid_201602'=>$ret5,
                            'upload_uid_201603'=>$ret6,
                            'upload_uid_201604'=>$ret7,
                            'upload_uid_201605'=>$ret8,
                            'upload_uid_201606'=>$ret9,
                            'upload_uid_201607'=>$ret10,
                            'upload_uid_201608'=>$ret11,
                            'upload_uid_201609'=>$ret12,
                            'upload_uid_201610'=>$ret13,
                        );
                    }
                    echo json_encode($rs);
                }
                ---------------------------------------------------------------------------------------
    （2）获取日播原始数据：
        a、接口URL：http://playstats-manager.v.duowan.com/?r=api/getAdPlay
        b、说明：获取每个视频在日期范围内的日播放量
    （3）uids.txt文件内容获取：
        a、接口URL：http://huya.cms.duowan.com/test/RatioList
        b、说明：获取每个分成用户的web/wap/app分成系数
        c、源码：由于接口是放在虎牙后台的TestController.php，随时可能会被删除，故在此处列出源码。
                ---------------------------------------------------------------------------------------
                class TestController extends BaseController {
                    function actionRatioList(){
                        $ret = obj('PodcastAgree')->getCanProfit();
                        foreach ($ret['uids'] as $uid) {
                            $ratios = $list = obj('PodcastRatio')->getRatios($uid);
                            echo "{$uid},{$ratios['ratio_web']},{$ratios['ratio_wap']},{$ratios['ratio_app']}<br>";
                        }
                    }
                }
                ---------------------------------------------------------------------------------------


6、新增的start.php说明：
    由于参与分成人数逐月增多，现有的逐个扫uids.txt方式已不能做到快速计算，故将uids.txt作分段计算改造。
    在每个uids.txt分段中，都会生成对应的临时任务目录，用来保存任务所需的执行文件、uids.txt文件、data目录。
    在临时任务目录中，可执行文件和原fencheng.php完全一样，是复制所得。
    使用：
        编辑start.bat文件，修改并行任务所需的执行文件，目前可供选择的有：
            fencheng.php            --  计算播客所有视频在指定时间段内的播放明细
            fencheng-calcmore.php   --  在fencheng.php基础上，再计算每个月份所发视频，在指定时间段内产生的播放明细
        修改完成后，双击start.bat，即可看到弹出一系列CMD窗口。
            第一个窗口可以看作守护进程，可以监控其它窗口任务的执行是否完成。
            当所有任务完成时，该守护进程会合并所有任务的结果到"cache_fencheng/result"目录中。
        如需根据实际情况，调整每个任务所计算的播客数量，则可编辑start.php文件中的变量"$pieceLen"。
            
        
                
                
7、个别名词解释
    （1）上传者视频集合：在upload_list表里，yyuid字段所筛选的记录。
    （2）个人主页视频集合：在upload_list和v_video表里都存在，由user_id字段所筛选的记录。
