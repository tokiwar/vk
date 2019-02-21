<?php
$i = 7; //стартовое время для постинга
$group_id = "151246399"; //ID группы
$v = "5.73"; //ВЕРСИЯ API
$album_id = '246622108'; //ID альбом группы
$token = '5c68b79fede480f2188d69f98d3aae9c267e8695d8e6bcb5b4b58b6c6fef6477933597d4240666e161995'; //access token
$curName = 'C:\Users\grasi\Desktop\pixiv\AUTHORS/';
$loadName = 'C:\Users\grasi\Desktop\PICS_FOR_PUBLIC\TO_LOAD\LOADED/';
$toAlbun = 'C:\Users\grasi\Desktop\pixivToAlbum/';
//сервер загрузки в альбом
//$serverUrl=file_get_contents("https://api.vk.com/method/photos.getUploadServer?group_id=".$group_id."&album_id=".$album_id."&access_token=".$token."&v=".$v);
//$serverUrl = json_decode($serverUrl)->response->upload_url;
//print_r($serverUrl);
//сервер загрузки
$url = file_get_contents("https://api.vk.com/method/photos.getWallUploadServer?group_id=" . $group_id . "&access_token=" . $token . "&v=" . $v);
$url = json_decode($url)->response->upload_url;
//print_r($url);
//список файлов
for ($x = 0; $x < 15; $x++) {
    $myFiles = scandir($curName);
    unset($myFiles[0]);
    unset($myFiles[1]);
    sort($myFiles);
    shuffle($myFiles);
//print_r($myFiles);
    $info_array = explode("_", $myFiles[0]);
    //print_r($info_array[0]);
    $post_data = array("file1" => new CURLFile(realpath($curName . $myFiles[0])));
    //print_r($post_data);
//РАСЧЕТ unixtime
    date_default_timezone_set('Europe/Moscow');
//$date = date('d-m-Y H:i:s', time());
    $day = 24;
    $month = 2;
    $year = 2019;
    $date = ($day . '-' . $month . '-' . $year . ' ' . $i . ':00');
    $unixtime = strtotime($date);
    print_r($date);
    //print_r($unixtime);
	print_r($i);
	print_r("\n");
//загрузка картинки на стену
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
    $result = json_decode(curl_exec($ch), true);
    curl_close($ch);
    //print_r($result);
//сохранение картинки
    $saveWallPhoto = file_get_contents("https://api.vk.com/method/photos.saveWallPhoto?group_id=" . $group_id . "&photo=" . $result['photo'] . "&server=" . $result['server'] . "&hash=" . $result['hash'] . "&access_token=" . $token . "&v=" . $v);
    $saveWallPhoto = json_decode($saveWallPhoto, true);
    //print_r($saveWallPhoto);
    $photoId = $saveWallPhoto['response']['0']['id'];
    $myId = $saveWallPhoto['response']['0']['owner_id'];
    $subStr = substr($info_array[1], 0, strlen($info_array[1]) - 4);
    $attachment_id = 'photo' . $myId . '_' . $photoId . ',https://www.pixiv.net/member_illust.php?mode=medium%26illust_id=' . $subStr;
    $page_content = file_get_contents('https://www.pixiv.net/member_illust.php?mode=medium&illust_id=' . $subStr);
//$page_content=file_get_contents("https://www.pixiv.net/member_illust.php?mode=medium&illust_id=63640049");
    preg_match_all("|<title>(.*)</title>|sUSi", $page_content, $titles);
//print_r ($titles[1]);
   // print_r($titles);
//print_r($page_content);
//return;
//$toAdd=substr($titles[1][0],9,strlen($titles[1][0]));
    $toAdd = $titles[1][0];
    $toAdd = substr($toAdd, 0, strlen($toAdd) - 8);
    $toAdd = $toAdd . ' [pixiv]';
    str_replace(' ', '', $toAdd);
   // print_r($toAdd);
//return;
    $msg = "%23art%40kawaii_desu_yo%0A%23PixivID_" . $info_array[0] . '%40kawaii_desu_yo%0A' . urlencode($toAdd);
    //print_r($msg);
   // print_r($attachment_id);
    str_replace(' ', '', $msg);
    str_replace(' ', '', $attachment_id);
//return;
//постинг картинки
    $owner_id = "-151246399";
    $post_dat_fucking_picture = file_get_contents("https://api.vk.com/method/wall.post?owner_id=" . $owner_id . "&from_group=1&message=" . $msg . "&attachments=" . $attachment_id . "&publish_date=" . $unixtime . "&signed=1&access_token=" . $token . "&v=" . $v);
    $post_dat_fucking_picture = json_decode($post_dat_fucking_picture, true);
   // print_r($post_dat_fucking_picture);
    //if (is_null($post_dat_fucking_picture . ['error'])) {
    if (!array_key_exists('error', $post_dat_fucking_picture)) {
        copy($curName . $myFiles[0], $toAlbun . $myFiles[0]);
        rename($curName . $myFiles[0], $loadName . $myFiles[0]);
    }
    $i += 1;
//загрузка карткинки на сервер стены
//$ch = curl_init();
//curl_setopt($ch, CURLOPT_URL, $serverUrl);
//curl_setopt($ch, CURLOPT_POST, true);
//curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
//curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
//curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
//$serverResult = json_decode(curl_exec($ch),true);
//curl_close( $ch );
//print_r($serverResult);
//сохранение картинки в альбом
//$photoSaveToAlbum=file_get_contents("https://api.vk.com/method/photos.save?album_id=".$album_id."&group_id=".$group_id."&server=".$serverResult['server']."&photos_list=".$serverResult['photos_list']."&hash=".$serverResult['hash']."&access_token=".$token."&v=".$v);
//$photoSaveToAlbum=json_decode($photoSaveToAlbum,true);
//print_r($photoSaveToAlbum);
}
?>
