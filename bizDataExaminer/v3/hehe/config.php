<?php

$GLOBALS = [
    'sources' => [
        'tu' => [
            'list_api' => 'http://tu.duowan.com/index.php?r=manager/getlist',
            'd' => "OgWvoap4Inmc.SVAl7'WSdlj-6QpVHUEoe!Foe(IL8GD-6Ed-r(~)5@Bm5De'1z2seErGjp7'uMK-6_LNpMA'1'MGwx1NpMi_U'1!yz6Uf42@0Qb~Z(4Kaeb)5p0DaSpHq",
            'keywordFields' => ['title', 'description'],
        ],
        'video' => [
            'list_api' => 'http://video.duowan.com/?r=test/examineList',
            'd' => "-Bm9K5WU-6FcJhe0PmK7da*3Sr~m(~_7Terp)5xbw8j7'1(HYOw0)H_DGt.0_JvhXKRzOj_sGE-6!nAy-6Si74-6PqVsQnsb",
            'keywordFields' => ['video_title', 'video_subtitle', 'video_intro'],
        ],
        'user' => [
            'list_api' => 'http://video.duowan.com/?r=test/examineList4User',
            'd' => "",//待定
            'keywordFields' => ['nickname', 'user_intro'],
        ],
        'comment' => [
            'list_api' => 'http://comment3.duowan.com/index.php?r=mgr/getlist',
            'd' => "(BQti4_L@ERtpdFkSE~ORcJH)5OnFpSwXkDA@0!BQbIF@0)GTC*E.0WpxjFsGj.QXisq-6Mqzb'X.0UxVLLfv7Rnuh.0SPu1o1tfr5ZDUw*R)q~Z-6_sus~1WmYV~1u5_EA7TC..",
            'keywordFields' => ['content'],
        ],
        'tucao' => [
            'list_api' => 'http://comment3.duowan.com/index.php?r=mgr/gettclist',
            'd' => "DfFvHvXtt0-Lse'W)qAy-6@zn7@EF2SP)5z8UfZW'9UvYHQr'1WpQC-TDg.QWh~Z@0n1zbDr.0ybwmRlKmQmj6.0PMKh!FqcWAVzWyj7L6ig'9M7TR)5H7*@'9DeIfYvyh))",
            'keywordFields' => ['content'],
        ],
        'bbspost' => [
            'list_api' => 'http://bbs.duowan.com/api/manage.php?op=getlist',
            'd' => "Ols0Suv6Mroana_uif)5OpGiG1ro.8w7k3Lm'1!O'TtjKnb1ZDG1CA-6vdIjd3Sdig_7ImCe'X.0Qt@QLf.KEai5.0'HEtc1RcRP-6N8hf@0)v*@-6~C_EUrJs",
            'keywordFields' => ['content', 'title'],
        ],
        'bbsuser' => [
            'list_api' => 'http://bbs.duowan.com/api/manage.php?op=getuser',
            'd' => "Cbk6z7OkLqEq_UPcVS'9r2Bd-rsp.8w7SBx8'1Iu(O'Zt6WMs6M7ki-6Ksy9JzPaYW_7QuKmIw'1q3ZPQkw8D9Fs.0Zxj8'Y@lus(4N8ge(4D3QN_7-H_Eu1wf",
            'keywordFields' => ['content', 'title'],
        ],
        
    ],
];

