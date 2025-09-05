<template>
    <div class="mx-auto max-w-[100em]">

        <!-- Message d'erreur si événement introuvable -->
        <Card v-if="hasError" class="border border-red-200 bg-red-50 shadow-sm">
            <CardContent class="p-6 text-center">
                <Icon name="alert-circle" class="mx-auto h-12 w-12 text-red-500 mb-4" />
                <h3 class="text-lg font-semibold text-red-900 mb-2">Événement introuvable</h3>
                <p class="text-red-700 mb-4">Cet événement n'existe pas ou vous n'avez pas l'autorisation d'y accéder.</p>
                <Button variant="outline" as-child>
                    <Link :href="route('events.index')">
                        <Icon name="arrow-left" class="mr-2 h-4 w-4" />
                        Retour aux événements
                    </Link>
                </Button>
            </CardContent>
        </Card>

        <!-- Formulaire -->
        <Card v-else class="border border-gray-200 bg-white shadow-sm">
            <CardContent class="p-6">
                <!-- Skeleton du formulaire -->
                <div v-if="isLoading" class="space-y-8">
                    <!-- Section Informations de base -->
                    <div class="space-y-6">
                        <div class="border-b border-gray-100 pb-4">
                            <div class="h-6 w-48 bg-gray-200 rounded animate-pulse mb-2"></div>
                            <div class="h-4 w-64 bg-gray-200 rounded animate-pulse"></div>
                        </div>
                        <div class="grid gap-6 sm:grid-cols-2">
                            <div class="space-y-3">
                                <div class="h-4 w-32 bg-gray-200 rounded animate-pulse"></div>
                                <div class="h-11 bg-gray-200 rounded animate-pulse"></div>
                            </div>
                            <div class="space-y-3">
                                <div class="h-4 w-24 bg-gray-200 rounded animate-pulse"></div>
                                <div class="h-11 bg-gray-200 rounded animate-pulse"></div>
                            </div>
                        </div>
                        <div class="space-y-3">
                            <div class="h-4 w-20 bg-gray-200 rounded animate-pulse"></div>
                            <div class="h-24 bg-gray-200 rounded animate-pulse"></div>
                        </div>
                    </div>

                    <!-- Section Planification -->
                    <div class="space-y-6">
                        <div class="border-b border-gray-100 pb-4">
                            <div class="h-6 w-32 bg-gray-200 rounded animate-pulse mb-2"></div>
                            <div class="h-4 w-56 bg-gray-200 rounded animate-pulse"></div>
                        </div>
                        <div class="space-y-3">
                            <div class="h-4 w-28 bg-gray-200 rounded animate-pulse"></div>
                            <div class="h-11 bg-gray-200 rounded animate-pulse"></div>
                        </div>
                        <div class="grid gap-6 sm:grid-cols-2">
                            <div class="space-y-3">
                                <div class="h-4 w-32 bg-gray-200 rounded animate-pulse"></div>
                                <div class="h-11 bg-gray-200 rounded animate-pulse"></div>
                            </div>
                            <div class="space-y-3">
                                <div class="h-4 w-36 bg-gray-200 rounded animate-pulse"></div>
                                <div class="h-11 bg-gray-200 rounded animate-pulse"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex flex-col items-center justify-between gap-4 border-t border-gray-100 pt-6 sm:flex-row">
                        <div class="flex gap-2">
                            <div class="h-10 w-24 bg-gray-200 rounded animate-pulse"></div>
                            <div class="h-10 w-28 bg-gray-200 rounded animate-pulse"></div>
                        </div>
                        <div class="h-10 w-32 bg-gray-200 rounded animate-pulse"></div>
                    </div>
                </div>

                <!-- Formulaire réel -->
                <form v-else-if="event && form" @submit.prevent="handleSubmit?.()" class="space-y-8">
                    <!-- General errors -->
                    <div v-if="form.errors.general" class="rounded-lg bg-red-50 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <Icon name="alert-circle" class="h-5 w-5 text-red-400" />
                            </div>
                            <div class="ml-3">
                                <div class="text-sm text-red-700">
                                    {{ form.errors.general }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Type d'événement (en haut, non modifiable) -->
                    <div class="space-y-6">
                        <div class="border-b border-gray-100 pb-4">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">Type d'événement</h3>
                            <p class="text-sm text-gray-600">Type d'événement associé à cet événement</p>
                        </div>

                        <!-- Affichage de la carte du type sélectionné seulement -->
                        <div class="max-w-md">
                            <!-- Carte Étape (si c'est le type sélectionné) -->
                            <div v-if="form.event_type === 'step'" class="relative rounded-xl border-1 p-4 border-blue-500 bg-blue-50 shadow-sm opacity-75">

                                <!-- Indicateur de sélection -->
                                <div class="absolute top-4 right-4">
                                    <div class="h-5 w-5 rounded-full border-2 border-blue-500 bg-blue-500">
                                        <div class="absolute inset-1 rounded-full bg-white"></div>
                                    </div>
                                </div>

                                <!-- Contenu de la carte -->
                                <div class="space-y-4">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-blue-100 text-blue-600">
                                            <Icon name="flag" class="h-6 w-6" />
                                        </div>
                                        <div>
                                            <h4 class="font-semibold text-blue-900">Étape</h4>
                                            <p class="text-sm text-blue-700">Tâche ou livrable</p>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <!-- Carte Facturation (si c'est le type sélectionné) -->
                            <div v-else-if="form.event_type === 'billing'" class="relative rounded-xl border-1 p-4 border-purple-500 bg-purple-50 shadow-sm opacity-75">

                                <!-- Indicateur de sélection -->
                                <div class="absolute top-4 right-4">
                                    <div class="h-5 w-5 rounded-full border-2 border-purple-500 bg-purple-500">
                                        <div class="absolute inset-1 rounded-full bg-white"></div>
                                    </div>
                                </div>

                                <!-- Contenu de la carte -->
                                <div class="space-y-4">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-purple-100 text-purple-600">
                                            <Icon name="banknote" class="h-6 w-6" />
                                        </div>
                                        <div>
                                            <h4 class="font-semibold text-purple-900">Facturation</h4>
                                            <p class="text-sm text-purple-700">Document financier</p>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>

                        <!-- Hidden input pour envoyer la valeur -->
                        <input type="hidden" name="event_type" :value="form.event_type" />

                        <!-- Message informatif -->
                        <div class="rounded-lg bg-gray-50 p-4">
                            <div class="flex items-start gap-3">
                                <Icon name="info" class="mt-0.5 h-4 w-4 text-gray-600" />
                                <div class="text-sm text-gray-700">
                                    <p class="font-medium text-gray-900 mb-1">Le type d'événement ne peut pas être modifié après création.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section Informations générales -->
                    <div class="space-y-8">
                        <div class="border-b border-gray-100 pb-4">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">Informations générales</h3>
                            <p class="text-sm text-gray-600">Les informations essentielles de votre événement</p>
                        </div>

                        <!-- Ligne 1: Projet associé -->
                        <div class="space-y-2">
                            <Label class="text-sm font-medium text-gray-900">Projet sélectionné</Label>
                            <div class="flex items-center gap-3 rounded-lg border border-gray-200 bg-gray-50/50 p-4">
                                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-emerald-100">
                                    <Icon name="folder" class="h-5 w-5 text-emerald-600" />
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">{{ event?.project?.name }}</p>
                                    <p class="text-sm text-gray-600">{{ event?.project?.client?.name }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Ligne 2: Nom de l'événement + Catégorie -->
                        <div class="grid gap-6 lg:grid-cols-2">
                            <div class="space-y-3">
                                <Label for="name" class="text-sm font-medium text-gray-700">Nom de l'événement *</Label>
                                <div class="relative">
                                    <Icon name="file-text" class="absolute top-1/2 left-3.5 h-4 w-4 -translate-y-1/2 transform text-blue-400" />
                                    <Input
                                        id="name"
                                        v-model="form.name"
                                        @input="form.clearErrors('name')"
                                        type="text"
                                        required
                                        placeholder="ex: Réunion de lancement"
                                        class="h-12 w-full rounded-lg border border-gray-200 bg-white py-3 pr-4 pl-11 text-sm transition-all focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 hover:border-gray-300"
                                        :class="{ 'border-red-300 bg-red-50 focus:border-red-500 focus:ring-red-500/10': form.errors.name }"
                                    />
                                </div>
                                <InputError :message="form.errors.name" />
                            </div>

                            <div class="space-y-3">
                                <Label for="type" class="text-sm font-medium text-gray-700">Catégorie *</Label>
                                <div class="relative">
                                    <Icon name="tag" class="absolute top-1/2 left-3.5 h-4 w-4 -translate-y-1/2 transform text-purple-400 pointer-events-none" />
                                    <select
                                        id="type"
                                        v-model="form.type"
                                        @change="form.clearErrors('type')"
                                        required
                                        class="h-12 w-full appearance-none rounded-lg border border-gray-200 bg-white py-3 pr-10 pl-11 text-sm transition-all focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 hover:border-gray-300"
                                        :class="{ 'border-red-300 bg-red-50 focus:border-red-500 focus:ring-red-500/10': form.errors.type }"
                                    >
                                        <option value="">Sélectionnez une catégorie</option>
                                        <option value="meeting">Réunion</option>
                                        <option value="consultation">Consultation</option>
                                        <option value="planning">Planification</option>
                                        <option value="execution">Exécution</option>
                                        <option value="review">Révision</option>
                                        <option value="delivery">Livraison</option>
                                        <option value="follow_up">Suivi</option>
                                        <option value="training">Formation</option>
                                        <option value="maintenance">Maintenance</option>
                                        <option value="research">Recherche</option>
                                        <option value="other">Autre</option>
                                    </select>
                                    <Icon name="chevron-down" class="absolute top-1/2 right-3.5 h-4 w-4 -translate-y-1/2 transform text-gray-400 pointer-events-none" />
                                </div>
                                <InputError :message="form.errors.type" />
                            </div>
                        </div>

                        <!-- Ligne 3: Description -->
                        <div class="space-y-3">
                            <Label for="description" class="text-sm font-medium text-gray-700">Description</Label>
                            <div class="relative">
                                <Icon name="file-text" class="absolute top-3.5 left-3.5 h-4 w-4 text-slate-400" />
                                <textarea
                                    id="description"
                                    v-model="form.description"
                                    @input="form.clearErrors('description')"
                                    rows="4"
                                    placeholder="Décrivez l'événement en détail..."
                                    class="min-h-[120px] w-full resize-none rounded-lg border border-gray-200 bg-white py-3.5 pr-4 pl-11 text-sm transition-all placeholder:text-gray-400 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 hover:border-gray-300"
                                    :class="{ 'border-red-300 bg-red-50 focus:border-red-500 focus:ring-red-500/10': form.errors.description }"
                                ></textarea>
                            </div>
                            <InputError :message="form.errors.description" />
                        </div>
                    </div>

                    <!-- Section Planification -->
                    <div class="space-y-8">
                        <div class="border-b border-gray-100 pb-4">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">Planification</h3>
                            <p class="text-sm text-gray-600">Définissez le calendrier et le suivi de votre événement</p>
                        </div>

                        <!-- Ligne 1: Date de création + Date d'envoi/exécution prévue -->
                        <div class="grid gap-6 lg:grid-cols-2">
                            <!-- Date de création -->
                            <div class="space-y-3">
                                <Label for="created_date" class="text-sm font-medium text-gray-700">Date de création *</Label>
                                <div class="relative">
                                    <Icon name="calendar-plus" class="absolute top-1/2 left-3.5 h-4 w-4 -translate-y-1/2 transform text-slate-400" />
                                    <Input
                                        id="created_date"
                                        v-model="form.created_date"
                                        @input="form.clearErrors('created_date')"
                                        type="date"
                                        required
                                        :min="getProjectStartDateForInput()"
                                        class="h-12 w-full rounded-lg border border-gray-200 bg-white py-3 pr-4 pl-11 text-sm transition-all focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 hover:border-gray-300"
                                        :class="{ 'border-red-300 bg-red-50 focus:border-red-500 focus:ring-red-500/10': (form && form.errors && form.errors.created_date) || (hasValidation && validation && validation.createdAtValidationError) }"
                                    />
                                </div>
                                <InputError :message="form.errors.created_date" />
                                <InputError v-if="hasValidation && validation && validation.createdAtValidationError" :message="validation.createdAtValidationError" />
                            </div>

                            <!-- Date d'envoi/exécution prévue -->
                            <div class="space-y-3">
                                <Label :for="form.event_type === 'step' ? 'execution_date' : 'send_date'" class="text-sm font-medium text-gray-700">
                                    {{ form.event_type === 'step' ? 'Date d\'exécution prévue *' : 'Date d\'envoi prévue *' }}
                                </Label>
                                <div class="relative">
                                    <Icon :name="form.event_type === 'step' ? 'clock' : 'send'" class="absolute top-1/2 left-3.5 h-4 w-4 -translate-y-1/2 transform text-indigo-400" />
                                    <!-- Champ pour étape -->
                                    <Input
                                        v-if="form.event_type === 'step'"
                                        id="execution_date"
                                        v-model="form.execution_date"
                                        @input="form.clearErrors('execution_date')"
                                        type="date"
                                        required
                                        :min="getMinDateForEvent()"
                                        class="h-12 w-full rounded-lg border border-gray-200 bg-white py-3 pr-4 pl-11 text-sm transition-all focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 hover:border-gray-300"
                                        :class="{ 'border-red-300 bg-red-50 focus:border-red-500 focus:ring-red-500/10': (form && form.errors && form.errors.execution_date) || (hasValidation && validation && validation.executionDateValidationError && validation.executionDateValidationError) }"
                                    />
                                    <!-- Champ pour facturation -->
                                    <Input
                                        v-else
                                        id="send_date"
                                        v-model="form.send_date"
                                        @input="form?.clearErrors('send_date')"
                                        type="date"
                                        required
                                        :min="getMinDateForEvent()"
                                        class="h-12 w-full rounded-lg border border-gray-200 bg-white py-3 pr-4 pl-11 text-sm transition-all focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 hover:border-gray-300"
                                        :class="{ 'border-red-300 bg-red-50 focus:border-red-500 focus:ring-red-500/10': (form && form.errors && form.errors.send_date) || (hasValidation && validation && validation.sendDateValidationError && validation.sendDateValidationError) }"
                                    />
                                </div>
                                <InputError v-if="form.event_type === 'step'" :message="form.errors.execution_date" />
                                <InputError v-else :message="form.errors.send_date" />
                                <InputError v-if="hasValidation && validation && validation.executionDateValidationError && form.event_type === 'step'" :message="validation.executionDateValidationError" />
                                <InputError v-if="hasValidation && validation && validation.sendDateValidationError && form.event_type === 'billing'" :message="validation.sendDateValidationError" />
                            </div>
                        </div>

                        <!-- Ligne 2: Statut + Date d'envoi/exécution réelle (si envoyé/fait) -->
                        <div class="grid gap-6 lg:grid-cols-2">
                            <!-- Statut -->
                            <div class="space-y-3">
                                <Label for="status" class="text-sm font-medium text-gray-700">Statut *</Label>
                                <div class="relative">
                                    <Icon :name="getStatusIcon(form.status)" class="absolute top-1/2 left-3.5 h-4 w-4 -translate-y-1/2 transform" :class="getStatusIconClasses(form.status)" />
                                    <select
                                        v-if="form"
                                        id="status"
                                        v-model="form.status"
                                        @change="form.clearErrors('status')"
                                        required
                                        class="h-12 w-full appearance-none rounded-lg border border-gray-200 bg-white py-3 pr-10 pl-11 text-sm transition-all focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 hover:border-gray-300"
                                        :class="{ 'border-red-300 bg-red-50 focus:border-red-500 focus:ring-red-500/10': form.errors.status }"
                                    >
                                        <option v-if="form.event_type === 'step'" value="todo">À faire</option>
                                        <option v-if="form.event_type === 'step'" value="done">Fait</option>
                                        <option v-if="showBillingFields" value="to_send">À envoyer</option>
                                        <option v-if="showBillingFields" value="sent">Envoyé</option>
                                        <option value="cancelled">Annulé</option>
                                    </select>
                                    <Icon name="chevron-down" class="absolute top-1/2 right-3.5 h-4 w-4 -translate-y-1/2 transform text-gray-400 pointer-events-none" />
                                </div>
                                <InputError :message="form.errors.status" />
                            </div>

                            <!-- Date d'envoi/exécution réelle (visible si statut = "fait" ou "envoyé") -->
                            <Transition
                                enter-active-class="transition-all duration-300 ease-out"
                                enter-from-class="opacity-0 transform -translate-y-2 scale-95"
                                enter-to-class="opacity-100 transform translate-y-0 scale-100"
                                leave-active-class="transition-all duration-200 ease-in"
                                leave-from-class="opacity-100 transform translate-y-0 scale-100"
                                leave-to-class="opacity-0 transform -translate-y-2 scale-95"
                            >
                                <div v-if="showCompletedAtField" class="space-y-3">
                                    <Label for="completed_at" class="text-sm font-medium text-gray-700">
                                        {{ form.event_type === 'step' ? 'Date d\'exécution *' : 'Date d\'envoi *' }}
                                    </Label>
                                    <div class="relative">
                                        <Icon name="check-circle" class="absolute top-1/2 left-3.5 h-4 w-4 -translate-y-1/2 transform text-emerald-400" />
                                        <Input
                                            id="completed_at"
                                            v-model="form.completed_at"
                                            @input="form.clearErrors('completed_at')"
                                            type="date"
                                            required
                                            :min="getMinDateForEvent()"
                                            class="h-12 w-full rounded-lg border border-gray-200 bg-white py-3 pr-4 pl-11 text-sm transition-all focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 hover:border-gray-300"
                                            :class="{
                                                'border-red-300 bg-red-50 focus:border-red-500 focus:ring-red-500/10': (form && form.errors && form.errors.completed_at) || completedAtError
                                            }"
                                        />
                                    </div>
                                    <InputError :message="form.errors.completed_at" />
                                    <div v-if="completedAtError" class="text-sm text-red-600">
                                        {{ completedAtError }}
                                    </div>
                                </div>
                            </Transition>
                        </div>
                    </div>

                    <!-- Section Informations financières -->
                    <div v-if="showBillingFields" class="space-y-8">
                        <div class="border-b border-gray-100 pb-4">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">Informations financières</h3>
                            <p class="text-sm text-gray-600">Gérez les aspects financiers de cet événement</p>
                        </div>

                        <!-- Ligne 1: Montant + Échéance de paiement -->
                        <div class="grid gap-6 lg:grid-cols-2">
                            <!-- Montant -->
                            <div class="space-y-3">
                                <Label for="amount" class="text-sm font-medium text-gray-700">Montant HT (€)</Label>
                                <div class="relative">
                                    <Icon name="euro" class="absolute top-1/2 left-3.5 h-4 w-4 -translate-y-1/2 transform text-emerald-400" />
                                    <Input
                                        id="amount"
                                        v-model="form.amount"
                                        @input="form.clearErrors('amount')"
                                        type="number"
                                        step="0.01"
                                        min="0"
                                        placeholder="0.00"
                                        class="h-12 w-full rounded-lg border border-gray-200 bg-white py-3 pr-4 pl-11 text-sm transition-all focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 hover:border-gray-300"
                                        :class="{ 'border-red-300 bg-red-50 focus:border-red-500 focus:ring-red-500/10': form.errors.amount }"
                                    />
                                </div>
                                <InputError :message="form.errors.amount" />
                            </div>

                            <!-- Échéance de paiement -->
                            <div class="space-y-3">
                                <Label for="payment_due_date" class="text-sm font-medium text-gray-700">Échéance de paiement *</Label>
                                <div class="relative">
                                    <Icon name="alarm-clock" class="absolute top-1/2 left-3.5 h-4 w-4 -translate-y-1/2 transform text-amber-400" />
                                    <Input
                                        id="payment_due_date"
                                        v-model="form.payment_due_date"
                                        @input="form?.clearErrors('payment_due_date')"
                                        type="date"
                                        required
                                        :min="form.send_date || undefined"
                                        class="h-12 w-full rounded-lg border border-gray-200 bg-white py-3 pr-4 pl-11 text-sm transition-all focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 hover:border-gray-300"
                                        :class="{ 'border-red-300 bg-red-50 focus:border-red-500 focus:ring-red-500/10': (form && form.errors && form.errors.payment_due_date) || (hasValidation && validation && validation.paymentDueDateValidationError && validation.paymentDueDateValidationError) }"
                                    />
                                </div>
                                <InputError :message="form.errors.payment_due_date" />
                                <InputError v-if="hasValidation && validation && validation.paymentDueDateValidationError" :message="validation.paymentDueDateValidationError" />
                            </div>
                        </div>

                        <!-- Ligne 2: Statut du paiement + Date de paiement (si payé) -->
                        <div class="grid gap-6 lg:grid-cols-2">
                            <!-- Statut du paiement -->
                            <div class="space-y-3">
                                <Label for="payment_status" class="text-sm font-medium text-gray-700">Statut du paiement</Label>
                                <div class="relative">
                                    <Icon :name="getPaymentStatusIcon(form.payment_status)" class="absolute top-1/2 left-3.5 h-4 w-4 -translate-y-1/2 transform" :class="getPaymentStatusIconClasses(form.payment_status)" />
                                    <select
                                        v-if="form"
                                        id="payment_status"
                                        v-model="form.payment_status"
                                        @change="form.clearErrors('payment_status')"
                                        class="h-12 w-full appearance-none rounded-lg border border-gray-200 bg-white py-3 pr-10 pl-11 text-sm transition-all focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 hover:border-gray-300"
                                        :class="{ 'border-red-300 bg-red-50 focus:border-red-500 focus:ring-red-500/10': form.errors.payment_status }"
                                    >
                                        <option value="pending">À payer</option>
                                        <option value="paid">Payé</option>
                                    </select>
                                    <Icon name="chevron-down" class="absolute top-1/2 right-3.5 h-4 w-4 -translate-y-1/2 transform text-gray-400 pointer-events-none" />
                                </div>
                                <InputError :message="form.errors.payment_status" />
                            </div>

                            <!-- Date de paiement si payé -->
                            <Transition
                                enter-active-class="transition-all duration-300 ease-out"
                                enter-from-class="opacity-0 transform -translate-y-2 scale-95"
                                enter-to-class="opacity-100 transform translate-y-0 scale-100"
                                leave-active-class="transition-all duration-200 ease-in"
                                leave-from-class="opacity-100 transform translate-y-0 scale-100"
                                leave-to-class="opacity-0 transform -translate-y-2 scale-95"
                            >
                                <div v-if="showPaidAtField" class="space-y-3">
                                    <Label for="paid_at" class="text-sm font-medium text-gray-700">Date de paiement *</Label>
                                    <div class="relative">
                                        <Icon name="calendar-check" class="absolute top-1/2 left-3.5 h-4 w-4 -translate-y-1/2 transform text-emerald-400" />
                                        <Input
                                            id="paid_at"
                                            v-model="form.paid_at"
                                            @input="form?.clearErrors('paid_at')"
                                            type="date"
                                            :required="form.payment_status === 'paid'"
                                            :min="getMinDateForEvent()"
                                            class="h-12 w-full rounded-lg border border-gray-200 bg-white py-3 pr-4 pl-11 text-sm transition-all focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 hover:border-gray-300"
                                            :class="{
                                                'border-red-300 bg-red-50 focus:border-red-500 focus:ring-red-500/10': (form && form.errors && form.errors.paid_at) ||
                                                                                      (hasValidation && validation && !validation.isPaidAtValid)
                                            }"
                                        />
                                    </div>
                                    <InputError v-if="form && form.errors" :message="form.errors.paid_at" />
                                    <div v-if="hasValidation && validation && validation.paidAtValidationError && validation.paidAtValidationError"
                                         class="text-sm text-red-600">
                                        {{ validation.paidAtValidationError }}
                                    </div>
                                </div>
                            </Transition>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex flex-col gap-4 border-t border-gray-200 bg-gray-50/50 rounded-b-xl pt-8 pb-2 -mx-6 px-6 sm:flex-row sm:items-center sm:justify-between">
                        <div class="order-2 sm:order-1 flex flex-wrap gap-3">
                            <Button
                                variant="outline"
                                type="button"
                                @click="handleCancel"
                                class="h-12 px-6 text-sm font-medium transition-all hover:bg-gray-100 border-gray-300 flex-1 sm:flex-none"
                            >
                                <Icon name="x" class="mr-2 h-4 w-4" />
                                Annuler
                            </Button>
                            <Button
                                variant="outline"
                                type="button"
                                @click="handleResetForm"
                                class="h-12 px-6 text-sm font-medium transition-all hover:bg-amber-50 border-amber-300 text-amber-700 hover:text-amber-800 flex-1 sm:flex-none"
                            >
                                <Icon name="rotate-ccw" class="mr-2 h-4 w-4" />
                                Réinitialiser
                            </Button>
                            <Button
                                variant="destructive"
                                type="button"
                                @click="showDeleteModal = true"
                                :disabled="form.processing"
                                class="h-12 px-6 text-sm font-medium transition-all hover:bg-red-600 bg-red-500 text-white border-red-500 hover:border-red-600 flex-1 sm:flex-none"
                            >
                                <Icon name="trash-2" class="mr-2 h-4 w-4" />
                                Supprimer
                            </Button>
                        </div>
                        <div class="order-1 sm:order-2">
                            <Button
                                type="submit"
                                :disabled="!isFormValid"
                                class="h-12 px-8 w-full sm:w-auto bg-emerald-600 text-white font-medium transition-all hover:bg-emerald-700 hover:shadow-lg hover:shadow-emerald-600/25 focus:ring-4 focus:ring-emerald-500/20 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:shadow-none"
                            >
                                <Icon v-if="form.processing" name="loader-2" class="mr-2 h-4 w-4 animate-spin" />
                                <Icon v-else name="save" class="mr-2 h-4 w-4" />
                                {{ form.processing ? 'Mise à jour en cours...' : 'Mettre à jour' }}
                            </Button>
                        </div>
                    </div>
                </form>
            </CardContent>
        </Card>

        <!-- Modale de confirmation de suppression -->
        <Teleport to="body">
            <Transition
                enter-active-class="transition-all duration-300 ease-out"
                enter-from-class="opacity-0"
                enter-to-class="opacity-100"
                leave-active-class="transition-all duration-200 ease-in"
                leave-from-class="opacity-100"
                leave-to-class="opacity-0"
            >
                <div v-if="showDeleteModal" class="fixed inset-0 z-50 overflow-y-auto">
                    <!-- Overlay -->
                    <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="showDeleteModal = false"></div>

                    <!-- Modal container -->
                    <div class="flex min-h-full items-center justify-center p-4">
                        <Transition
                            enter-active-class="transition-all duration-300 ease-out"
                            enter-from-class="opacity-0 scale-95 translate-y-4"
                            enter-to-class="opacity-100 scale-100 translate-y-0"
                            leave-active-class="transition-all duration-200 ease-in"
                            leave-from-class="opacity-100 scale-100 translate-y-0"
                            leave-to-class="opacity-0 scale-95 translate-y-4"
                        >
                            <div v-if="showDeleteModal" class="w-full max-w-md">
                                <div class="relative transform overflow-hidden rounded-xl bg-white shadow-2xl ring-1 ring-black/5">
                                    <!-- Header avec icône -->
                                    <div class="px-6 pt-6">
                                        <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 rounded-full">
                                            <Icon name="alert-triangle" class="w-6 h-6 text-red-600" />
                                        </div>
                                    </div>

                                    <!-- Contenu -->
                                    <div class="px-6 py-4 text-center">
                                        <h3 class="text-lg font-semibold text-gray-900 mb-2">
                                            Supprimer l'événement
                                        </h3>
                                        <p class="text-md text-gray-600 mb-1">
                                            Êtes-vous sûr de vouloir supprimer l'événement
                                        </p>
                                        <p class="text-md font-medium text-gray-900 mb-4">
                                            "{{ event?.name }}" ?
                                        </p>
                                        <p class="text-md text-gray-500">
                                            Cette action est irréversible et toutes les données associées seront perdues.
                                        </p>
                                    </div>

                                    <!-- Actions -->
                                    <div class="flex gap-3 px-6 pb-6">
                                        <Button
                                            variant="outline"
                                            @click="showDeleteModal = false"
                                            :disabled="form.processing"
                                            class="flex-1 h-11"
                                        >
                                            Annuler
                                        </Button>
                                        <Button
                                            variant="destructive"
                                            @click="confirmDelete"
                                            :disabled="form.processing"
                                            class="flex-1 h-11"
                                        >
                                            <Icon v-if="form.processing" name="loader-2" class="mr-2 h-4 w-4 animate-spin" />
                                            <Icon v-else name="trash-2" class="mr-2 h-4 w-4" />
                                            Supprimer
                                        </Button>
                                    </div>
                                </div>
                            </div>
                        </Transition>
                    </div>
                </div>
            </Transition>
        </Teleport>

    </div>
</template>

<script setup lang="ts">
import { computed, ref, watch } from 'vue'
import Icon from '@/components/Icon.vue'
import InputError from '@/components/InputError.vue'
import { Button } from '@/components/ui/button'
import { Card, CardContent } from '@/components/ui/card'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Link } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import { useEventEditForm } from '@/composables/events/edit/useEventEditForm'
import type { EventEditFormData, EventEditFormValidation } from '@/types/events/edit'

interface Props {
    event: EventEditFormData | null
    isLoading: boolean
    hasError: boolean
}

const props = defineProps<Props>()


// Variables réactives - utilisation des types d'Inertia.js officiels
const form = ref<any>(null)
const validation = ref<EventEditFormValidation | null>(null)
const isFormValid = ref(false)
const showPaidAtField = ref(false)
const showCompletedAtField = ref(false)
const showDeleteModal = ref(false)
const handleSubmit = ref<(() => void) | null>(null)
const handleDelete = ref<(() => void) | null>(null)

// Puisque le type d'événement ne change jamais, on peut déterminer ces valeurs statiquement
const showBillingFields = computed(() => props.event?.event_type === 'billing')

// Créer le composable une seule fois quand les données sont disponibles
let composableInstance: ReturnType<typeof useEventEditForm> | null = null

const initializeComposable = (): void => {
    if (props.event && !props.isLoading && !props.hasError && !composableInstance) {
        composableInstance = useEventEditForm(props.event)

        // Assigner les références une seule fois
        form.value = composableInstance.form
        validation.value = composableInstance.validation
        handleSubmit.value = composableInstance.handleSubmit
        handleDelete.value = composableInstance.handleDelete

        // Watcher pour les computed du composable
        watch(composableInstance.isFormValid, (val: boolean) => {
            isFormValid.value = val
        }, { immediate: true })

        watch(composableInstance.showPaidAtField, (val: boolean) => {
            showPaidAtField.value = val
        }, { immediate: true })

        watch(composableInstance.showCompletedAtField, (val: boolean) => {
            showCompletedAtField.value = val
        }, { immediate: true })
    }
}

// Watcher pour initialiser quand les données arrivent
watch(
    () => [props.event, props.isLoading, props.hasError] as const,
    () => {
        initializeComposable()
    },
    { immediate: true }
)

const getProjectStartDateForInput = (): string => composableInstance?.getProjectStartDateForInput() ?? ''
const getMinDateForEvent = (): string => composableInstance?.getMinDateForEvent() ?? ''

// Computed pour les guards de validation null
const hasValidation = computed(() => validation.value !== null)

// Computed pour accéder facilement aux messages d'erreur de validation
const completedAtError = computed(() => {
    if (!hasValidation.value || !validation.value || !validation.value.completedAtValidationError) return ''
    return validation.value.completedAtValidationError
})

// Fonctions pour les icônes dynamiques des statuts
const getStatusIcon = (status: string): string => {
    switch (status) {
        case 'done':
        case 'sent':
            return 'check-circle'
        case 'cancelled':
            return 'x-circle'
        case 'todo':
        case 'to_send':
        default:
            return 'clock'
    }
}

const getStatusIconClasses = (status: string): string => {
    switch (status) {
        case 'done':
        case 'sent':
            return 'text-emerald-400'
        case 'cancelled':
            return 'text-red-400'
        case 'todo':
        case 'to_send':
        default:
            return 'text-orange-400'
    }
}

const getPaymentStatusIcon = (status: string): string => {
    return status === 'paid' ? 'check-circle' : 'clock'
}

const getPaymentStatusIconClasses = (status: string): string => {
    return status === 'paid' ? 'text-emerald-400' : 'text-orange-400'
}

// Handler pour annuler (retour à la page précédente)
const handleCancel = (): void => {
    window.history.back()
}

// Handler pour réinitialiser le formulaire
const handleResetForm = (): void => {
    // Réinitialiser avec les données d'origine de l'événement
    if (props.event && form.value) {
        const event = props.event
        
        // Restaurer les valeurs originales
        form.value.name = event.name || ''
        form.value.description = event.description || ''
        form.value.type = event.type || ''
        form.value.status = event.status || ''
        form.value.created_date = event.created_date ? event.created_date.split('T')[0] : ''
        
        // Champs conditionnels selon le type d'événement
        if (event.event_type === 'step') {
            form.value.execution_date = event.execution_date ? event.execution_date.split('T')[0] : ''
        } else if (event.event_type === 'billing') {
            form.value.send_date = event.send_date ? event.send_date.split('T')[0] : ''
            form.value.amount = event.amount ? String(event.amount) : ''
            form.value.payment_due_date = event.payment_due_date ? event.payment_due_date.split('T')[0] : ''
        }
        
        // Effacer les erreurs
        form.value.clearErrors()
    }
}

// Handler pour confirmer la suppression
const confirmDelete = (): void => {
    if (handleDelete.value) {
        handleDelete.value()
        showDeleteModal.value = false
    }
}

// Les handlers sont maintenant fournis par le composable
</script>
