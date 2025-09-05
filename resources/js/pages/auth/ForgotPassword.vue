<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthLayout from '@/layouts/AuthLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';

defineProps<{
    status?: string;
}>();

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <AuthLayout title="Mot de passe oublié" description="Saisissez votre email pour recevoir un lien de réinitialisation">
        <Head title="Mot de passe oublié" />

        <div class="space-y-6">
            <!-- Status message -->
            <div v-if="status" class="p-4 rounded-lg bg-emerald-50 border border-emerald-200">
                <p class="text-sm font-medium text-emerald-800">{{ status }}</p>
            </div>

            <!-- Info message -->
            <div class="p-4 rounded-lg bg-blue-50 border border-blue-200">
                <p class="text-sm text-blue-800 leading-relaxed">
                    Saisissez votre adresse email ci-dessous et nous vous enverrons un lien 
                    pour réinitialiser votre mot de passe.
                </p>
            </div>

            <form method="POST" @submit.prevent="submit" class="space-y-5">
                <!-- Email field -->
                <div class="space-y-2">
                    <Label for="email" class="text-sm font-medium text-slate-700">
                        Adresse email
                    </Label>
                    <Input 
                        id="email" 
                        type="email" 
                        name="email" 
                        autocomplete="off" 
                        v-model="form.email" 
                        autofocus 
                        placeholder="jean@exemple.com"
                        class="h-11 px-4 bg-white border-slate-300 rounded-lg text-slate-900 placeholder:text-slate-400 focus:border-slate-400 focus:ring-slate-400/20 transition-colors"
                        :class="{ 'border-red-300 focus:border-red-400 focus:ring-red-400/20': form.errors.email }"
                    />
                    <InputError :message="form.errors.email" class="text-sm" />
                </div>

                <!-- Submit button -->
                <Button 
                    class="w-full h-11 bg-slate-900 hover:bg-slate-800 text-white font-medium rounded-lg transition-colors focus:ring-slate-400/20" 
                    :disabled="form.processing"
                >
                    <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin mr-2" />
                    Envoyer le lien de réinitialisation
                </Button>
            </form>

            <!-- Back to login link -->
            <div class="relative">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-slate-200"></div>
                </div>
                <div class="relative flex justify-center text-xs uppercase">
                    <span class="bg-white px-3 text-slate-500 font-medium">Ou</span>
                </div>
            </div>

            <div class="text-center">
                <TextLink 
                    :href="route('login')"
                    class="inline-flex items-center text-sm font-medium text-slate-900 hover:text-slate-700 transition-colors"
                >
                    ← Retourner à la connexion
                </TextLink>
            </div>
        </div>
    </AuthLayout>
</template>
