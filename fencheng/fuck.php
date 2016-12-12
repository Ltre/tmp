-- 查看上传者每日截至当日的总视频数
select yyuid,FROM_UNIXTIME(upload_start_time, '%Y%m%d') as date,count(1) from upload_list,v_podcast_agree a where yyuid=a.user_id and a.can_profit=1 and a.success=1 and upload_start_time < UNIX_TIMESTAMP('20160501') and status!=-9 and can_play=1 and duration>=120 group by yyuid,date

