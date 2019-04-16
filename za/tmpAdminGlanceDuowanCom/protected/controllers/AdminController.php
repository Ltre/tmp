<?php

class AdminController extends BaseController {

    function actionGetAllUps(){
        echo json_encode([
            'code' => 0,
            'data' => [
                'list' => [],
            ],
            'result' => 1,
        ]);
    }

}