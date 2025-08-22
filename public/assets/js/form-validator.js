/**
 * Form Validator
 * Handles client-side form validation with real-time feedback
 */

class FormValidator {
    constructor(form, options = {}) {
        this.form = form;
        this.options = {
            ajax: false,
            realTimeValidation: true,
            showToasts: true,
            validateOnBlur: true,
            validateOnInput: false,
            ...options
        };
        
        this.rules = {};
        this.errors = {};
        
        this.init();
    }

    init() {
        this.setupValidationRules();
        this.setupEventListeners();
    }

    setupValidationRules() {
        const fields = this.form.querySelectorAll('input, select, textarea');
        
        fields.forEach(field => {
            const rules = [];
            
            // Required validation
            if (field.hasAttribute('required')) {
                rules.push({ type: 'required', message: 'This field is required' });
            }
            
            // Email validation
            if (field.type === 'email') {
                rules.push({ type: 'email', message: 'Please enter a valid email address' });
            }
            
            // Number validation
            if (field.type === 'number') {
                rules.push({ type: 'number', message: 'Please enter a valid number' });
                
                if (field.hasAttribute('min')) {
                    rules.push({ 
                        type: 'min', 
                        value: parseFloat(field.getAttribute('min')),
                        message: `Value must be at least ${field.getAttribute('min')}`
                    });
                }
                
                if (field.hasAttribute('max')) {
                    rules.push({ 
                        type: 'max', 
                        value: parseFloat(field.getAttribute('max')),
                        message: `Value must not exceed ${field.getAttribute('max')}`
                    });
                }
            }
            
            // Length validation
            if (field.hasAttribute('minlength')) {
                rules.push({ 
                    type: 'minlength', 
                    value: parseInt(field.getAttribute('minlength')),
                    message: `Must be at least ${field.getAttribute('minlength')} characters`
                });
            }
            
            if (field.hasAttribute('maxlength')) {
                rules.push({ 
                    type: 'maxlength', 
                    value: parseInt(field.getAttribute('maxlength')),
                    message: `Must not exceed ${field.getAttribute('maxlength')} characters`
                });
            }
            
            // Pattern validation
            if (field.hasAttribute('pattern')) {
                rules.push({ 
                    type: 'pattern', 
                    value: new RegExp(field.getAttribute('pattern')),
                    message: field.getAttribute('title') || 'Invalid format'
                });
            }
            
            // Custom validation rules
            const customRules = field.dataset.validate;
            if (customRules) {
                try {
                    const parsed = JSON.parse(customRules);
                    rules.push(...parsed);
                } catch (e) {
                    console.warn('Invalid validation rules for field:', field.name);
                }
            }
            
            if (rules.length > 0) {
                this.rules[field.name] = rules;
            }
        });
    }

    setupEventListeners() {
        if (this.options.realTimeValidation) {
            const fields = this.form.querySelectorAll('input, select, textarea');
            
            fields.forEach(field => {
                if (this.options.validateOnBlur) {
                    field.addEventListener('blur', () => {
                        this.validateField(field);
                    });
                }
                
                if (this.options.validateOnInput) {
                    field.addEventListener('input', () => {
                        // Debounce input validation
                        clearTimeout(field.validationTimeout);
                        field.validationTimeout = setTimeout(() => {
                            this.validateField(field);
                        }, 300);
                    });
                }
            });
        }
    }

    validate() {
        this.errors = {};
        let isValid = true;
        
        const fields = this.form.querySelectorAll('input, select, textarea');
        
        fields.forEach(field => {
            if (!this.validateField(field)) {
                isValid = false;
            }
        });
        
        return isValid;
    }

    validateField(field) {
        const fieldName = field.name;
        const fieldValue = field.value.trim();
        const rules = this.rules[fieldName] || [];
        
        // Clear previous errors for this field
        delete this.errors[fieldName];
        this.clearFieldError(field);
        
        for (const rule of rules) {
            const result = this.applyRule(fieldValue, rule, field);
            if (!result.valid) {
                this.errors[fieldName] = result.message;
                this.showFieldError(field, result.message);
                return false;
            }
        }
        
        this.showFieldSuccess(field);
        return true;
    }

    applyRule(value, rule, field) {
        switch (rule.type) {
            case 'required':
                return {
                    valid: value !== '',
                    message: rule.message
                };
                
            case 'email':
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                return {
                    valid: value === '' || emailRegex.test(value),
                    message: rule.message
                };
                
            case 'number':
                return {
                    valid: value === '' || !isNaN(parseFloat(value)),
                    message: rule.message
                };
                
            case 'min':
                const numValue = parseFloat(value);
                return {
                    valid: value === '' || (!isNaN(numValue) && numValue >= rule.value),
                    message: rule.message
                };
                
            case 'max':
                const maxValue = parseFloat(value);
                return {
                    valid: value === '' || (!isNaN(maxValue) && maxValue <= rule.value),
                    message: rule.message
                };
                
            case 'minlength':
                return {
                    valid: value === '' || value.length >= rule.value,
                    message: rule.message
                };
                
            case 'maxlength':
                return {
                    valid: value.length <= rule.value,
                    message: rule.message
                };
                
            case 'pattern':
                return {
                    valid: value === '' || rule.value.test(value),
                    message: rule.message
                };
                
            case 'custom':
                if (typeof rule.validator === 'function') {
                    return rule.validator(value, field);
                }
                return { valid: true, message: '' };
                
            default:
                return { valid: true, message: '' };
        }
    }

    showFieldError(field, message) {
        field.classList.add('is-invalid');
        field.classList.remove('is-valid');
        
        let feedback = field.parentNode.querySelector('.invalid-feedback');
        if (!feedback) {
            feedback = document.createElement('div');
            feedback.className = 'invalid-feedback';
            field.parentNode.appendChild(feedback);
        }
        
        feedback.textContent = message;
        feedback.style.display = 'block';
    }

    showFieldSuccess(field) {
        if (field.value.trim() !== '') {
            field.classList.add('is-valid');
            field.classList.remove('is-invalid');
        }
        
        const feedback = field.parentNode.querySelector('.invalid-feedback');
        if (feedback) {
            feedback.style.display = 'none';
        }
    }

    clearFieldError(field) {
        field.classList.remove('is-invalid', 'is-valid');
        
        const feedback = field.parentNode.querySelector('.invalid-feedback');
        if (feedback) {
            feedback.style.display = 'none';
        }
    }

    clearErrors() {
        this.errors = {};
        
        const fields = this.form.querySelectorAll('input, select, textarea');
        fields.forEach(field => {
            this.clearFieldError(field);
        });
    }

    async submitAjax() {
        if (!this.options.ajax) return;
        
        try {
            const formData = new FormData(this.form);
            const response = await fetch(this.form.action, {
                method: this.form.method,
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            const result = await response.json();
            
            if (response.ok && result.success) {
                this.handleAjaxSuccess(result);
            } else {
                this.handleAjaxError(result);
            }
        } catch (error) {
            console.error('Form submission error:', error);
            this.handleAjaxError({ message: 'An error occurred while submitting the form' });
        } finally {
            this.resetSubmitButton();
        }
    }

    handleAjaxSuccess(result) {
        if (this.options.showToasts && typeof showToast === 'function') {
            showToast(result.message || 'Form submitted successfully', 'success');
        }
        
        // Trigger custom success event
        this.form.dispatchEvent(new CustomEvent('formSuccess', { detail: result }));
        
        // Redirect if specified
        if (result.redirect) {
            setTimeout(() => {
                window.location.href = result.redirect;
            }, 1000);
        }
    }

    handleAjaxError(result) {
        if (result.errors) {
            // Show field-specific errors
            Object.keys(result.errors).forEach(fieldName => {
                const field = this.form.querySelector(`[name="${fieldName}"]`);
                if (field) {
                    this.showFieldError(field, result.errors[fieldName]);
                }
            });
        }
        
        if (this.options.showToasts && typeof showToast === 'function') {
            showToast(result.message || 'Please correct the errors and try again', 'error');
        }
        
        // Trigger custom error event
        this.form.dispatchEvent(new CustomEvent('formError', { detail: result }));
    }

    resetSubmitButton() {
        const submitBtn = this.form.querySelector('button[type="submit"]');
        if (submitBtn) {
            const btnText = submitBtn.querySelector('.btn-text');
            const spinner = submitBtn.querySelector('.spinner-border');
            
            submitBtn.disabled = false;
            if (btnText) btnText.textContent = submitBtn.dataset.originalText || 'Save';
            if (spinner) spinner.classList.add('d-none');
        }
    }

    // Public methods
    addRule(fieldName, rule) {
        if (!this.rules[fieldName]) {
            this.rules[fieldName] = [];
        }
        this.rules[fieldName].push(rule);
    }

    removeRule(fieldName, ruleType) {
        if (this.rules[fieldName]) {
            this.rules[fieldName] = this.rules[fieldName].filter(rule => rule.type !== ruleType);
        }
    }

    setFieldValue(fieldName, value) {
        const field = this.form.querySelector(`[name="${fieldName}"]`);
        if (field) {
            field.value = value;
            this.validateField(field);
        }
    }

    getFieldValue(fieldName) {
        const field = this.form.querySelector(`[name="${fieldName}"]`);
        return field ? field.value : null;
    }

    reset() {
        this.form.reset();
        this.clearErrors();
    }
}

// Auto-initialize forms with validation
document.addEventListener('DOMContentLoaded', function() {
    const forms = document.querySelectorAll('.modern-form');
    forms.forEach(form => {
        if (!form.dataset.validatorInitialized) {
            new FormValidator(form);
            form.dataset.validatorInitialized = 'true';
        }
    });
});

// Export for global use
window.FormValidator = FormValidator;