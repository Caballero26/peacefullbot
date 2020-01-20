<?php

define('CALLBACK_API_EVENT_CONFIRMATION', 'confirmation');
define('CALLBACK_API_EVENT_MESSAGE_NEW', 'message_new');

require_once 'config.php';
require_once 'global.php';

require_once 'api/vk_api.php';
require_once 'api/yandex_api.php';

require_once 'bot/bot.php';

if (!isset($_REQUEST)) {
return;
}

//Строка для подтверждения адреса сервера из настроек Callback API
$confirmation_token = '0e77be4a';
//Ключ доступа сообщества
$token = '86aa3eb2b53ed8e337e4d419d1ed6e8df4449c96b5d8a985c42896ff3f147065fbbf873475c4b3111e96f';
//Получаем и декодируем уведомление
$data = json_decode(file_get_contents('php://input'));

if ($data->type == 'confirmation') {
   echo $confirmation_token;
}

if ($data->type == 'message_new') {
	
    $peer_id = $data->object->message->peer_id;
	$from_id = $data->object->message->from_id;
	$message = $data->object->message->text;
	
    if ($message == 'Привет') {
        
        $user_info = json_decode(file_get_contents("https://api.vk.com/method/users.get?user_ids={$from_id}&access_token={$token}&v=5.0"));
        $user_name = $user_info->response[0]->first_name;
        $request_params = array(
			'message' => "Привет, {$user_name}! Дата твоей регистрации: ",
			'peer_id' => $peer_id,
			'random_id'	 => $from_id,
			'access_token' => $token,
			'v' => '5.103'
		);
		$get_params = http_build_query($request_params);
		file_get_contents('https://api.vk.com/method/messages.send?'. $get_params);

		//Возвращаем "ok" серверу Callback API
		
		echo('ok');

    }
    
    if ($message == 'кик') {
        	
        $userDel = $data->object->message->reply_message->from_id;
        $group_id = $peer_id - 2000000000;
        $request_params = array(
			//'message' => "Пользовтаель {$user_name} будет исключен из беседы",
			'chat_id'		=> $group_id,
			'member_id'		=> $userDel,
			//'access_token'	=> $token,
			'v' => '5.103'
		);
		$get_params = http_build_query($request_params);
		file_get_contents('https://api.vk.com/method/messages.removeChatUser?'. $get_params);

		//Возвращаем "ok" серверу Callback API
		
		echo('ok');

    }  
    
}













/*

//Проверяем, что находится в поле "type"
switch ($data->type) {
//Если это уведомление для подтверждения адреса...
case 'confirmation':
//...отправляем строку для подтверждения
echo $confirmation_token;
break;

//Если это уведомление о новом сообщении...
case 'message_new':

$peer_id = $data->object->message->peer_id;
$from_id = $data->object->message->from_id;
//затем с помощью users.get получаем данные об авторе
$user_info = json_decode(file_get_contents("https://api.vk.com/method/users.get?user_ids={$from_id}&access_token={$token}&v=5.0"));

//и извлекаем из ответа его имя
$user_name = $user_info->response[0]->first_name;

//С помощью messages.send отправляем ответное сообщение
$request_params = array(
'message' => "Привет, {$user_name}! Дата твоей регистрации: ",
'peer_id' => $peer_id,
'random_id'	 => $from_id,
'access_token' => $token,
'v' => '5.103'
);

$get_params = http_build_query($request_params);

file_get_contents('https://api.vk.com/method/messages.send?'. $get_params);

//Возвращаем "ok" серверу Callback API

echo('ok');

break;

}

*/
