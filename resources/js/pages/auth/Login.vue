<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthBase from '@/layouts/AuthLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';

defineProps<{
    status?: string;
    canResetPassword: boolean;
}>();

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <AuthBase title="Connectez-vous à votre compte" description="Saisissez votre email et votre mot de passe ci-dessous pour vous connecter">
        <Head title="Connexion" />

        <div class="space-y-6">
            <!-- Status message -->
            <div v-if="status" class="p-4 rounded-lg bg-emerald-50 border border-emerald-200">
                <p class="text-sm font-medium text-emerald-800">{{ status }}</p>
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
                        required
                        autofocus
                        :tabindex="1"
                        autocomplete="email"
                        v-model="form.email"
                        placeholder="email@exemple.com"
                        class="h-11 px-4 bg-white border-slate-300 rounded-lg text-slate-900 placeholder:text-slate-400 focus:border-slate-400 focus:ring-slate-400/20 transition-colors"
                        :class="{ 'border-red-300 focus:border-red-400 focus:ring-red-400/20': form.errors.email }"
                    />
                    <InputError :message="form.errors.email" class="text-sm" />
                </div>

                <!-- Password field -->
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <Label for="password" class="text-sm font-medium text-slate-700">
                            Mot de passe
                        </Label>
                        <TextLink 
                            v-if="canResetPassword" 
                            :href="route('password.request')" 
                            class="text-sm text-slate-600 hover:text-slate-900 transition-colors"
                            :tabindex="5"
                        >
                            Mot de passe oublié ?
                        </TextLink>
                    </div>
                    <Input
                        id="password"
                        type="password"
                        required
                        :tabindex="2"
                        autocomplete="current-password"
                        v-model="form.password"
                        placeholder="••••••••"
                        class="h-11 px-4 bg-white border-slate-300 rounded-lg text-slate-900 placeholder:text-slate-400 focus:border-slate-400 focus:ring-slate-400/20 transition-colors"
                        :class="{ 'border-red-300 focus:border-red-400 focus:ring-red-400/20': form.errors.password }"
                    />
                    <InputError :message="form.errors.password" class="text-sm" />
                </div>

                <!-- Remember me -->
                <div class="flex items-center">
                    <Label for="remember" class="flex items-center gap-3 cursor-pointer">
                        <Checkbox 
                            id="remember" 
                            v-model="form.remember" 
                            :tabindex="3"
                            class="rounded border-slate-300 text-slate-900 focus:ring-slate-400/20"
                        />
                        <span class="text-sm text-slate-700">Se souvenir de moi</span>
                    </Label>
                </div>

                <!-- Submit button -->
                <Button 
                    type="submit" 
                    class="w-full h-11 bg-slate-900 hover:bg-slate-800 text-white font-medium rounded-lg transition-colors focus:ring-slate-400/20"
                    :tabindex="4" 
                    :disabled="form.processing"
                >
                    <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin mr-2" />
                    Se connecter
                </Button>
            </form>

            <!-- Sign up link -->
            <div class="relative">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-slate-200"></div>
                </div>
                <div class="relative flex justify-center text-xs uppercase">
                    <span class="bg-white px-3 text-slate-500 font-medium">Ou</span>
                </div>
            </div>

            <div class="text-center">
                <span class="text-sm text-slate-600">Vous n'avez pas de compte ?</span>
                <TextLink 
                    :href="route('register')" 
                    :tabindex="5"
                    class="ml-1 text-sm font-medium text-slate-900 hover:text-slate-700 transition-colors"
                >
                    S'inscrire gratuitement
                </TextLink>
            </div>
        </div>
    </AuthBase>
</template>
