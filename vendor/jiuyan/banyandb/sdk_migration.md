banyandb sdk migration
===

## sdk迁移目标
- 替换ssdb api
- 下线ssdb router，简化部署和维护

## 参与人员
- java php 大数据 搜索 运维

## sdk迁移步骤
- 新业务统一用banyandb sdk开发
- 按业务重要性优先级来迁移

## 业务优先级较低
``` shell
 in_lbs lbs
 in_lbs_idx lbs
 in_lbs_user_idx lbs
 in_search_update 搜索更新列表
 in_search_data 搜索基础数据
 in_search_refresh

 magic_page magic_page页面
 in_magic_page 同magic_page, 新的magic page算法

 push_exp 表情包app 推送; nc
 in_web_h5 H5首页
 in_test 标签排序
 in_net ip库: 此刻
 in_conf 18楼
 in_applink 广告对账
 in_topic 老话题
 in_user_photo 自拍照
 in_paster_rec 贴纸关联推荐, 老版本
 in_recommend 临时库
 in_ubas BI报表
```

## 推送，快捷消息，推荐，聊天相关
``` shell
 in_notice_cnt php读写, mc读
 in_notice_list mc
 push_direct 定向推送, 计数, 去重; ncdirect
 push_apns
 push_user

 in_nc nc
 in_device ncapi 设备号
 in_push worker 混合
 in_active_user_info worker, lc写 gps, 街道
 in_wire_msg wire/mc
 in_friend mc读, 新的好友; 通讯录和好友列表

 in_paster 贴纸推荐, 贴纸商城, 玩字推荐
 in_rec 发图异步推荐, 关联标签
 in_friend_rec

 photo_comment 热门评论, 图片详情的评论部分
 in_pbuffer 新帖鉴定
 in_wordart 玩字, 字体是否已经生成; 玩字使用时查询
 in_imbridge 聊天
```

## 二度关系，活动相关, mongodb迁移库
``` shell
 in_promo 活动
 in_relationlink 大数据: 一度二度关系推荐
 in_relationlink_new
 in_relationlink_twodegree

 photo_exif
 in_photo_refactoring
 in_user_ext_refactoring
```

## 重要业务
``` shell
 in_idx_tag tag搜索
 in_idx_user user搜索
 in_tag_cnt 话题下的计数 lc
 in_tag_user 参与,收藏的人, tag详情页; 订阅的tag动态
 in_tag_photo 话题下数据,tag详情页
 in_tag_algo 话题图片排序算法, 发图时异步话题推荐

 in_user_task 任务,in币相关
 in_user_count 用户相关计数器, in币
 in_printer_center 打印线; 订单支付; 优惠券兑换;
 in_user_info 性别, 隐藏手机号; 很重要!!!

 in_user_rec 看过的照片, 发现页
 in_photo_rec 2.7前的发现页; 过滤垃圾图片
 in_visitor 访客记录
 in_discover 发现页推荐

 in_user_watch 用户的关注, 判定关注关系; 摇一摇
 in_hot_user 达人列表; 达人关注关系(不再使用)
 in_photo 曝光,点击 图片详情+in记 lc
 in_new_exposure 广场点击
 in_news 此刻, 发现页 最新
 in_user uc
 in_sms 短信验证码; 手机注册

 in_story
 in_story_cnt
 in_paster_rec_new
 in_story_visitor
 fu_li_she
```

## banyandb库表信息
``` shell
            "ns": "banyan" 
                    "table": "delay" 
            "ns": "push_exp" 
                    "table": "deftab" 
            "ns": "in_pbuffer" 
                    "table": "tag_photo_released" 
                    "table": "vote" 
            "ns": "in_user" 
                    "table": "deftab" 
            "ns": "in_promo" 
                    "table": "deftab" 
            "ns": "in_user_info" 
                    "table": "info" 
            "ns": "in_user_rec" 
                    "table": "viewed_photo" 
                    "table": "viewed_tag" 
                    "table": "viewed_story" 
            "ns": "in_magic_page" 
                    "table": "magic_page" 
            "ns": "in_lbs" 
                    "table": "deftab" 
            "ns": "in_lbs_idx" 
                    "table": "deftab" 
            "ns": "in_lbs_user_idx" 
                    "table": "user" 
            "ns": "push_apns" 
                    "table": "deftab" 
            "ns": "push_user" 
                    "table": "deftab" 
            "ns": "in_idx_tag" 
                    "table": "uniq_match" 
            "ns": "in_idx_user" 
                    "table": "uniq_in_match" 
                    "table": "uniq_name_match" 
            "ns": "in_tag_user" 
                    "table": "deftab" 
            "ns": "in_user_watch" 
                    "table": "friend" 
                    "table": "user_fans" 
                    "table": "user_male_fans" 
                    "table": "relation" 
                    "table": "shake" 
            "ns": "in_friend" 
                    "table": "cache" 
                    "table": "new_friend" 
            "ns": "in_tag_photo" 
                    "table": "valid_square_times" 
                    "table": "photo_tag" 
                    "table": "tag_merge" 
                    "table": "tag_photo_hot" 
                    "table": "tag_photo_select" 
                    "table": "tag_photo_zan" 
                    "table": "tag_photo_publish" 
                    "table": "tag_photo_stick" 
            "ns": "in_user_task" 
                    "table": "task" 
            "ns": "photo_comment" 
                    "table": "deftab" 
            "ns": "in_device" 
                    "table": "device" 
            "ns": "in_web_h5" 
                    "table": "h5_home" 
            "ns": "in_test" 
                    "table": "test_sort" 
            "ns": "in_wordart" 
                    "table": "wordart" 
            "ns": "in_push" 
                    "table": "friend" 
                    "table": "feedback" 
            "ns": "in_net" 
                    "table": "ip2city" 
            "ns": "in_rec" 
                    "table": "topic" 
            "ns": "in_photo_rec" 
                    "table": "filter" 
                    "table": "user_based_cf" 
                    "table": "most_loved_photo" 
                    "table": "photo_click_rec" 
                    "table": "photo_type" 
            "ns": "in_conf" 
                    "table": "beijing" 
            "ns": "in_news" 
                    "table": "realtime" 
                    "table": "city_photo_latest" 
            "ns": "in_paster" 
                    "table": "deftab" 
            "ns": "in_sms" 
                    "table": "auth_code" 
            "ns": "in_active_user_info" 
                    "table": "user" 
                    "table": "recently_interest" 
                    "table": "init_ts" 
            "ns": "in_applink" 
                    "table": "ad" 
            "ns": "in_user_photo" 
                    "table": "rec_portrait" 
            "ns": "in_search_data" 
                    "table": "tag" 
                    "table": "user" 
                    "table": "poi" 
            "ns": "in_search_update" 
                    "table": "tag" 
                    "table": "user" 
            "ns": "in_relationlink" 
                    "table": "relation" 
                    "table": "contacts" 
                    "table": "weibo" 
                    "table": "twodegree" 
                    "table": "follow2" 
                    "table": "contacts2" 
                    "table": "weibo2" 
                    "table": "hybrid" 
                    "table": "weixin" 
                    "table": "weixin2" 
            "ns": "in_tag_cnt" 
                    "table": "tag_counter_list" 
            "ns": "in_user_count" 
                    "table": "user_photo_count" 
            "ns": "in_photo" 
                    "table": "deftab" 
            "ns": "in_notice_cnt" 
                    "table": "deftab" 
            "ns": "in_notice_list" 
                    "table": "deftab" 
            "ns": "in_imbridge" 
                    "table": "im" 
                    "table": "imcs" 
            "ns": "in_topic" 
                    "table": "deftab" 
            "ns": "in_ubas" 
                    "table": "ubas" 
            "ns": "in_paster_rec" 
                    "table": "recrelation" 
                    "table": "recretion" 
            "ns": "in_printer_center" 
                    "table": "user" 
                    "table": "printer_code" 
                    "table": "order" 
                    "table": "user_hint" 
                    "table": "goods" 
            "ns": "in_recommend" 
                    "table": "user" 
            "ns": "magic_page" 
                    "table": "magic_page" 
            "ns": "in_hot_user" 
                    "table": "hot_user" 
                    "table": "watch_hot" 
                    "table": "daren_watch" 
                    "table": "daren_rec" 
                    "table": "inner" 
            "ns": "in_story" 
                    "table": "counter" 
                    "table": "photo" 
                    "table": "comment" 
                    "table": "group" 
                    "table": "story" 
                    "table": "zan" 
                    "table": "rec" 
                    "table": "rec_photos" 
                    "table": "detail_rec" 
            "ns": "in_friend_rec" 
                    "table": "rec_time" 
                    "table": "rec_days" 
                    "table": "talent" 
            "ns": "in_nc" 
                    "table": "nc" 
            "ns": "in_wire_msg" 
                    "table": "wire" 
                    "table": "mc" 
            "ns": "push_direct" 
                    "table": "direct" 
            "ns": "in_visitor" 
                    "table": "timeline" 
            "ns": "in_discover" 
                    "table": "discover" 
                    "table": "hq_onestep" 
            "ns": "in_new_exposure" 
                    "table": "expcnt" 
            "ns": "in_tag_algo" 
                    "table": "vote" 
                    "table": "hotter" 
            "ns": "in_story_cnt" 
                    "table": "count" 
            "ns": "in_paster_rec_new" 
                    "table": "paster_rec" 
            "ns": "in_relationlink_new" 
                    "table": "contacts" 
                    "table": "weibo" 
                    "table": "weixin" 
                    "table": "follow2" 
                    "table": "contacts2" 
                    "table": "weibo2" 
                    "table": "weixin2" 
                    "table": "hybrid" 
                    "table": "weibo2way" 
                    "table": "onestep_ignore" 
                    "table": "twostep_ignore" 
                    "table": "onestep_exposure" 
                    "table": "contacts_rev" 
                    "table": "weibo_rev" 
            "ns": "in_story_visitor" 
                    "table": "wx_friend" 
            "ns": "in_search_refresh" 
                    "table": "user_position" 
                    "table": "user_post" 
            "ns": "in_relationlink_twodegree" 
                    "table": "twodegree" 
            "ns": "photo_exif" 
                    "table": "exif" 
            "ns": "in_user_index" 
                    "table": "private_key" 
            "ns": "fu_li_she" 
                    "table": "goods" 
            "ns": "in_photo_refactoring" 
                    "table": "photo" 
                    "table": "photo_index" 
            "ns": "in_user_ext_refactoring" 
                    "table": "user_counter" 
            "ns": "in_topic_refactoring" 
                    "table": "in_topic" 
```
