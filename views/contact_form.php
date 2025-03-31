<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>お問い合わせフォーム</title>
    <link rel="stylesheet" href="/css/contact_form.css">
</head>
<body>

<div class="contact-form">
  <h2 class="contact-form__heading content__heading">お問い合わせフォーム</h2>
  <div class="contact-form__inner">
    <form action="<?= $base_url ?>/confirm" method="post">
      <input type="hidden" name="_csrf" value="<?= $csrf_token ?>">

      <div class="contact-form__group contact-form__name-group">
        <label class="contact-form__label" for="name">
          お名前<span class="contact-form__required">※</span>
        </label>
        <div class="contact-form__name-inputs">
          <input class="contact-form__input contact-form__name-input" type="text" name="first_name" id="name"
                 value="<?= htmlspecialchars($old['first_name'] ?? '', ENT_QUOTES) ?>" placeholder="例：山田">
          <input class="contact-form__input contact-form__name-input" type="text" name="last_name"
                 value="<?= htmlspecialchars($old['last_name'] ?? '', ENT_QUOTES) ?>" placeholder="例：太郎">
        </div>
            <?php if (isset($errors['first_name'])): ?>
              <div class="error-message"><?= htmlspecialchars($errors['first_name'], ENT_QUOTES) ?></div>
            <?php endif; ?>
          <?php if (isset($errors['last_name'])): ?>
              <div class="error-message"><?= htmlspecialchars($errors['last_name'], ENT_QUOTES) ?></div>
          <?php endif; ?>
      </div>

      <div class="contact-form__group">
        <label class="contact-form__label">性別<span class="contact-form__required">※</span></label>
        <div class="contact-form__gender-inputs">
          <?php
          $gender = $old['gender'] ?? '';
          ?>
          <label class="contact-form__gender-label">
            <input class="contact-form__gender-input" name="gender" type="radio" value="1" <?= $gender == '1' ? 'checked' : '' ?>>
            <span class="contact-form__gender-text">男性</span>
          </label>
          <label class="contact-form__gender-label">
            <input class="contact-form__gender-input" name="gender" type="radio" value="2" <?= $gender == '2' ? 'checked' : '' ?>>
            <span class="contact-form__gender-text">女性</span>
          </label>
          <label class="contact-form__gender-label">
            <input class="contact-form__gender-input" name="gender" type="radio" value="3" <?= $gender == '3' ? 'checked' : '' ?>>
            <span class="contact-form__gender-text">その他</span>
          </label>
        </div>
        <?php if (isset($errors['gender'])): ?>
              <div class="error-message"><?= htmlspecialchars($errors['gender'], ENT_QUOTES) ?></div>
            <?php endif; ?>
      </div>

      <div class="contact-form__group">
        <label class="contact-form__label" for="email">メールアドレス<span class="contact-form__required">※</span></label>
        <input class="contact-form__input" type="email" name="email" id="email"
               value="<?= htmlspecialchars($old['email'] ?? '', ENT_QUOTES) ?>" placeholder="例：test@example.com">
               <?php if (isset($errors['email'])): ?>
              <div class="error-message"><?= htmlspecialchars($errors['email'], ENT_QUOTES) ?></div>
              <?php endif; ?>
      </div>

      <div class="contact-form__group">
        <label class="contact-form__label" for="tel">電話番号<span class="contact-form__required">※</span></label>
        <div class="contact-form__tel-inputs">
          <input class="contact-form__input contact-form__tel-input" type="tel" name="tel_1" id="tel"
            value="<?= htmlspecialchars($old['tel'] ?? '', ENT_QUOTES) ?>" placeholder="例： 090" maxlength="4">
          <span>-</span>
          <input class="contact-form__input contact-form__tel-input" type="tel" name="tel_2" value="<?= htmlspecialchars($old['tel'] ?? '', ENT_QUOTES) ?>" placeholder="例：1234" maxlength="4">
          <span>-</span>
          <input class="contact-form__input contact-form__tel-input" type="tel" name="tel_3" value="<?= htmlspecialchars($old['tel'] ?? '', ENT_QUOTES) ?>" placeholder="例：5678" maxlength="4">
        </div>
              <?php if (isset($errors['tel_1'])): ?>
              <div class="error-message"><?= htmlspecialchars($errors['tel_1'], ENT_QUOTES) ?></div>
              <?php endif; ?>

               <?php if (isset($errors['tel_2'])): ?>
              <div class="error-message"><?= htmlspecialchars($errors['tel_2'], ENT_QUOTES) ?></div>
              <?php endif; ?>

               <?php if (isset($errors['tel_3'])): ?>
              <div class="error-message"><?= htmlspecialchars($errors['tel_3'], ENT_QUOTES) ?></div>
              <?php endif; ?>
      </div>

      <div class="contact-form__group">
        <label class="contact-form__label" for="address">
          住所<span class="contact-form__required">※</span>
        </label>
        <input class="contact-form__input contact-form__address-input" type="text" name="address" value="<?= htmlspecialchars($old['address'] ?? '', ENT_QUOTES) ?>" placeholder="例：東京都渋谷区千駄ヶ谷1-2-3">
              <?php if (isset($errors['address'])): ?>
              <div class="error-message"><?= htmlspecialchars($errors['address'], ENT_QUOTES) ?></div>
              <?php endif; ?>
      </div>

      <div class="contact-form__group">
        <label class="contact-form__label" for="building">建物名</label>
        <input class="contact-form__input contact-form__building-input" type="text" name="building" value="<?= htmlspecialchars($old['building'] ?? '', ENT_QUOTES) ?>" placeholder="例：千駄ヶ谷マンション101">
      </div>

        <div class="contact-form__group">
          <label class="contact-form__label" for="category">
          お問い合わせの種類<span class="contact-form__required">※</span>
          </label>
          <div class="contact-form__select-inner">
            <select class="contact-form__select" name="category_id" id="category_id">
                  <option disabled selected>選択してください</option>
                      <?php foreach ($categories as $category): ?>
                        <option value="<?= htmlspecialchars($category->id, ENT_QUOTES, 'UTF-8'); ?>"
                          <?= (old('category_id') == $category->id) ? 'selected' : ''; ?>>
                          <?= htmlspecialchars($category->content, ENT_QUOTES, 'UTF-8'); ?>
                        </option>
                      <?php endforeach; ?>
              </select>
          </div>
            <?php if (isset($errors['category_id'])): ?>
                <div class="error-message"><?= htmlspecialchars($errors['category_id'], ENT_QUOTES) ?></div>
            <?php endif; ?>
        </div>

      <div class="contact-form__group">
        <label class="contact-form__label" for="detail">
          お問い合わせ内容<span class="contact-form__required">※</span>
        </label>
        <textarea class="contact-form__textarea" name="detail" id="detail" cols="30" rows="10"
          placeholder="お問い合わせ内容をご記載ください"><?= htmlspecialchars(old('detail') ?? '', ENT_QUOTES) ?></textarea>
            <?php if (isset($errors['detail'])): ?>
                <div class="error-message"><?= htmlspecialchars($errors['detail'], ENT_QUOTES) ?></div>
            <?php endif; ?>
      </div>
      <input class="btn" type="submit" value="確認画面">
    </form>
  </div>
</div>

</body>
</html>