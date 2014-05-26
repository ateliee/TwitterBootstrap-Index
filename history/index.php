<?
        // 文字コード設定
        mb_language('uni');
        mb_internal_encoding('UTF-8');
        mb_http_input('UTF-8');
        mb_http_output('UTF-8');
        date_default_timezone_set("Asia/Tokyo");
        // 
        $L_URL = $_SERVER["SCRIPT_FILENAME"];
        $L_URL = preg_replace("/^".preg_quote($_SERVER["DOCUMENT_ROOT"],"/")."/","http://".$_SERVER["HTTP_HOST"]."/",$L_URL);
        $L_URL = preg_replace("/\/".preg_quote(basename($L_URL),"/")."$/","",$L_URL);
        //$parse_urls = parse_url($L_URL);
        // 
        $L_PATH = preg_replace("/^".preg_quote(dirname($_SERVER["SCRIPT_NAME"]),"/")."/","",urldecode($_SERVER["REQUEST_URI"]));
        // blacklist
        $L_UNVISIBLE_LIST = array(
                "data",
                "index\.php",
                ".+\.sql",
                "\.htaccess",
                "log\.txt",
                "run",
        );
        
        $L_BASE_DIR = "../backup";
        $L_URL_DIR = ".";
        $L_PATH_DIR = "/";
        $L_DIR_NAME = "/";
        if($L_PATH != ""){
                $L_PATH_DIR = $L_PATH;
                $L_DIR_NAME = basename($L_PATH);
        }
        if(!file_exists($L_BASE_DIR.$L_PATH_DIR)){
                header("Location: ".$L_URL);
                exit;
        }
        if(!is_dir($L_BASE_DIR.$L_PATH_DIR)){
                $filename = $L_BASE_DIR.$L_PATH_DIR;
                $filesize = filesize($filename);
                $name = mb_convert_encoding(basename($filename),"SHIFT-JIS",mb_internal_encoding());
                header("Content-type: "."application/octet-stream");
                header('Content-Length: '.$filesize);
                header("Content-Disposition: inline; filename=\"".$name."\"");
                
                readfile($filename);
                exit;
        }
        if(!preg_match("/(.*)\/$/",$L_PATH_DIR)){
                $L_PATH_DIR .= "/";
        }
        $L_PARENT_DIR = dirname($L_URL_DIR.$L_PATH_DIR);
        $L_IS_ROOT = ($L_PATH_DIR == "/") ? true : false;
        
        $folder_list = array();
        if($dir = opendir($L_BASE_DIR.$L_PATH_DIR)){
                while(($file = readdir($dir)) !== false){
                        if($file == "." || $file == ".."){
                                continue;
                        }
                        $filename = $L_BASE_DIR.$L_PATH_DIR.$file;
                        
                        $item = array();
                        $item["name"] = $file;
                        $item["filename"] = $L_URL_DIR.$L_PATH_DIR.$file;
                        $item["filetime"] = date("Y/m/d H:i:s",filemtime($filename));
                        $item["is_dir"] = is_dir($filename);
                        $item["filesize"] = get_file_size($item["is_dir"] ? 0 : filesize($filename));
                        $item["description"] = "";
                        
                        $check = true;
                        if($L_IS_ROOT){
                                foreach($L_UNVISIBLE_LIST as $key){
                                        if(preg_match("/".$key."/",$file)){
                                                $check = false;
                                        }
                                }
                        }
                        
                        if($check){
                                $folder_list[] = $item;
                        }
                }
                closedir($dir);
        }
function get_file_size($byte) {
        $n = 0;
        $b = $byte;
        $sizes = array("B","KB","MB","GB","TB");
        foreach($sizes as $k => $v){
                if((count($sizes) - 1) <= $n)   break;
                if($b < 1024){
                        break;
                }
                $b /= 1024;
                $n ++;
        }
        return round($b, 2).$sizes[$n];
}
?><!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Index Of <?php print ($L_DIR_NAME); ?></title>
    <base href="<?php print $L_URL; ?>/">
    <!-- Bootstrap -->
    <link href="data/css/bootstrap.min.css" rel="stylesheet">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
    <style type="text/css">
.btn-small {
  padding: 3px 9px;
  font-size: 12px;
  line-height: 18px;
}
    </style>
  </head>
  <body>
    <div class="container">
    <h1>
<?php
        $list = explode("/",$L_PATH_DIR);
        $dir = array();
        print "/";
        foreach($list as $val){
                if($val != ""){
                        $dir[] = $val;
                        print '<a href="./'.implode("/",$dir).'">'.$val.'</a>';
                        print "/";
                }
        }
?>
    </h1>

    <div class="table-responsive">
      <table class="table table-striped">
        <thead>
          <tr>
            <th>ファイル名</th>
            <th width="180">最終更新日</th>
            <th width="80">サイズ</th>
            <th width="110">ダウンロード</th>
          </tr>
        </thead>
        <tbody>
        <?php if(!$L_IS_ROOT){ ?>
          <tr>
            <td colspan="4"><a href="<?php print ($L_PARENT_DIR); ?>"><img src="data/images/folder.png" width="16" height="16" alt=""> 1つ上の階層へ</a></td>
          </tr>
        <?php } ?>
        <?php foreach($folder_list as $key => $item){ ?>
          <tr>
            <td>
                　
                <?php if($item["is_dir"]){ ?>
                <img src="data/images/folder.png" width="16" height="16" alt="">
                <?php }else{ ?>
                <img src="data/images/file.png" width="16" height="16" alt="">
                <?php } ?>
                <a href="<?php print ($item["filename"]); ?>"><?php print ($item["name"]); ?></a>
            </td>
            <td><?php print ($item["filetime"]); ?></td>
            <td>
                <?php if(!$item["is_dir"]){ ?>
                        <?php print ($item["filesize"]); ?>
                <?php } ?>
            </td>
            <td>
                <?php if(!$item["is_dir"]){ ?>
                <a href="<?php print ($item["filename"]); ?>" class="btn btn-default btn-small">ダウンロード</a>
                <?php } ?>
            </td>
          </tr>
          <?php } ?>
        </tbody>
      </table>
    </div>
    </div>
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="data/js/bootstrap.min.js"></script>
  </body>
</html>
