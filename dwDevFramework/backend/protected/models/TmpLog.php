<?php
/**
 * 临时日志表
 *      强烈建议：不适用于频繁的记录，仅用于重要、关键的记录
 */
class TmpLog extends Model {
    
    protected $table_name = 'tmp_log';
    
    public function add($name, $content, $note = 'note'){
        if (is_array($content)) {
            $content = json_encode($content, JSON_UNESCAPED_UNICODE);
        } elseif (is_object($content)) {
            $content = json_encode((array)$content, JSON_UNESCAPED_UNICODE);
        } else {
            $content = (string) $content;
        }
        $rs = $this->insert(array(
        	'name' => $name,
            'content' => $content,
            'note' => $note,
            'log_ip' => getIP()?:'',
        ));
        return $rs;
    }
    
}
