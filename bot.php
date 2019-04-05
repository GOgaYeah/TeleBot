<?php
include "setting.php";
date_default_timezone_set("Europe/Moscow");
///////////////////////////////////////////////////////////////////////////////////////


$result = $telegram->getData();


$call = $telegram->Callback_Data();
$text = $telegram->Text();
$text0 = explode(" ",$telegram->Text())[0];
$text1 = explode(" ",$telegram->Text())[1];
$text2 = explode(" ",$telegram->Text())[2];

$chat_id = $telegram->ChatID();
$substr = substr($text, 0, 1);
$uid_from = $result['message']['from']['id'];
$first_name = $result['message']['from']['first_name'];

$option = array(array($telegram->buildKeyboardButton("✅Я оплатил товар")),array($telegram->buildKeyboardButton("🔙Отмена")));
$keyPay = $telegram->buildKeyBoard($option, $onetime=false);

$option = array(array($telegram->buildKeyboardButton("✅Смена кошельков")),array($telegram->buildKeyboardButton("🔄Кошелек для слива")),array($telegram->buildKeyboardButton("🇷🇺Баланс")),array($telegram->buildKeyboardButton("❌Блокировка")),array($telegram->buildKeyboardButton("🔙 Назад")));
$keyAdm = $telegram->buildKeyBoard($option, $onetime=false);

//************************************************************\\
	if (!is_null($text) && !is_null($chat_id)) {
		
	$buttons = array("🔹Москва","🔹Санкт-Петербург","🔹Сочи","🔹Тула","🔹Уфа","🔹Екатеринбург","🔹Красноярск","🔹Ростов","🔹Барнаул","🔹Томск","🔹Воронеж","🔹Тюмень","🔹Тольятти","🔹Ижевск","🔹Пермь","🔹Хабаровск","🔹Казань","🔹Самара","🔹Новосибирск","🔹Челябинск","🔹Нижнеудинск","🔹Усть-Илимск","📣Правила","❓Помощь","📦Заказы");
	if(in_array($uid_from,$admins)) array_unshift($buttons, "🔴Админ панель🔴");
	$option = array();
	foreach($buttons as $button) {
			array_push($option, array($telegram->buildKeyboardButton($button)));
	}	
	$keyb = $telegram->buildKeyBoard($option, $onetime=false);
		
	if ($stmt = $mysqli->query('SELECT * FROM `orders` WHERE uid = '.$uid_from.' and status = 2')) {
		$count_orders = mysqli_num_rows($stmt);
	}		
		$getID = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT * FROM `users` WHERE uid = ".$uid_from.""));
		if(!$getID['uid']) $mysqli->query("INSERT INTO users (uid,name,balance,status) VALUES(".$uid_from.",'".$first_name."',0,0)");
		if($getID['status'] != 1) {
			if ($text == '/start') {
				$uid = $result['callback_query']['from']['id'];
				
				
$reply = $first_name.' 
🔥Добро пожаловать!🔥
➖ ➖ ➖ ➖ ➖ ➖
Ваш профиль:
➖ 
🛒Вы совершили покупок: '.$count_orders.'
💳Ваш баланс: '.$getID['balance'].' руб.
📉Ваша скидка: '.$getID['discount'].' %
➖
Чтобы показать ваши заказы нажмите /order
⬇️Выберите город⬇️
➖ ➖ ➖ ➖ ➖ ➖	
		';
								
				
				$content = ['chat_id' => $chat_id, 'reply_markup' => $keyb, 'text' => $reply];
				$telegram->sendMessage($content);
				
			} elseif($text == "🔴Админ панель🔴" and in_array($uid_from,$admins)){
				$reply = '🔴Админ панель🔴';				
				$content = ['chat_id' => $chat_id, 'reply_markup' => $keyAdm, 'text' => $reply];
				$telegram->sendMessage($content);				
			} elseif($text == "🔄Кошелек для слива" and in_array($uid_from,$admins)){
				$stmt = $mysqli->query('SELECT * FROM `send`');
				$st = $stmt->fetch_assoc();
				$qiwi_to = $st['qiwi'];
				
				$reply = "💶 Сейчас используется: \n 🔻🔻🔻🔻🔻🔻🔻🔻🔻 \n               *".$qiwi_to."* \n 🔺🔺🔺🔺🔺🔺🔺🔺🔺 \n➕ Для замены кошелька введите запрос по примеру: \n ➖ ➖ ➖ ➖ ➖ ➖ \n */sliv 79229221122* \n ➖ ➖ ➖ ➖ ➖ ➖ \n ";					
				$content = ['chat_id' => $chat_id, 'reply_markup' => $keyAdm, 'text' => $reply, 'parse_mode' => 'Markdown'];
				$telegram->sendMessage($content);				
			} elseif($text0 == "/sliv"){
				
				$mysqli->query("UPDATE send SET qiwi = ".$text1." WHERE id = 1");
				$reply = "Установлен кошелек для слива: ".$text1;
				$content = ['chat_id' => $chat_id, 'reply_markup' => $keyAdm, 'text' => $reply];
				$telegram->sendMessage($content);
								
			} elseif($text == "✅Смена кошельков" and in_array($uid_from,$admins)){
				$option = array();
				if ($stmt = $mysqli->query('SELECT * FROM `qiwi`')) {
					while($st = $stmt->fetch_assoc()){
						array_push($option, array($telegram->buildInlineKeyBoardButton('Удалить - '.$st['number'], $url='', $callback_data = '/del '.$st['number'])));
					}
				}					
				$keyS = $telegram->buildInlineKeyBoard($option);
				
				$reply = "❗ Если вы хотите удалить какой-либо номер - то нажмите на него. \n ➕ Если хотите добавить, то введите его по примеру: \n ➖ ➖ ➖ ➖ ➖ ➖ \n */add 79091303911 ТОКЕН* \n ➖ ➖ ➖ ➖ ➖ ➖";				
				$content = ['chat_id' => $chat_id, 'reply_markup' => $keyS, 'text' => $reply, 'parse_mode' => 'Markdown'];
				$telegram->sendMessage($content);				
			} elseif($text0 == "/del"){
				
				$reply = 'Удаление: '.$text1;
				$content = ['chat_id' => $chat_id, 'reply_markup' => $keyAdm, 'text' => $reply];
				$telegram->sendMessage($content);
				
				$insert_row = $mysqli->query("DELETE FROM qiwi WHERE number = ".$text1."");
				
				$reply = 'Номер удален!';
				$content = ['chat_id' => $chat_id, 'reply_markup' => $keyAdm, 'text' => $reply];
				$telegram->sendMessage($content);				
			} elseif($text0 == "/add"){
				
				$reply = 'Проверка кошелька: '.$text1.' с токеном: '.$text2;
				$content = ['chat_id' => $chat_id, 'reply_markup' => $keyAdm, 'text' => $reply];
				$telegram->sendMessage($content);
				
				$reply = '';
				
				$api = new Qiwi($text1, $text2);
				$getHistory = $api->getBalance();
				if(!$getHistory) $reply .= "Ошибка добавления номера!";
				else {
					$mysqli->query("INSERT INTO qiwi (number,token,status) VALUES(".$text1.",'".$text2."',0)");
					$reply .= "Номер успешно добавлен";	
				}
				
				$content = ['chat_id' => $chat_id, 'reply_markup' => $keyAdm, 'text' => $reply];
				$telegram->sendMessage($content);				
			} elseif($text == "🇷🇺Баланс" and in_array($uid_from,$admins)){
				$reply = '';
				if ($stmt = $mysqli->query('SELECT * FROM `qiwi`')) {
					$count = mysqli_num_rows($stmt);
					if($count !=0 ) {
						while($st = $stmt->fetch_assoc()){
							$qiwi = $st['number'];
							$token = $st['token'];
							
							$api = new Qiwi($qiwi, $token);
							$getHistory = $api->getBalance();
							if(!$getHistory) $reply .= $qiwi." -> Ошибка \n";
							else $reply .= $qiwi." -> ".$getHistory['accounts'][0]['balance']['amount']." \n";
							
						}
					} else {
						$reply .= "Кошельков нет!";
					}					
				}				
				$content = ['chat_id' => $chat_id, 'reply_markup' => $keyAdm, 'text' => $reply];
				$telegram->sendMessage($content);				
			} elseif($text == "❌Блокировка" and in_array($uid_from,$admins)){
				$reply = '';
				if ($stmt = $mysqli->query('SELECT * FROM `qiwi`')) {
					$count = mysqli_num_rows($stmt);
					if($count !=0 ) {
						while($st = $stmt->fetch_assoc()){
							$qiwi = $st['number'];
							$token = $st['token'];
							
							$api = new Qiwi($qiwi, $token);
							$getHistory = $api->getBalance();
							if(!$getHistory) $reply .= $qiwi." -> Ошибка \n";
							else $reply .= $qiwi." -> Работает \n";
							
						}
					} else {
						$reply .= "Кошельков нет!";
					}					
				}				
				$content = ['chat_id' => $chat_id, 'reply_markup' => $keyAdm, 'text' => $reply];
				$telegram->sendMessage($content);								
			} elseif($text == "❓Помощь"){
				$reply = '➖ ➖ ➖ ➖ ➖ ➖
❓ ПОМОЩЬ ❓
➖ ➖ ➖ ➖ ➖ ➖
Инструкция:

1.Сделайте выбор необходимого города нажав на кнопку с его названием. 

2.После выбора города отобразиться список товаров в наличии. Наименование состоит из названия вещества, его веса, района/метро в котором располагается клад с этим веществом и цена.

3.Чтобы купить заинтересовавший товар необходимо нажать на него в списке.(Не выбирайте товар, если не собираетесь его покупать.) 

4.После выбора товара Вы автоматически получите реквизиты для оплаты. Обратите внимание, при выдаче реквизитов товар резервируется на 30 минут. Реквизиты действительны только на момент резерва товара, Вам необходимо его оплатить за 30 минут с момента получения реквизитов. Для удобства оплаты в реквизитах есть ссылка ОПЛАТИТЬ для перехода к прямой оплате товара.

5.После выбора Вы получите рекивизиты для оплаты.

6.Оплата принимается только на номер кошелька QIWI(мобильный номер) указанный в реквизитах с обязательным указанием комментария из реквизитов. Платежи обрабатываются в автоматическом режиме, платёж без комментария зачислен не будет! Комментарий к платежу обязателен! Без него платёж не обработается!

7.Платить можно через терминалы оплаты, с карты итд., главное чтобы был комментарий! Сумма должна быть равна или больше суммы заказа, так же можно платить частями, но каждый платёж должен быть с одинаковым комментарием. Разница свыше стоимости товара зачисляется на ваш виртуальный баланс.

8.После совершения платежа нажмите кнопку Я оплатил товар. Платежи проверяются раз в 3-5 минут, после того, как ваши деньги с правильным комментарием поступят на реквизиты Вы получите адрес, фото и подробное описание расположения клада. 

Все ваши заказы и баланс можно посмотреть нажав /order или выбрав в меню ЗАКАЗЫ';				
				$content = ['chat_id' => $chat_id, 'reply_markup' => $keyb, 'text' => $reply];
				$telegram->sendMessage($content);				
			} elseif($text == "📦Заказы" || $text == "/order"){
				$reply = "🛒Вы совершили покупок: ".$count_orders."\n";
				$reply .= "➖ ➖ ➖ ➖ ➖ ➖➖ ➖ ➖\n";
				if ($stmt = $mysqli->query('SELECT * FROM `orders` WHERE uid = '.$uid_from.' and status = 2')) {
					while($st = $stmt->fetch_assoc()){
						$reply .= "🔥".$st['text']."🔥\n";
					}
				}								
				$content = ['chat_id' => $chat_id, 'reply_markup' => $keyb, 'text' => $reply];
				$telegram->sendMessage($content);				
			} elseif($text == "📣Правила"){
				$reply = '➖ ➖ ➖ ➖ ➖ ➖
📣 ПРАВИЛА 📣
➖ ➖ ➖ ➖ ➖ ➖
1. Клады делаются на магнитах в городской черте, рядом с метро. 
Закапываются в случае если делаются в лесу или парке.
2. Персональные, комбинированные и заказы на опт, исполняются в течении 48ч после оплаты.
3. Мы не несем ответственности за адреса переданные третьим лицам.
4. Убедительно просим вас быть тактичными и адекватными, при решении проблем,
это однозначно повлияет на положительную атмосферу в процессе решения проблемы, 
и очевидно на ее результат. Клиентам с хамским поведением автоматически отказывается в обслуживании.
5. Более 1 перезаклада не выдается.
6. У нас нет и не будет бесплатных проб , а если пробы все таки будут, то они будут выставлены в виде торговых позиций по заниженной цене. 
➖ ➖ ➖ ➖ ➖ ➖
🔦ПРИ НЕНАХОДЕ🔦
➖ ➖ ➖ ➖ ➖ ➖
1. Если у вас какая-либо  проблема с нахождением клада, то обязательно сделайте фото местности
со стороны и место где должен был располагаться клад. 
(если не сделали - придется ехать еще раз).
2. Если клад в парке или лесу то попробуйте покопать поглубже и вокруг в радиусе 15см, 
пересмотрите выкопанную земли, возможны вы не заметили клад, откинув его вместе с землей и листвой.
3. Напишите оператору поддержки через telegram '.$oper.'
4. Одним сообщением сразу опишите ваши действия на месте клада, или что вам не понятно.
Приложите фотографии!
5. Дождитесь ответа оператора и курьера 
(первый ответ может занять максимум сутки, но обычно ответ приходит в течении первых часов или
даже минут в зависимости от времени суток).
6. Если повторные поиски не помогли, тогда администратором будет рассматриваться вопрос о перезакладе.
7. Перезаклад не выдается просто так, просим нас понять, так как в этой сфере слишком много халявщиков. Каждая ситуация подробно анализируется, к каждой ситуации персональный подход с выделенным на ее решение менеджером. На решение проблемной ситуации обычно уходит от суток до недели, в зависимости от сложности ситуации и того насколько оперативно клиент отвечает на наши вопросы и выполняет требования. 
Будьте, пожалуйста, терпеливы.';				
				$content = ['chat_id' => $chat_id, 'reply_markup' => $keyb, 'text' => $reply];
				$telegram->sendMessage($content);				
			} elseif($text == "✅Я оплатил товар"){
					$reply = '➖ ➖ ➖ ➖ ➖ ➖
⚙️ Проверка платежа ⚙️
➖ ➖ ➖ ➖ ➖ ➖';				
				$content = ['chat_id' => $chat_id, 'reply_markup' => $keyb, 'text' => $reply];
				$telegram->sendMessage($content);	
				
				$getOrder = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT * FROM `orders` WHERE uid = ".$uid_from." and status = 0"));
				$getQiwi = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT * FROM `qiwi` WHERE number = ".$getOrder['qiwi'].""));

				if($getOrder['qiwi']) {
					$qiwi = new Qiwi($getOrder['qiwi'], $getQiwi['token']);
					$date = date("Y-m-d", mktime(0, 0, 0, date('m'), date('d') + 1, date('Y')));
							
					$getHistory = $qiwi->getPaymentsHistory([
						'startDate' => ''.date("Y-m-d").'T00:00:00+03:00',
						'endDate' => ''.$date.'T00:00:00+03:00',
						'rows' => '10'
					]);


					foreach ( $getHistory["data"] as $value ) {
						$comments[] = $value['comment'];
						$amount[] = $value['sum']['amount'];
					}			
					if (in_array($getOrder['code'], $comments) and in_array($getOrder['summa'], $amount)) {
						
						$mysqli->query("UPDATE orders SET status = 1, date = ".date(strtotime("+1 min"))." WHERE uid = ".$uid_from.", status = 0");
						
						foreach($admins as $admin) {
							$reply = "На кошелек: ".$getOrder['qiwi']." поступила оплата в сумме ".$getOrder['summa']." с кодом ".$getOrder['code'];				
							$content = ['chat_id' => $admin, 'text' => $reply];
							$telegram->sendMessage($content);							
						}
						
						$reply = " ✅Деньги поступили в обработку!✅ \n ⚙️Проверка примечания платежа⚙️ \n";				
						$content = ['chat_id' => $chat_id, 'reply_markup' => $keyb, 'text' => $reply];
						$telegram->sendMessage($content);					
					
					} else {
						$reply = '➖ ➖ ➖ ➖ ➖ ➖
❗️ Нет оплаты ❗️
➖ ➖ ➖ ➖ ➖ ➖
⏳Ожидание платежа⏳
➖ ➖ ➖ ➖ ➖ ➖
Обновление платежей происходит раз в 3-5 минут.';
						$content = ['chat_id' => $chat_id, 'reply_markup' => $keyPay, 'text' => $reply];
						$telegram->sendMessage($content);	
					}
					
				} else {
					$reply = "🚫Ошибка проверки ордера.
					Скорее всего закончилось время оплаты и ордер был снят с брони!";
					$content = ['chat_id' => $chat_id, 'reply_markup' => $keyb, 'text' => $reply];
					$telegram->sendMessage($content);					
				}				
			} elseif($text == "🔙Отмена"){
				if ($stmt = $mysqli->query('SELECT * FROM `orders` WHERE uid = '.$uid_from.'')) {
					if (mysqli_num_rows($stmt) != 0){
						while($st = $stmt->fetch_assoc()){
							$insert_row = $mysqli->query("DELETE FROM orders WHERE uid = ".$uid_from." and status = 0");
						}
					}
				}				
$reply = $first_name.' 
🔥Добро пожаловать!🔥
➖ ➖ ➖ ➖ ➖ ➖
Начиная с 5 покупки скидка будет 3%
Начиная с каждой 10 покупки скидка 5%
➖ ➖ ➖ ➖ ➖ ➖
Ваш профиль:
➖ 
🛒Вы совершили покупок: '.$count_orders.'
💳Ваш баланс: '.$getID['balance'].' руб.
📉Ваша скидка: '.$getID['discount'].' %
➖
Чтобы показать ваши заказынажмите /order
⬇️Выберите город⬇️
➖ ➖ ➖ ➖ ➖ ➖	
		';				
				$content = ['chat_id' => $chat_id, 'reply_markup' => $keyb, 'text' => $reply];
				$telegram->sendMessage($content);				
			} elseif($items[$text]){
				$reply = '⬇️Выберите товар и район⬇️';	
				
				$option = array();
				foreach($items[$text] as $item) {
						array_push($option, array($telegram->buildKeyboardButton($item)));
				}
				array_push($option, array($telegram->buildKeyboardButton("🔙 Назад")));
				$keyb = $telegram->buildKeyBoard($option, $onetime=false);
				
				$content = ['chat_id' => $chat_id, 'reply_markup' => $keyb, 'text' => $reply];
				$telegram->sendMessage($content);				
			} elseif(strpos($text, "[") == 4){
				$name = explode("]",explode("[",$text)[1])[0];
				$summa = explode("руб", explode("- ", $text)[1])[0];
				$random = rand(12345,54321);
				$mysqli->query("INSERT INTO orders (uid,qiwi,code,summa,status,text,date) VALUES(".$uid_from.",".$qiwi.",".$random.",".$summa.",0,'".$name."',".date(strtotime("+30 min")).")");
				$reply = 'Вы приобретаете: 

👉«*'.$name.'*»
➖➖➖➖➖➖➖➖➖➖
Вы зарезервировали товар 
на 30 минут⌛️ до *'.date('H:i:s', strtotime("+30 min")).' (по МСК)*
Чтобы получить координаты/фото 
товара - Совершите платёж на QIWI.
➖➖➖➖➖➖➖➖➖➖
🏷QIWI кошелек: *'.$qiwi.'*
💵Сумма: *'.$summa.' рублей*
💬Комментарий к платежу: *'.$random.'*
💸[>>>ПЕРЕЙТИ К ОПЛАТЕ<<<](http://w.qiwi.com/payment/form/99?amountFraction=0&extra%5B%27account%27%5D='.$qiwi.'&extra%5B%27comment%27%5D='.$random.'&amountInteger='.$summa.'&blocked[0]=account&blocked[1]=comment)💸
➖➖➖➖➖➖➖➖➖➖
_Сумма платежа должна быть 
равна '.$summa.' или больше.
Разрешается оплата частями с указанием 
вашего комментария к платежу: '.$random.' 
Платежи обрабатывает робот каждые 3-5 минут._
*БЕЗ КОММЕНТАРИЯ ДЕНЬГИ НЕ ЗАЧИСЛЯЮТСЯ*';	
				
				
				$content = ['chat_id' => $chat_id, 'reply_markup' => $keyPay, 'text' => $reply, 'parse_mode' => 'Markdown'];
				$telegram->sendMessage($content);				
			} else {
				if ($stmt = $mysqli->query('SELECT * FROM `orders` WHERE uid = '.$uid_from.'')) {
					if (mysqli_num_rows($stmt) != 0){
						while($st = $stmt->fetch_assoc()){
							$insert_row = $mysqli->query("DELETE FROM orders WHERE uid = ".$uid_from." and status = 0");
						}
					}
				}					
$reply = $first_name.' 
🔥Добро пожаловать!🔥
➖ ➖ ➖ ➖ ➖ ➖
Ваш профиль:
➖ 
🛒Вы совершили покупок: '.$count_orders.'
💳Ваш баланс: '.$getID['balance'].' руб.
📉Ваша скидка: '.$getID['discount'].' %
➖
Чтобы показать ваши заказы нажмите /order
⬇️Выберите город⬇️
➖ ➖ ➖ ➖ ➖ ➖	
		';
								
				
				$content = ['chat_id' => $chat_id, 'reply_markup' => $keyb, 'text' => $reply];
				$telegram->sendMessage($content);				
			}
		} else {
			$reply = 'Бан';
								
				
			$content = ['chat_id' => $chat_id, 'text' => $reply];
			$telegram->sendMessage($content);			
		}
	}	

fclose($fp);

?>