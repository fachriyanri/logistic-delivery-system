<?php
/**
 * Form Group Component
 * 
 * @param string $label - Field label
 * @param string $name - Field name
 * @param string $type - Input type (text, email, password, select, textarea)
 * @param mixed $value - Field value
 * @param array $options - Options for select fields
 * @param string $placeholder - Placeholder text
 * @param bool $required - Whether field is required
 * @param string $help - Help text
 * @param array $validation - Validation object
 * @param string $icon - Icon class for label
 * @param string $class - Additional CSS classes
 * @param array $attributes - Additional HTML attributes
 */

$label = $label ?? '';
$name = $name ?? '';
$type = $type ?? 'text';
$value = $value ?? '';
$options = $options ?? [];
$placeholder = $placeholder ?? '';
$required = $required ?? false;
$help = $help ?? '';
$validation = $validation ?? null;
$icon = $icon ?? '';
$class = $class ?? '';
$attributes = $attributes ?? [];

$fieldId = $attributes['id'] ?? $name;
$hasError = $validation && $validation->hasError($name);
$errorMessage = $hasError ? $validation->getError($name) : '';

$inputClass = match($type) {
    'select' => 'form-select',
    'textarea' => 'form-control',
    default => 'form-control'
};

if ($hasError) {
    $inputClass .= ' is-invalid';
}

if ($class) {
    $inputClass .= " {$class}";
}

$attributeString = '';
foreach ($attributes as $key => $val) {
    if ($key !== 'id') {
        $attributeString .= " {$key}=\"" . esc($val) . "\"";
    }
}
?>

<div class="form-group mb-3">
    <?php if ($label): ?>
    <label for="<?= $fieldId ?>" class="form-label">
        <?php if ($icon): ?>
            <i class="<?= $icon ?>"></i>
        <?php endif; ?>
        <?= esc($label) ?>
        <?php if ($required): ?>
            <span class="text-danger">*</span>
        <?php endif; ?>
    </label>
    <?php endif; ?>
    
    <?php if ($type === 'select'): ?>
        <select id="<?= $fieldId ?>" 
                name="<?= $name ?>" 
                class="<?= $inputClass ?>"
                <?= $required ? 'required' : '' ?>
                <?= $attributeString ?>>
            <?php if ($placeholder): ?>
                <option value=""><?= esc($placeholder) ?></option>
            <?php endif; ?>
            <?php foreach ($options as $optValue => $optText): ?>
                <option value="<?= esc($optValue) ?>" 
                        <?= $value == $optValue ? 'selected' : '' ?>>
                    <?= esc($optText) ?>
                </option>
            <?php endforeach; ?>
        </select>
    <?php elseif ($type === 'textarea'): ?>
        <textarea id="<?= $fieldId ?>" 
                  name="<?= $name ?>" 
                  class="<?= $inputClass ?>"
                  <?= $placeholder ? 'placeholder="' . esc($placeholder) . '"' : '' ?>
                  <?= $required ? 'required' : '' ?>
                  <?= $attributeString ?>><?= esc($value) ?></textarea>
    <?php else: ?>
        <input type="<?= $type ?>" 
               id="<?= $fieldId ?>" 
               name="<?= $name ?>" 
               class="<?= $inputClass ?>"
               value="<?= esc($value) ?>"
               <?= $placeholder ? 'placeholder="' . esc($placeholder) . '"' : '' ?>
               <?= $required ? 'required' : '' ?>
               <?= $attributeString ?>>
    <?php endif; ?>
    
    <?php if ($hasError): ?>
        <div class="invalid-feedback">
            <?= esc($errorMessage) ?>
        </div>
    <?php endif; ?>
    
    <?php if ($help): ?>
        <div class="form-text">
            <?= esc($help) ?>
        </div>
    <?php endif; ?>
</div>