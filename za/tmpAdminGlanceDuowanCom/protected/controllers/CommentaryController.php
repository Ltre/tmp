<?php

class CommentaryController extends BaseController {

    function actionAdd(){
        echo json_encode([
            'code' => 0,
            'result' => 1,
        ]);
    }

}