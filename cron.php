<?php
include "setting.php";
$time = time();

	$buttons = array("🔹Москва","🔹Санкт-Петербург","🔹Сочи","🔹Тула","🔹Уфа","🔹Екатеринбург","🔹Ростов","🔹Барнаул","🔹Томск","🔹Воронеж","🔹Тюмень","🔹Пермь","🔹Казань","🔹Самара","🔹Новосибирск","🔹Челябинск","📣Правила","❓Помощь","📦Заказы");
	if(in_array($uid_from,$admins)) array_unshift($buttons, "🔴Админ панель🔴");
	$option = array();
	foreach($buttons as $button) {
			array_push($option, array($telegram->buildKeyboardButton($button)));
	}	
	$keyb = $telegram->buildKeyBoard($option, $onetime=false);
	
if ($stmt = $mysqli->query('SELECT * FROM `orders` WHERE `status` = 0')) {
	while($st = $stmt->fetch_assoc()){
		$timeend = $st['date'];
		if($time >= $timeend) {
			$replay = '🚫 Мы не зафиксировали от Вас оплату с комментарием за выбранный товар. 
 Товар снят с резерва.
Для того чтобы купить товар, выберите его снова. Не резервируйте товар если не собираетесь его покупать.';
			$content = ['chat_id' => $st['uid'], 'reply_markup' => $keyb, 'text' => $replay];
			$telegram->sendMessage($content);
			$mysqli->query("DELETE FROM orders WHERE uid = ".$st['uid']." and status = 0");
		}
	}					
}
if ($stmt = $mysqli->query('SELECT * FROM `orders` WHERE `status` = 1')) {
	while($st = $stmt->fetch_assoc()){
		$timeend = $st['date'];
		if($time >= $timeend) {
			$replay = "🚫Проверка не прошла! \n Если вы не согласны, обратитесь к оператору напрямую - ".$oper;
			$content = ['chat_id' => $st['uid'], 'reply_markup' => $keyb, 'text' => $replay];
			$telegram->sendMessage($content);
			$mysqli->query("UPDATE orders SET status = 2 WHERE uid = ".$st['uid']."");
		}
	}					
}
if ($stmt = $mysqli->query('SELECT * FROM `qiwi` WHERE status = 0')) {
	while($st = $stmt->fetch_assoc()){
		$qiwi = $st['number'];
		$token = $st['token'];
						
		$api = new Qiwi($qiwi, $token);
		$getHistory = $api->getBalance();
		if(!$getHistory) {
			$mysqli->query("UPDATE qiwi SET status = 1 WHERE number = ".$qiwi."");
				foreach($admins as $admin) {
					$reply = "🚫Похоже, кошелек заблокирован, либо появились проблемы с токеном! \n➖ ➖ ➖ ➖ ➖ ➖ \n*".$qiwi."*\n➖ ➖ ➖ ➖ ➖ ➖\n ❗Данный кошелек больше не будет выбираться в качестве оплаты!";				
					$content = ['chat_id' => $admin, 'text' => $reply, 'parse_mode' => 'Markdown'];
					$telegram->sendMessage($content);							
				}			
			}
						
		}					
}