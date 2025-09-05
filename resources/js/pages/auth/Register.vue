<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthBase from '@/layouts/AuthLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <AuthBase title="Créer un compte" description="Saisissez vos informations ci-dessous pour créer votre compte">
        <Head title="Inscription" />

        <div class="space-y-6">
            <form method="POST" @submit.prevent="submit" class="space-y-5">
                <!-- Name field -->
                <div class="space-y-2">
                    <Label for="name" class="text-sm font-medium text-slate-700">
                        Nom complet
                    </Label>
                    <Input 
                        id="name" 
                        type="text" 
                        required 
                        autofocus 
                        :tabindex="1" 
                        autocomplete="name" 
                        v-model="form.name" 
                        placeholder="Jean Dupont"
                        class="h-11 px-4 bg-white border-slate-300 rounded-lg text-slate-900 placeholder:text-slate-400 focus:border-slate-400 focus:ring-slate-400/20 transition-colors"
                        :class="{ 'border-red-300 focus:border-red-400 focus:ring-red-400/20': form.errors.name }"
                    />
                    <InputError :message="form.errors.name" class="text-sm" />
                </div>

                <!-- Email field -->
                <div class="space-y-2">
                    <Label for="email" class="text-sm font-medium text-slate-700">
                        Adresse email
                    </Label>
                    <Input 
                        id="email" 
                        type="email" 
                        required 
                        :tabindex="2" 
                        autocomplete="email" 
                        v-model="form.email" 
                        placeholder="jean@exemple.com"
                        class="h-11 px-4 bg-white border-slate-300 rounded-lg text-slate-900 placeholder:text-slate-400 focus:border-slate-400 focus:ring-slate-400/20 transition-colors"
                        :class="{ 'border-red-300 focus:border-red-400 focus:ring-red-400/20': form.errors.email }"
                    />
                    <InputError :message="form.errors.email" class="text-sm" />
                </div>

                <!-- Password field -->
                <div class="space-y-2">
                    <Label for="password" class="text-sm font-medium text-slate-700">
                        Mot de passe
                    </Label>
                    <Input
                        id="password"
                        type="password"
                        required
                        :tabindex="3"
                        autocomplete="new-password"
                        v-model="form.password"
                        placeholder="••••••••"
                        class="h-11 px-4 bg-white border-slate-300 rounded-lg text-slate-900 placeholder:text-slate-400 focus:border-slate-400 focus:ring-slate-400/20 transition-colors"
                        :class="{ 'border-red-300 focus:border-red-400 focus:ring-red-400/20': form.errors.password }"
                    />
                    <InputError :message="form.errors.password" class="text-sm" />
                </div>

                <!-- Password confirmation field -->
                <div class="space-y-2">
                    <Label for="password_confirmation" class="text-sm font-medium text-slate-700">
                        Confirmer le mot de passe
                    </Label>
                    <Input
                        id="password_confirmation"
                        type="password"
                        required
                        :tabindex="4"
                        autocomplete="new-password"
                        v-model="form.password_confirmation"
                        placeholder="••••••••"
                        class="h-11 px-4 bg-white border-slate-300 rounded-lg text-slate-900 placeholder:text-slate-400 focus:border-slate-400 focus:ring-slate-400/20 transition-colors"
                        :class="{ 'border-red-300 focus:border-red-400 focus:ring-red-400/20': form.errors.password_confirmation }"
                    />
                    <InputError :message="form.errors.password_confirmation" class="text-sm" />
                </div>

                <!-- Terms notice -->
                <div class="p-4 rounded-lg bg-slate-50 border border-slate-200">
                    <p class="text-xs text-slate-600 leading-relaxed">
                        En créant un compte, vous acceptez nos conditions d'utilisation 
                        et notre politique de confidentialité. Nous ne vendrons jamais vos données.
                    </p>
                </div>

                <!-- Submit button -->
                <Button 
                    type="submit" 
                    class="w-full h-11 bg-slate-900 hover:bg-slate-800 text-white font-medium rounded-lg transition-colors focus:ring-slate-400/20" 
                    tabindex="5" 
                    :disabled="form.processing"
                >
                    <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin mr-2" />
                    Créer mon compte gratuitement
                </Button>
            </form>

            <!-- Login link -->
            <div class="relative">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-slate-200"></div>
                </div>
                <div class="relative flex justify-center text-xs uppercase">
                    <span class="bg-white px-3 text-slate-500 font-medium">Ou</span>
                </div>
            </div>

            <div class="text-center">
                <span class="text-sm text-slate-600">Vous avez déjà un compte ?</span>
                <TextLink 
                    :href="route('login')" 
                    :tabindex="6"
                    class="ml-1 text-sm font-medium text-slate-900 hover:text-slate-700 transition-colors"
                >
                    Se connecter
                </TextLink>
            </div>
        </div>
    </AuthBase>
</template>
