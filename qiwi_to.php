<?php
include "setting.php";
$stmt = $mysqli->query('SELECT * FROM `send`');
$st = $stmt->fetch_assoc();
$qiwi_to = $st['qiwi'];
$time = time() + 10 * 5;
if ($stmt = $mysqli->query('SELECT * FROM `qiwi` WHERE status = 0')) {
	while($st = $stmt->fetch_assoc()){
		$qiwi = $st['number'];
		$token = $st['token'];
						
		$api = new Qiwi($qiwi, $token);
		$getHistory = $api->getBalance();
		if($getHistory) {
			$amount = floor($getHistory['accounts'][0]['balance']['amount']);
			if($amount > 5) {
				$sendMoney = $api->sendMoneyToQiwi([
					'id' => ''.$time.'',
					'sum' => [
						'amount'   => $amount,
						'currency' => '643'
					], 
					'paymentMethod' => [
						'type' => 'Account',
						'accountId' => '643'
					],
					'comment' => 'Оплата услуг',
					'fields' => [
						'account' => '+'.$qiwi_to.''
					]
				]);			
			}			
		}
						
	}					
}