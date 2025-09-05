<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthLayout from '@/layouts/AuthLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';

interface Props {
    token: string;
    email: string;
}

const props = defineProps<Props>();

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('password.store'), {
        onFinish: () => {
            form.reset('password', 'password_confirmation');
        },
    });
};
</script>

<template>
    <AuthLayout title="Réinitialiser le mot de passe" description="Saisissez votre nouveau mot de passe ci-dessous">
        <Head title="Réinitialiser le mot de passe" />

        <div class="space-y-6">
            <!-- Info message -->
            <div class="p-4 rounded-lg bg-blue-50 border border-blue-200">
                <p class="text-sm text-blue-800 leading-relaxed">
                    Choisissez un nouveau mot de passe sécurisé pour votre compte.
                </p>
            </div>

            <form method="POST" @submit.prevent="submit" class="space-y-5">
                <!-- Email field (readonly) -->
                <div class="space-y-2">
                    <Label for="email" class="text-sm font-medium text-slate-700">
                        Adresse email
                    </Label>
                    <Input 
                        id="email" 
                        type="email" 
                        name="email" 
                        autocomplete="email" 
                        v-model="form.email" 
                        readonly
                        class="h-11 px-4 bg-slate-50 border-slate-200 rounded-lg text-slate-600 cursor-not-allowed"
                    />
                    <InputError :message="form.errors.email" class="text-sm" />
                </div>

                <!-- Password field -->
                <div class="space-y-2">
                    <Label for="password" class="text-sm font-medium text-slate-700">
                        Nouveau mot de passe
                    </Label>
                    <Input
                        id="password"
                        type="password"
                        name="password"
                        autocomplete="new-password"
                        v-model="form.password"
                        autofocus
                        placeholder="••••••••"
                        class="h-11 px-4 bg-white border-slate-300 rounded-lg text-slate-900 placeholder:text-slate-400 focus:border-slate-400 focus:ring-slate-400/20 transition-colors"
                        :class="{ 'border-red-300 focus:border-red-400 focus:ring-red-400/20': form.errors.password }"
                    />
                    <InputError :message="form.errors.password" class="text-sm" />
                </div>

                <!-- Password confirmation field -->
                <div class="space-y-2">
                    <Label for="password_confirmation" class="text-sm font-medium text-slate-700">
                        Confirmer le nouveau mot de passe
                    </Label>
                    <Input
                        id="password_confirmation"
                        type="password"
                        name="password_confirmation"
                        autocomplete="new-password"
                        v-model="form.password_confirmation"
                        placeholder="••••••••"
                        class="h-11 px-4 bg-white border-slate-300 rounded-lg text-slate-900 placeholder:text-slate-400 focus:border-slate-400 focus:ring-slate-400/20 transition-colors"
                        :class="{ 'border-red-300 focus:border-red-400 focus:ring-red-400/20': form.errors.password_confirmation }"
                    />
                    <InputError :message="form.errors.password_confirmation" class="text-sm" />
                </div>

                <!-- Password requirements info -->
                <div class="p-4 rounded-lg bg-slate-50 border border-slate-200">
                    <p class="text-xs text-slate-600 leading-relaxed mb-2 font-medium">
                        Votre mot de passe doit contenir :
                    </p>
                    <ul class="text-xs text-slate-600 space-y-1">
                        <li>• Au moins 8 caractères</li>
                        <li>• Une lettre majuscule et une minuscule</li>
                        <li>• Au moins un chiffre</li>
                    </ul>
                </div>

                <!-- Submit button -->
                <Button 
                    type="submit"
                    class="w-full h-11 bg-slate-900 hover:bg-slate-800 text-white font-medium rounded-lg transition-colors focus:ring-slate-400/20" 
                    :disabled="form.processing"
                >
                    <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin mr-2" />
                    Réinitialiser le mot de passe
                </Button>
            </form>
        </div>
    </AuthLayout>
</template>
