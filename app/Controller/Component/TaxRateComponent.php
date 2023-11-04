<?php
// adminとappに同じものを配置すべし
class TaxRateComponent extends Component{

	private $consumptionTaxHistory = array(
		array(
			'start'	 => '',
			'end'	 => '198903',
			'rate'	 => 1.0
		),
		array(
			'start'	 => '198904',
			'end'	 => '199703',
			'rate'	 => 1.03
		),
		array(
			'start'	 => '199704',
			'end'	 => '201403',
			'rate'	 => 1.05
		),
		array(
			'start'	 => '201404',
			'end'	 => '201909',
			'rate'	 => 1.08
		),
		array(
			'start'	 => '201910',
			'end'	 => '',
			'rate'	 => 1.1
		),
	);

	public function getConsumptionTaxRate($year, $month) {
		$yearMonth = sprintf('%d%02d', $year, $month);
		$rate = 1.0;
		foreach ($this->consumptionTaxHistory as $h) {
			$startIsMatch = empty($h['start']) || (!empty($h['start']) && $yearMonth >= $h['start']);
			$endIsMatch = empty($h['end']) || (!empty($h['end']) && $yearMonth <= $h['end']);
			if ($startIsMatch && $endIsMatch) {
				$rate = $h['rate'];
				break;
			}
		}
		return $rate;
	}
}