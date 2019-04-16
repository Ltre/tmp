<?php

class SearchController extends BaseController {

    function actionArticle(){
        echo json_encode([
            'code' => 0,
            'data' => [
                'list' => [],
                'next' => 0,
                'timeline' => 0,
            ],
            'result' => 1,
        ]);
    }

}

