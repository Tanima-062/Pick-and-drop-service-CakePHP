<!-- このページは使われてない -->

<div id="js-content">
	<h2 class="title_blue_line"><span>予約内容再送完了</span></h2>
	<section class="plan_form">
		<div class="inner">
			<p>入力いただいたメールアドレス宛てに予約内容を再送いたしました。</p>
			<p>▼メールが届いていない方は以下をご確認ください。</p>
			<div>
			<?php if ($emailDomain == 'docomo') { ?>

				【docomoユーザーで最も多いメールが届かない原因】<br>
				<br>
				<b>・ 携帯メールで受信制限の設定をしている</b>
				<p>
					ご自身で、迷惑メール防止機能の受信拒否などを設定していない場合でも、携帯電話以外からのメールは受信しないよう、標準設定されていることがあります。<br>
					<span style="color:red;">※携帯の受信設定をご自身で変更いただかない限り、お問い合せいただいてもメールを送信することができません。<br>何卒ご了承ください。</span>
				</p>
				<br>
				<br>
				<b>【確認・対処方法】</b><br>
				① 携帯メール設定の変更<br>
				メール送信前に「」のドメイン、または「」のメールアドレスから受信できるように設定してください。<br>また、URL付きメールを受信する設定をお願いします。<br>
				<br>
				docomo公式ホームページをご参考になさってください。<br>
				→ <?php echo $this->Html->link('NTT docomo 受信／拒否設定', 'https://www.nttdocomo.co.jp/info/spam_mail/measure/domain/', array('rel' => 'nofollow', 'target' => '_blank')); ?><Br>
				<br>
				※携帯電話の機種によっては、文字数制限により受信できない場合があります。古い機種でのご利用は避けていただくことをおすすめします。<br>
				<br>
				② PCメールアドレスへ変更する<br>
				「予約番号」を控えている方は、<?php echo $this->Html->link('お客様認証ページ', '/mypages/login/'); ?>よりログイン後にPCメールアドレスに変更して予約内容を再送ください。

			<?php } elseif ($emailDomain == 'au') { ?>

				【auユーザーで最も多いメールが届かない原因】<br>
				<br>
				<b>・ 携帯メールで受信制限の設定をしている</b>
				<p>
					ご自身で、迷惑メール防止機能の受信拒否などを設定していない場合でも、携帯電話以外からのメールは受信しないよう、標準設定されていることがあります。<br>
					<span style="color:red;">※携帯の受信設定をご自身で変更いただかない限り、お問い合せいただいてもメールを送信することができません。<br>　何卒ご了承ください。</span>
				</p>
				<br>
				<b>【確認・対処方法】</b><br>
				① 携帯メール設定の変更<br>
				メール送信前に「」のドメイン、または「」のメールアドレスから受信できるように設定してください。<br>また、URL付きメールを受信する設定をお願いします。<br>
				<br>
				<br>
				au公式ホームページをご参考になさってください。<br>
				→ <?php echo $this->Html->link('【iPhone / iPad（iOS7）】特定のメールアドレスからのメールを受信したい（受信リスト設定）', 'http://csqa.kddi.com/posts/view/qid/k13121323725/', array('rel' => 'nofollow', 'target' => '_blank')); ?><br>
				→ <?php echo $this->Html->link('【Android】特定のメールアドレスからのメールを受信したい（受信リスト設定）', 'http://csqa.kddi.com/posts/view/qid/k13121723783/', array('rel' => 'nofollow', 'target' => '_blank')); ?><br>
				→ <?php echo $this->Html->link('特定のメールアドレスからのメールを受信したい（受信リスト設定）【パソコンから設定する】', 'http://csqa.kddi.com/posts/view/qid/k1412824493/', array('rel' => 'nofollow', 'target' => '_blank')); ?><br>
				→ <?php echo $this->Html->link('【auケータイ】特定のメールアドレスからのメールを受信したい（受信リスト設定）', 'http://csqa.kddi.com/posts/view/qid/k13121823787/', array('rel' => 'nofollow', 'target' => '_blank')); ?><br>
				<br>
				※携帯電話の機種によっては、文字数制限により受信できない場合があります。古い機種でのご利用は避けていただくことをおすすめします。<br>
				<br>
				② PCメールアドレスへ変更する<br>
				「予約番号」を控えている方は、<?php echo $this->Html->link('お客様認証ページ', '/mypages/login/'); ?>よりログイン後にPCメールアドレスに変更して予約内容を再送ください。

			<?php } elseif ($emailDomain == 'softbank') { ?>

				【Softbankユーザーで最も多いメールが届かない原因】<br>
				<br>
				<b>・ 携帯メールで受信制限の設定をしている</b>
				<p>
					ご自身で、迷惑メール防止機能の受信拒否などを設定していない場合でも、携帯電話以外からのメールは受信しないよう、標準設定されていることがあります。<br>
					<span style="color:red;">※携帯の受信設定をご自身で変更いただかない限り、お問い合せいただいてもメールを送信することができません。<br>　何卒ご了承ください。</span>
				</p>
				<br>
				<b>【確認・対処方法】</b><br>
				① 携帯メール設定の変更<br>
				メール送信前に「」のドメイン、または「」のメールアドレスから受信できるように設定してください。<br>また、URL付きメールを受信する設定をお願いします。<br>
				<br>
				<br>
				Softbank公式ホームページをご参考になさってください。<br>
				→ <?php echo $this->Html->link('Softbank 受信／拒否設定', 'http://www.softbank.jp/mobile/support/antispam/settings/indivisual/whiteblack/', array('rel' => 'nofollow', 'target' => '_blank')); ?><br>
				<br>
				※携帯電話の機種によっては、文字数制限により受信できない場合があります。古い機種でのご利用は避けていただくことをおすすめします。<br>
				<br>
				② PCメールアドレスへ変更する<br>
				「予約番号」を控えている方は、<?php echo $this->Html->link('お客様認証ページ', '/mypages/login/'); ?>よりログイン後にPCメールアドレスに変更して予約内容を再送ください。

			<?php } elseif ($emailDomain == 'other') { ?>

				【GmailやYahoo!メール、Hotmailなどのフリーメールユーザーで最も多いメールが届かない原因】<br>
				<br>
				<b>・迷惑メールフォルダに振り分けられている</b><br>
				<span>Yahoo!やHotmailなどのフリーメールの場合、またお客様がお使いのメールソフトの設定によっては、弊社からのメールを自動的に迷惑メールとして振り分けてしまうことがあります。</span><br>
				<br>
				【確認・対処方法】『迷惑メールフォルダ』をご確認ください。<br>
				<br>

			<?php } ?>
			</div>

			<?php echo $this->Form->create(false, array('url' => '/resend/', 'inputDefaults' => array('label' => false, 'div' => false))); ?>
			<table>
				<tbody>
					<tr>
						<th>メールアドレス</th>
						<td class="select_type_line1"><?php echo $this->Form->input('email', array('type' => 'email', 'value' => $emailAdress)); ?></td>
					</tr>
				</tbody>
			</table>
			<?php echo $this->Form->submit('予約内容を再送信する', array('class' => 'btn')); ?>
			<?php echo $this->Form->end(); ?>

		</div>
	</section>
</div>
