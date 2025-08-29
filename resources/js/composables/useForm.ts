import { ref, computed, reactive } from 'vue';
import { useForm as useInertiaForm } from '@inertiajs/vue3';

export interface ValidationErrors {
    [key: string]: string[];
}

export interface FormOptions {
    resetOnSuccess?: boolean;
    preserveState?: boolean;
    preserveScroll?: boolean;
}

export function useForm<T extends Record<string, any>>(
    initialData: T,
    options: FormOptions = {}
) {
    const form = useInertiaForm(initialData);
    const isSubmitting = ref(false);
    const hasSubmitted = ref(false);

    // État de validation côté client
    const clientErrors = reactive<ValidationErrors>({});
    const isDirty = ref(false);

    // Erreurs combinées (serveur + client)
    const allErrors = computed(() => {
        const errors: ValidationErrors = { ...clientErrors };
        
        // Ajouter les erreurs serveur
        Object.keys(form.errors).forEach(key => {
            const serverError = form.errors[key];
            if (serverError) {
                if (!errors[key]) errors[key] = [];
                if (!errors[key].includes(serverError)) {
                    errors[key].push(serverError);
                }
            }
        });
        
        return errors;
    });

    const hasErrors = computed(() => {
        return Object.keys(allErrors.value).length > 0;
    });

    const isValid = computed(() => {
        return !hasErrors.value && hasSubmitted.value;
    });

    // Validation côté client
    const validateField = (field: string, rules: ValidationRule[]): string[] => {
        const value = form.data[field];
        const errors: string[] = [];

        for (const rule of rules) {
            const error = rule.validate(value, form.data);
            if (error) {
                errors.push(error);
            }
        }

        return errors;
    };

    const setFieldError = (field: string, errors: string[]) => {
        if (errors.length > 0) {
            clientErrors[field] = errors;
        } else {
            delete clientErrors[field];
        }
    };

    const clearFieldError = (field: string) => {
        delete clientErrors[field];
        delete form.errors[field];
    };

    const clearAllErrors = () => {
        Object.keys(clientErrors).forEach(key => {
            delete clientErrors[key];
        });
        form.clearErrors();
    };

    // Validation en temps réel
    const validateFieldRealTime = (field: string, rules: ValidationRule[]) => {
        const errors = validateField(field, rules);
        setFieldError(field, errors);
        return errors.length === 0;
    };

    // Gestion des données du formulaire
    const setData = (data: Partial<T>) => {
        Object.assign(form.data, data);
        isDirty.value = true;
    };

    const setField = (field: keyof T, value: any) => {
        form.data[field] = value;
        isDirty.value = true;
        clearFieldError(field as string);
    };

    const reset = () => {
        form.reset();
        clearAllErrors();
        isDirty.value = false;
        hasSubmitted.value = false;
        isSubmitting.value = false;
    };

    // Soumission du formulaire
    const submit = (
        method: 'get' | 'post' | 'put' | 'patch' | 'delete',
        url: string,
        submitOptions: FormOptions = {}
    ) => {
        const finalOptions = { ...options, ...submitOptions };
        
        hasSubmitted.value = true;
        isSubmitting.value = true;

        return form.submit(method, url, {
            preserveState: finalOptions.preserveState ?? true,
            preserveScroll: finalOptions.preserveScroll ?? true,
            onSuccess: () => {
                if (finalOptions.resetOnSuccess) {
                    reset();
                }
                isSubmitting.value = false;
            },
            onError: () => {
                isSubmitting.value = false;
            },
            onFinish: () => {
                isSubmitting.value = false;
            }
        });
    };

    const post = (url: string, submitOptions?: FormOptions) => {
        return submit('post', url, submitOptions);
    };

    const put = (url: string, submitOptions?: FormOptions) => {
        return submit('put', url, submitOptions);
    };

    const patch = (url: string, submitOptions?: FormOptions) => {
        return submit('patch', url, submitOptions);
    };

    const destroy = (url: string, submitOptions?: FormOptions) => {
        return submit('delete', url, submitOptions);
    };

    return {
        // Données du formulaire
        form,
        data: form.data,
        
        // État
        isSubmitting,
        hasSubmitted,
        isDirty,
        processing: form.processing,
        recentlySuccessful: form.recentlySuccessful,
        
        // Erreurs
        errors: allErrors,
        hasErrors,
        isValid,
        
        // Actions
        setData,
        setField,
        reset,
        clearFieldError,
        clearAllErrors,
        validateField,
        validateFieldRealTime,
        setFieldError,
        
        // Soumission
        submit,
        post,
        put,
        patch,
        destroy
    };
}

// Types pour les règles de validation
export interface ValidationRule {
    validate: (value: any, formData: any) => string | null;
}

// Règles de validation prédéfinies
export const validationRules = {
    required: (message = 'Ce champ est requis'): ValidationRule => ({
        validate: (value) => {
            if (value === null || value === undefined || value === '') {
                return message;
            }
            return null;
        }
    }),

    email: (message = 'Adresse email invalide'): ValidationRule => ({
        validate: (value) => {
            if (!value) return null;
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailRegex.test(value) ? null : message;
        }
    }),

    minLength: (min: number, message?: string): ValidationRule => ({
        validate: (value) => {
            if (!value) return null;
            const actualMessage = message || `Minimum ${min} caractères requis`;
            return value.length >= min ? null : actualMessage;
        }
    }),

    maxLength: (max: number, message?: string): ValidationRule => ({
        validate: (value) => {
            if (!value) return null;
            const actualMessage = message || `Maximum ${max} caractères autorisés`;
            return value.length <= max ? null : actualMessage;
        }
    }),

    numeric: (message = 'Doit être un nombre'): ValidationRule => ({
        validate: (value) => {
            if (!value) return null;
            return !isNaN(Number(value)) ? null : message;
        }
    }),

    min: (min: number, message?: string): ValidationRule => ({
        validate: (value) => {
            if (!value) return null;
            const actualMessage = message || `Doit être supérieur ou égal à ${min}`;
            return Number(value) >= min ? null : actualMessage;
        }
    }),

    max: (max: number, message?: string): ValidationRule => ({
        validate: (value) => {
            if (!value) return null;
            const actualMessage = message || `Doit être inférieur ou égal à ${max}`;
            return Number(value) <= max ? null : actualMessage;
        }
    }),

    date: (message = 'Date invalide'): ValidationRule => ({
        validate: (value) => {
            if (!value) return null;
            return !isNaN(Date.parse(value)) ? null : message;
        }
    }),

    afterDate: (compareField: string, message = 'Doit être postérieure à la date de début'): ValidationRule => ({
        validate: (value, formData) => {
            if (!value || !formData[compareField]) return null;
            const date1 = new Date(value);
            const date2 = new Date(formData[compareField]);
            return date1 >= date2 ? null : message;
        }
    }),

    phone: (message = 'Numéro de téléphone invalide'): ValidationRule => ({
        validate: (value) => {
            if (!value) return null;
            // Regex simple pour les numéros français
            const phoneRegex = /^(?:(?:\+|00)33|0)\s*[1-9](?:[\s.-]*\d{2}){4}$/;
            return phoneRegex.test(value) ? null : message;
        }
    })
};