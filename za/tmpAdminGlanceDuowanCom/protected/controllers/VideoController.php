<?php

class VideoController extends BaseController {

    function actionNotify(){
        echo json_encode([
            'code' => 0,
            'msg' => 'OK',
            'result' => 0,
        ]);
    }

}

