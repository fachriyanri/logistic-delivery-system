<?php
/**
 * Form Component
 * 
 * @param string $action - Form action URL
 * @param string $method - Form method (GET, POST)
 * @param array $fields - Form fields configuration
 * @param array $data - Form data (for editing)
 * @param array $validation - Validation object
 * @param string $submitText - Submit button text
 * @param string $cancelUrl - Cancel button URL
 * @param bool $ajax - Enable AJAX form submission
 * @param string $class - Additional CSS classes
 * @param array $attributes - Additional HTML attributes
 */

$action = $action ?? '';
$method = $method ?? 'POST';
$fields = $fields ?? [];
$data = $data ?? [];
$validation = $validation ?? null;
$submitText = $submitText ?? 'Save';
$cancelUrl = $cancelUrl ?? '';
$ajax = $ajax ?? false;
$class = $class ?? '';
$attributes = $attributes ?? [];

$formId = $attributes['id'] ?? 'form-' . uniqid();
$formClass = "modern-form";
if ($class) {
    $formClass .= " {$class}";
}

$attributeString = '';
foreach ($attributes as $key => $value) {
    if ($key !== 'id' && $key !== 'class') {
        $attributeString .= " {$key}=\"" . esc($value) . "\"";
    }
}
?>

<form id="<?= $formId ?>" 
      action="<?= $action ?>" 
      method="<?= strtoupper($method) ?>" 
      class="<?= $formClass ?>"
      <?= $ajax ? 'data-ajax="true"' : '' ?>
      <?= $attributeString ?>
      novalidate>
    
    <?= csrf_field() ?>
    
    <div class="form-container">
        <?php foreach ($fields as $fieldName => $fieldConfig): ?>
            <?php
            // Handle different field configuration formats
            if (is_string($fieldConfig)) {
                $fieldConfig = ['label' => $fieldConfig];
            }
            
            $fieldType = $fieldConfig['type'] ?? 'text';
            $fieldLabel = $fieldConfig['label'] ?? ucfirst(str_replace('_', ' ', $fieldName));
            $fieldValue = $data[$fieldName] ?? ($fieldConfig['value'] ?? '');
            $fieldRequired = $fieldConfig['required'] ?? false;
            $fieldPlaceholder = $fieldConfig['placeholder'] ?? '';
            $fieldHelp = $fieldConfig['help'] ?? '';
            $fieldIcon = $fieldConfig['icon'] ?? '';
            $fieldClass = $fieldConfig['class'] ?? '';
            $fieldAttributes = $fieldConfig['attributes'] ?? [];
            $fieldOptions = $fieldConfig['options'] ?? [];
            $fieldRows = $fieldConfig['rows'] ?? 3;
            $fieldGroup = $fieldConfig['group'] ?? '';
            $fieldWidth = $fieldConfig['width'] ?? 'col-12';
            ?>
            
            <?php if ($fieldType === 'hidden'): ?>
                <input type="hidden" name="<?= $fieldName ?>" value="<?= esc($fieldValue) ?>">
            <?php elseif ($fieldType === 'group_start'): ?>
                <div class="form-group-section">
                    <h5 class="form-group-title">
                        <?php if ($fieldIcon): ?>
                            <i class="<?= $fieldIcon ?> me-2"></i>
                        <?php endif; ?>
                        <?= esc($fieldLabel) ?>
                    </h5>
                    <div class="row">
            <?php elseif ($fieldType === 'group_end'): ?>
                    </div>
                </div>
            <?php else: ?>
                <div class="<?= $fieldWidth ?> mb-3">
                    <?= component('form_group', [
                        'name' => $fieldName,
                        'label' => $fieldLabel,
                        'type' => $fieldType,
                        'value' => $fieldValue,
                        'options' => $fieldOptions,
                        'placeholder' => $fieldPlaceholder,
                        'required' => $fieldRequired,
                        'help' => $fieldHelp,
                        'validation' => $validation,
                        'icon' => $fieldIcon,
                        'class' => $fieldClass,
                        'attributes' => array_merge($fieldAttributes, [
                            'rows' => $fieldRows
                        ])
                    ]) ?>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
    
    <!-- Form Actions -->
    <div class="form-actions mt-4 pt-3 border-top">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <?php if ($cancelUrl): ?>
                    <a href="<?= $cancelUrl ?>" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-2"></i>
                        Cancel
                    </a>
                <?php endif; ?>
            </div>
            
            <div class="d-flex gap-2">
                <button type="reset" class="btn btn-outline-secondary">
                    <i class="fas fa-undo me-2"></i>
                    Reset
                </button>
                
                <button type="submit" class="btn btn-primary" data-loading-text="Saving...">
                    <i class="fas fa-save me-2"></i>
                    <span class="btn-text"><?= esc($submitText) ?></span>
                    <span class="spinner-border spinner-border-sm d-none ms-2" role="status"></span>
                </button>
            </div>
        </div>
    </div>
</form>

<!-- Form Validation and AJAX Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('<?= $formId ?>');
    if (!form) return;
    
    // Initialize form validation
    const formValidator = new FormValidator(form, {
        ajax: <?= $ajax ? 'true' : 'false' ?>,
        realTimeValidation: true,
        showToasts: true
    });
    
    // Handle form submission
    form.addEventListener('submit', function(e) {
        if (!formValidator.validate()) {
            e.preventDefault();
            return false;
        }
        
        // Show loading state
        const submitBtn = form.querySelector('button[type="submit"]');
        if (submitBtn) {
            const btnText = submitBtn.querySelector('.btn-text');
            const spinner = submitBtn.querySelector('.spinner-border');
            
            submitBtn.disabled = true;
            if (btnText) btnText.textContent = submitBtn.dataset.loadingText || 'Saving...';
            if (spinner) spinner.classList.remove('d-none');
        }
        
        // If not AJAX, let the form submit normally
        if (!<?= $ajax ? 'true' : 'false' ?>) {
            return true;
        }
        
        // Handle AJAX submission
        e.preventDefault();
        formValidator.submitAjax();
    });
    
    // Handle reset button
    form.addEventListener('reset', function() {
        formValidator.clearErrors();
    });
});
</script>