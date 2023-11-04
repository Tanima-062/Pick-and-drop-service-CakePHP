<?php
App::uses('AppShell', 'Console/Command');

class DeleteOldStockReservationShell extends AppShell {

	public $uses = array('CarClassReservation');

	public function deleteOldStockReservation(){
		$error = false;
		$days = 62;
		$time = strtotime("-$days days", time());
  		$date = date("Ymd", $time);

		$fields = array('CarClassReservation.id');
		$conditions = array('stock_date <=' => $date);
		$limit = 1000;
		$recursive = -1;
		$order = array('CarClassReservation.id');
		$conditions = compact('fields','conditions','limit','recursive','order');
		$stocks = $this->CarClassReservation->find('all',$conditions);
		$stock_ids = array();

		foreach($stocks as $stock){
			$stock_ids[] = $stock['CarClassReservation']['id'];
		}

		if(!empty($stock_ids)){
			echo "Delete ids : \n";
			echo implode(",", $stock_ids);
			echo "\n";
			$this->CarClassReservation->begin();
			$conditions = array('CarClassReservation.id' => $stock_ids);
			if(!$this->CarClassReservation->deleteAll($conditions)){
				$error = true;
			}
			if($error){
				$this->CarClassReservation->rollback();
				echo "Error : Rollback \n";
				return false;
			} else {
				$this->CarClassReservation->commit();
				echo "Commit \n";
			}
		}

		return count($stock_ids);
	}
	public function main() {
		$wait = 60;
		$loop = 10;
		$now = date("Y-m-d H:i:s");
		$total = 0;
		echo "Start Batch ($now) \n";
		ob_start();
		for ($x = 1; $x <= $loop; $x++) {
		    $count = $this->deleteOldStockReservation();

			if ($count === false) {
				ob_flush();
			} else {
				ob_clean();
			}

		    if($count == 0 && $count == false){
		    	break;
		    }
		    $total = $total + $count;

		    if($x < $loop){
			    echo "Wait $wait s \n";
				sleep($wait);
			}
		}
		ob_end_clean();
		echo "Deleted records : $total \n";
		$now = date("Y-m-d H:i:s");
		echo "End Batch ($now) \n";
	}
}