<?php
$data = array(
    'name'    => '前世死因分析',
    'appid'   => 'siyin',
    'sucai'   => 'siyin.png', // 素材写法：单文件file.png，目录中随机文件dir/*.png
    'plot'    => array(
        array(
            'pos'     => '110:130',
            // 文字来源：profile: 从档案取
            //           plain: 纯文字，
            //           rand: 从给定范围随机取值，
            //           randtext: “|”分隔的文字随机取一段
            'text'    => 'profile:nickname', 
            'prepend' => '',
            'append'  => '%',
            'font'    => 'msyh.ttf',  // data/fonts
            'size'    => 10,
            'angle'   => 0,   // rotation, 逆时针方向
            'color'   => '#ff0000',
        ),
        array(
            'pos'     => '170:200',
            'text'    => 'rand:1-99',
            'prepend' => '',
            'append'  => '%',
            'font'    => 'simhei.ttf',  // data/fonts
            'size'    => 10,
            'angle'   => 0,   // rotation, 逆时针方向
            'color'   => '#660099',
        ),
        array(
            'pos'     => '170:250',
            'text'    => 'rand:1-99',
            'prepend' => '',
            'append'  => '%',
            'font'    => 'simsun.ttf',  // data/fonts
            'size'    => 10,
            'angle'   => 0,   // rotation, 逆时针方向
            'color'   => '#996633',
        ),
    )
);
