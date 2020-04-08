<?php
class MemoryHelper{
	static function get_key($fsize, $file){
        if(!file_exists(JPATH_ROOT.'/tmp/'.$file)){
            touch(JPATH_ROOT.'/tmp/'.$file);
        }
       $SharedMemorySegment = @shmop_open(0xee4, "c", 0770, 100); debug($SharedMemorySegment);die;
        $shmkey = @shmop_open(ftok(JPATH_ROOT.'/tmp/'.$file, 'R'), "c", 0644, $fsize);
        debug($shmkey);
        if(!$shmkey) {
                return false;
        }else{
            return $shmkey;
        }//fi
    }
    static function writemem($fdata, $shmkey){
        if(MEMCOMPRESS && function_exists('gzcompress')){
            $fdata = @gzcompress($fdata, MEMCOMPRESSLVL);
        }
        $fsize = strlen($fdata);
        $shm_bytes_written = shmop_write($shmkey, $fdata, 0);
        updatestats($shm_bytes_written, "add");
        if($shm_bytes_written != $fsize) {
                return false;
        }else{
            return $shm_bytes_written;
        }
    }
    
    static function readmem($shmkey, $shm_size){
        $my_string = @shmop_read($shmkey, 0, $shm_size);
        if(MEMCOMPRESS && function_exists('gzuncompress')){
            $my_string = @gzuncompress($my_string);
        }
        if(!$my_string) {
                return false;
        }else{
            return $my_string;
        }//fi
    }
    static function deletemem($shmkey){
        $size = @shmop_size($shmkey);
        if($size > 0){ updatestats($size, "del"); }
        if(!@shmop_delete($shmkey)) {
            @shmop_close($shmkey);
                return false;
        }else{
            @shmop_close($shmkey);
            return true;
        }
    }
    static function closemem($shmkey){
        if(!shmop_close($shmkey)) {
                return false;
        }else{
            return true;
        }
    }
    static function iskey($size, $key){
        if($ret = get_key($size, $key)){
            return $ret;
        }else{
            return false;
        }
    } 
	
}