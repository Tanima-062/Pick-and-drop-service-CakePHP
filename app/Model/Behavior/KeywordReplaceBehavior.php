<?php
class KeywordReplaceBehavior extends ModelBehavior {

	public $skipLinkCd = '';
	public $keywordList = array();
	public $keywordCount = 0;
	public $keywordReplaced = array();

	// キーワード長の比較（コールバック）
	public function compareKeywords($model, $a, $b) {
		// 降順
		return ($a['length'] >= $b['length']) ? -1 : 1;
	}

	// 文章に含まれるキーワードにリンクを付ける（再帰）
	public function replaceLinkAll(Model $model, $text, $startIndex = 0) {
		if (empty($model->keywordList)) {
			return $text;
		}
		$result = $text;

		for ($i = $startIndex; $i < $model->keywordCount; ++$i) {
			$isFirst = !($model->keywordReplaced[$i]);

			$pos = mb_strpos($text, $model->keywordList[$i]['name']);
			if ($pos !== false) {
				$model->keywordReplaced[$i] = true;

				$name = $model->keywordList[$i]['name'];
				$leftText = mb_substr($text, 0, $pos);
				$rightText = mb_substr($text, $pos + mb_strlen($name));

				if ($i < $model->keywordCount - 1) {
					if (!empty($leftText)) {
						$leftText = $this->replaceLinkAll($model, $leftText, $i + 1);
					}
				}
				if (!empty($rightText)) {
					$rightText = $this->replaceLinkAll($model, $rightText, $i);
				}

				if ($isFirst && $model->skipLinkCd !== $model->keywordList[$i]['link_cd']) {
					$middleText = '<a href="' . $model->keywordList[$i]['url'] . '">' . $name . '</a>';
				} else {
					$middleText = $name;
				}
				$result = $leftText . $middleText . $rightText;
				break;
			}
		}

		return $result;
	}
}