<?= $this->insert('fields.label') ?>
<input class="file-input" id="file-uploader" type="file" name="<?= $field->formName() ?>" accept="<?= $field->get('accept') ?>">
<label for="file-uploader" class="file-input-label">
    <span><?= $this->label('pages.files.upload-label') ?></span>
</label>
