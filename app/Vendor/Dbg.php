<?php

class Dbg {

	public $depthMax = 15;

	public $depth = 0;

	static function isDebugging() {

		return false;
//		return true;
	}

	static function trace($message = "", $limit = 2) {

		if (! Dbg::isDebugging()) {
			return;
		}

		echo "<html>";
		echo "<head><meta http-equiv='Content-Type' content='text/html; charset=utf-8' /></head>";
		echo "<body>";

		echo "<table bgcolor='FFFFFF' border='1'>";

		echo "<tr>";

		echo "<td>TRACE" . (("" == $message) ? "" : ":") . $message . "</td>";

		echo "<td><pre>";

		Dbg::echo_traces(debug_backtrace(1, $limit));

		echo "</pre></td>";

		echo "</tr>";

		echo "</table></body></html>";
	}

	static function print_pre($name, $var) {

		if (! Dbg::isDebugging()) {
			return;
		}

		echo "<html>";
		echo "<head><meta http-equiv='Content-Type' content='text/html; charset=utf-8' /></head>";
		echo "<body>";

		echo "<table bgcolor='FFFFFF' border='1'>";

		echo "<tr>";

		echo "<td colspan='2'><pre>";
		Dbg::echo_traces(debug_backtrace(1,1));
		echo "</pre></td>";

		echo "</tr>";

		echo "<tr>";

		echo "<td>" . $name . "</td>";

		echo "<td><pre>";
		print_r($var);
		echo "</pre></td>";

		echo "</tr>";

		echo "</table></body></html>";
	}

	static function print_var($name, $var, $depthMax = 15) {

		if (! Dbg::isDebugging()) {
			return;
		}

		$dbg = new Dbg();

		$dbg->depth = 0;
		$dbg->depthMax = $depthMax;

		echo "<html>";
		echo "<head><meta http-equiv='Content-Type' content='text/html; charset=utf-8' /></head>";
		echo "<body>";

		echo "<table bgcolor='FFFFFF' border='1'>";

		echo "<tr>";

		echo "<td colspan='2'><pre>";
		Dbg::echo_traces(debug_backtrace(1,1));
		echo "</pre></td>";

		echo "</tr>";


		$dbg->print_key_value($name, $var);

		echo "</table></body></html>";
	}

	static function echo_traces($traces) {

		foreach($traces as $trace) {

			if (array_key_exists('class', $trace)) {

				if (array_key_exists('object', $trace)) {

					echo "(" . get_class($trace['object']) . ")";
				}

				echo $trace['class'] . $trace['type'] . $trace['function'];

				echo "(";

				if (array_key_exists('args', $trace)) {

					$isTheFirst = true;

					foreach($trace['args'] as $arg) {

						if (! $isTheFirst) {

							echo ", ";
						}

						if (is_object($arg)) {

							echo "(" . get_class($arg) . ")";
						}
						elseif(is_array($arg)) {

							echo "(array)";
						}
						else {

							echo $arg;
						}

						$isTheFirst = false;
					}
				}

				echo ")";
			}
			else {

				echo "クラス情報なし";
			}

			echo '</br>';

			echo array_key_exists('file', $trace) ? ($trace['file'] . ":" . $trace['line'] ) : "ファイル情報なし" . '</br>';

			echo '</br>';
		}
	}

	function print_key_value($name, $var) {

		echo "<tr>";
		echo "<td valign='top'>" . $name . "</td>";
		echo "<td>";

		if (is_object($var)) {

			$this->print_array(get_object_vars($var));
		}
		else if (is_array($var)) {

			$this->print_array($var);
		}
		else if (isset($var)) {

			if ($var === "") {

				echo "<empty>";
			}
			else {

				echo $var;
			}
		}
		else {
			echo "<null>";
		}

		echo "</td>";
		echo "</tr>";
	}

	function print_array($var) {

		if ($this->depthMax <= $this->depth) {

			echo "<p><font color='red'>階層が深すぎます！</font></p>";
			return;
		}

		echo "<table bgcolor='FFFFFF' border='1'>";

		$this->depth++;

		foreach($var as $key => $value) {

			$this->print_key_value($key, $value);
		}

		$this->depth--;

		echo "</table>";
	}
}
