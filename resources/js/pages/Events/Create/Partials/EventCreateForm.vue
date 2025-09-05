<template>
    <div class="mx-auto max-w-[100em]">
        <div class="grid gap-8 form:grid-cols-12">
            <!-- Formulaire principal -->
            <Card v-if="!hasError" class="border border-gray-200 bg-white shadow-sm form:col-span-8">
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
                        </div>
                        <div class="h-10 w-32 bg-gray-200 rounded animate-pulse"></div>
                    </div>
                </div>

                <!-- Formulaire réel -->
                <div v-else-if="projects && form">
                    <form @submit.prevent="onSubmit" class="space-y-8">
                    <!-- General errors -->
                    <div v-if="form.errors.general" class="rounded-xl bg-red-50 border border-red-200 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <Icon name="alert-circle" class="h-5 w-5 text-red-400" />
                            </div>
                            <div class="ml-3">
                                <div class="text-sm text-red-700 font-medium">
                                    {{ form.errors.general }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sélection du type d'événement -->
                    <div class="space-y-4">
                        <EventTypeCards
                            v-model="form.event_type"
                            @update:model-value="form.clearErrors('event_type')"
                        />
                        <InputError :message="form.errors.event_type" />
                    </div>

                    <!-- Section Informations générales -->
                    <div class="space-y-8">
                        <div class="space-y-1">
                            <h3 class="text-xl font-semibold text-gray-900 tracking-tight">Informations générales</h3>
                            <p class="text-sm text-gray-600">Les informations essentielles de votre événement</p>
                        </div>

                        <div class="space-y-6">
                            <!-- Sélecteur de projet (si pas de projet présélectionné) -->
                            <div v-if="showProjectSelector" class="space-y-2">
                                <Label for="project_id" class="text-sm font-medium text-gray-900">Projet *</Label>
                                <ProjectSelector
                                    :projects="projects"
                                    v-model="form.project_id"
                                    @change="handleProjectChange"
                                    placeholder="Rechercher un projet... (ex: nom client ou nom projet)"
                                    required
                                    :has-error="!!(form.errors.project_id || (hasValidation && validation && validation.projectIdValidationError))"
                                />
                                <InputError :message="form.errors.project_id" />
                                <InputError v-if="hasValidation && validation && validation.projectIdValidationError" :message="validation.projectIdValidationError" />
                                <p class="text-xs text-gray-500">Tapez pour rechercher par nom de client ou nom de projet</p>
                            </div>

                            <!-- Affichage du projet présélectionné -->
                            <div v-else-if="currentProject" class="space-y-2">
                                <Label class="text-sm font-medium text-gray-900">Projet sélectionné</Label>
                                <div class="flex items-center gap-3 rounded-lg border border-gray-200 bg-gray-50/50 p-4">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-emerald-100">
                                        <Icon name="folder" class="h-5 w-5 text-emerald-600" />
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900">{{ currentProject.name }}</p>
                                        <p class="text-sm text-gray-600">{{ currentProject.client.name }}</p>
                                    </div>
                                </div>
                                <input type="hidden" name="project_id" :value="form.project_id" />
                            </div>

                            <!-- Nom et Catégorie en ligne sur desktop -->
                            <div class="grid gap-6 lg:grid-cols-2">
                                <div class="space-y-2">
                                    <Label for="name" class="text-sm font-medium text-gray-900">Nom de l'événement *</Label>
                                    <div class="relative">
                                        <Icon name="calendar" class="absolute top-1/2 left-3.5 h-4 w-4 -translate-y-1/2 transform text-emerald-400" />
                                        <Input
                                            id="name"
                                            v-model="form.name"
                                            @input="form.clearErrors('name')"
                                            @blur="markNameAsTouched?.()"
                                            type="text"
                                            required
                                            placeholder="ex: Réunion de lancement"
                                            class="h-12 border-gray-200 pl-11 pr-4 transition-all focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 hover:border-gray-300 rounded-lg"
                                            :class="{ 'border-red-300 bg-red-50 focus:border-red-500 focus:ring-red-500/10': form.errors.name || (hasValidation && validation && validation.nameValidationError) }"
                                        />
                                    </div>
                                    <InputError :message="form.errors.name" />
                                    <InputError v-if="hasValidation && validation && validation.nameValidationError" :message="validation.nameValidationError" />
                                </div>

                                <div class="space-y-2">
                                    <Label for="type" class="text-sm font-medium text-gray-900">Catégorie *</Label>
                                    <div class="relative">
                                        <Icon name="tag" class="absolute top-1/2 left-3.5 h-4 w-4 -translate-y-1/2 transform text-purple-400 pointer-events-none" />
                                        <select
                                            id="type"
                                            v-model="form.type"
                                            @change="form.clearErrors('type')"
                                            @blur="markTypeAsTouched?.()"
                                            required
                                            class="h-12 w-full appearance-none rounded-lg border border-gray-200 bg-white py-2 pr-10 pl-11 text-sm transition-all focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 hover:border-gray-300"
                                            :class="{ 'border-red-300 bg-red-50 focus:border-red-500 focus:ring-red-500/10': form.errors.type || (hasValidation && validation && validation.typeValidationError) }"
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
                                    <InputError v-if="hasValidation && validation && validation.typeValidationError" :message="validation.typeValidationError" />
                                </div>
                            </div>

                            <!-- Description -->
                            <div class="space-y-2">
                                <Label for="description" class="text-sm font-medium text-gray-900">Description</Label>
                                <div class="relative">
                                    <Icon name="file-text" class="absolute top-3.5 left-3.5 h-4 w-4 text-indigo-400" />
                                    <textarea
                                        id="description"
                                        v-model="form.description"
                                        @input="form.clearErrors('description')"
                                        rows="4"
                                        placeholder="Décrivez l'événement en détail..."
                                        class="min-h-[100px] w-full resize-none rounded-lg border border-gray-200 bg-white py-3.5 pr-4 pl-11 text-sm placeholder:text-gray-400 transition-all focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 hover:border-gray-300"
                                        :class="{ 'border-red-300 bg-red-50 focus:border-red-500 focus:ring-red-500/10': form.errors.description }"
                                    />
                                </div>
                                <InputError :message="form.errors.description" />
                                <p class="text-xs text-gray-500">Ajoutez des détails sur les objectifs, participants ou éléments importants</p>
                            </div>
                        </div>
                    </div>

                    <!-- Section Planification -->
                    <div class="space-y-8">
                        <div class="space-y-1">
                            <h3 class="text-xl font-semibold text-gray-900 tracking-tight">Planification</h3>
                            <p class="text-sm text-gray-600">Définissez les dates et le statut de votre événement</p>
                        </div>

                        <div class="space-y-6">
                            <!-- Grille responsive pour les dates -->
                            <div class="grid gap-6 sm:grid-cols-2">
                                <!-- Date de création -->
                                <div class="space-y-2">
                                    <Label for="created_date" class="text-sm font-medium text-gray-900">Date de création *</Label>
                                    <div class="relative">
                                        <Icon name="calendar-plus" class="absolute top-1/2 left-3.5 h-4 w-4 -translate-y-1/2 transform text-slate-400" />
                                        <Input
                                            id="created_date"
                                            v-model="form.created_date"
                                            @input="form.clearErrors('created_date')"
                                            type="date"
                                            required
                                            :min="getProjectStartDateForInput()"
                                            class="h-12 border-gray-200 pl-11 pr-4 transition-all focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 hover:border-gray-300 rounded-lg"
                                            :class="{ 'border-red-300 bg-red-50 focus:border-red-500 focus:ring-red-500/10': (form && form.errors && form.errors.created_date) || (hasValidation && validation && validation.createdAtValidationError) }"
                                        />
                                    </div>
                                    <InputError :message="form.errors.created_date" />
                                    <InputError v-if="hasValidation && validation && validation.createdAtValidationError" :message="validation.createdAtValidationError" />
                                    <p class="text-xs text-gray-500">Quand l'événement est-il créé dans le projet</p>
                                </div>

                                <!-- Date d'exécution/envoi prévue -->
                                <div class="space-y-2">
                                    <Label :for="form.event_type === 'step' ? 'execution_date' : 'send_date'" class="text-sm font-medium text-gray-900">
                                        {{ form.event_type === 'step' ? 'Date d\'exécution prévue *' : 'Date d\'envoi prévue *' }}
                                    </Label>
                                    <div class="relative">
                                        <Icon :name="form.event_type === 'step' ? 'play' : 'send'" class="absolute top-1/2 left-3.5 h-4 w-4 -translate-y-1/2 transform text-blue-400" />
                                        <!-- Champ pour étape -->
                                        <Input
                                            v-if="form.event_type === 'step'"
                                            id="execution_date"
                                            v-model="form.execution_date"
                                            @input="form.clearErrors('execution_date')"
                                            type="date"
                                            required
                                            :min="getMinDateForEvent()"
                                            class="h-12 border-gray-200 pl-11 pr-4 transition-all focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 hover:border-gray-300 rounded-lg"
                                            :class="{ 'border-red-300 bg-red-50 focus:border-red-500 focus:ring-red-500/10': (form && form.errors && form.errors.execution_date) || (hasValidation && validation && validation.executionDateValidationError) }"
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
                                            class="h-12 border-gray-200 pl-11 pr-4 transition-all focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 hover:border-gray-300 rounded-lg"
                                            :class="{ 'border-red-300 bg-red-50 focus:border-red-500 focus:ring-red-500/10': (form && form.errors && form.errors.send_date) || (hasValidation && validation && validation.sendDateValidationError) }"
                                        />
                                    </div>
                                    <InputError v-if="form.event_type === 'step'" :message="form.errors.execution_date" />
                                    <InputError v-else :message="form.errors.send_date" />
                                    <InputError v-if="hasValidation && validation && validation.executionDateValidationError && form.event_type === 'step'" :message="validation.executionDateValidationError" />
                                    <InputError v-if="hasValidation && validation && validation.sendDateValidationError && form.event_type === 'billing'" :message="validation.sendDateValidationError" />
                                    <p class="text-xs text-gray-500">
                                        {{ form.event_type === 'step' ? 'Quand prévoir la réalisation' : 'Quand envoyer le document au client' }}
                                    </p>
                                </div>
                            </div>

                            <!-- Statut et date de réalisation -->
                            <div class="grid gap-6 sm:grid-cols-2">
                                <!-- Statut -->
                                <div class="space-y-2">
                                    <Label for="status" class="text-sm font-medium text-gray-900">Statut *</Label>
                                    <div class="relative">
                                        <Icon
                                            :name="getStatusIcon(form.status)"
                                            :class="getStatusIconClasses(form.status)"
                                            class="absolute top-1/2 left-3.5 h-4 w-4 -translate-y-1/2 transform"
                                        />
                                        <select
                                            v-if="form"
                                            id="status"
                                            v-model="form.status"
                                            @change="form.clearErrors('status')"
                                            required
                                            class="h-12 w-full appearance-none rounded-lg border border-gray-200 bg-white py-2 pr-10 pl-11 text-sm transition-all focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 hover:border-gray-300"
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
                                    <p class="text-xs text-gray-500">
                                        {{ form.event_type === 'step' ? 'État d\'avancement actuel' : 'État du document' }}
                                    </p>
                                </div>

                                <!-- Date de réalisation effective (si nécessaire) -->
                                <Transition
                                    enter-active-class="transition-all duration-300 ease-out"
                                    enter-from-class="opacity-0 transform -translate-y-2"
                                    enter-to-class="opacity-100 transform translate-y-0"
                                    leave-active-class="transition-all duration-200 ease-in"
                                    leave-from-class="opacity-100 transform translate-y-0"
                                    leave-to-class="opacity-0 transform -translate-y-2"
                                >
                                    <div v-if="showCompletedAtField" class="space-y-2">
                                        <Label for="completed_at" class="text-sm font-medium text-gray-900">
                                            {{ form.event_type === 'step' ? 'Date d\'exécution réelle *' : 'Date d\'envoi réelle *' }}
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
                                                class="h-12 border-gray-200 pl-11 pr-4 transition-all focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 hover:border-gray-300 rounded-lg"
                                                :class="{
                                                    'border-red-300 bg-red-50 focus:border-red-500 focus:ring-red-500/10': (form && form.errors && form.errors.completed_at) || completedAtError
                                                }"
                                            />
                                        </div>
                                        <InputError :message="form.errors.completed_at" />
                                        <div v-if="completedAtError" class="text-sm text-red-600">
                                            {{ completedAtError }}
                                        </div>
                                        <p class="text-xs text-gray-500">Quand l'événement a été effectivement réalisé</p>
                                    </div>
                                </Transition>
                            </div>
                        </div>
                    </div>

                    <!-- Section Facturation -->
                    <div v-if="showBillingFields" class="space-y-8">
                        <div class="space-y-1">
                            <h3 class="text-xl font-semibold text-gray-900 tracking-tight">Informations financières</h3>
                            <p class="text-sm text-gray-600">Gérez les aspects financiers de ce document</p>
                        </div>

                        <div class="space-y-6">
                            <!-- Ligne 1: Montant et Échéance -->
                            <div class="grid gap-6 sm:grid-cols-2">
                                <div class="space-y-2">
                                    <Label for="amount" class="text-sm font-medium text-gray-900">Montant (€)</Label>
                                    <div class="relative">
                                        <Icon name="banknote" class="absolute top-1/2 left-3.5 h-4 w-4 -translate-y-1/2 transform text-green-400" />
                                        <Input
                                            id="amount"
                                            v-model="form.amount"
                                            @input="form.clearErrors('amount')"
                                            type="number"
                                            step="0.01"
                                            min="0"
                                            placeholder="0.00"
                                            class="h-12 border-gray-200 pl-11 pr-4 transition-all focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 hover:border-gray-300 rounded-lg"
                                            :class="{ 'border-red-300 bg-red-50 focus:border-red-500 focus:ring-red-500/10': form.errors.amount }"
                                        />
                                    </div>
                                    <InputError :message="form.errors.amount" />
                                    <p class="text-xs text-gray-500">Montant HT du document</p>
                                </div>

                                <!-- Échéance de paiement -->
                                <div class="space-y-2">
                                    <Label for="payment_due_date" class="text-sm font-medium text-gray-900">Échéance de paiement *</Label>
                                    <div class="relative">
                                        <Icon name="calendar" class="absolute top-1/2 left-3.5 h-4 w-4 -translate-y-1/2 transform text-red-400" />
                                        <Input
                                            id="payment_due_date"
                                            v-model="form.payment_due_date"
                                            @input="form?.clearErrors('payment_due_date')"
                                            type="date"
                                            required
                                            :min="form.send_date || undefined"
                                            class="h-12 border-gray-200 pl-11 pr-4 transition-all focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 hover:border-gray-300 rounded-lg"
                                            :class="{ 'border-red-300 bg-red-50 focus:border-red-500 focus:ring-red-500/10': (form && form.errors && form.errors.payment_due_date) || (hasValidation && validation && validation.paymentDueDateValidationError) }"
                                        />
                                    </div>
                                    <InputError :message="form.errors.payment_due_date" />
                                    <InputError v-if="hasValidation && validation && validation.paymentDueDateValidationError" :message="validation.paymentDueDateValidationError" />
                                    <p class="text-xs text-gray-500">Date limite de paiement client</p>
                                </div>
                            </div>

                            <!-- Ligne 2: État du paiement et Date de paiement -->
                            <div class="grid gap-6 sm:grid-cols-2">
                                <div class="space-y-2">
                                    <Label for="payment_status" class="text-sm font-medium text-gray-900">État du paiement</Label>
                                    <div class="relative">
                                        <Icon
                                            :name="getPaymentStatusIcon(form.payment_status)"
                                            :class="getPaymentStatusIconClasses(form.payment_status)"
                                            class="absolute top-1/2 left-3.5 h-4 w-4 -translate-y-1/2 transform"
                                        />
                                        <select
                                            v-if="form"
                                            id="payment_status"
                                            v-model="form.payment_status"
                                            @change="form.clearErrors('payment_status')"
                                            class="h-12 w-full appearance-none rounded-lg border border-gray-200 bg-white py-2 pr-10 pl-11 text-sm transition-all focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 hover:border-gray-300"
                                            :class="{ 'border-red-300 bg-red-50 focus:border-red-500 focus:ring-red-500/10': form.errors.payment_status }"
                                        >
                                            <option value="pending">À payer</option>
                                            <option value="paid">Payé</option>
                                        </select>
                                        <Icon name="chevron-down" class="absolute top-1/2 right-3.5 h-4 w-4 -translate-y-1/2 transform text-gray-400 pointer-events-none" />
                                    </div>
                                    <InputError :message="form.errors.payment_status" />
                                    <p class="text-xs text-gray-500">Statut actuel du paiement</p>
                                </div>

                                <!-- Date de paiement effective (si payé) -->
                                <Transition
                                    enter-active-class="transition-all duration-300 ease-out"
                                    enter-from-class="opacity-0 transform -translate-y-2"
                                    enter-to-class="opacity-100 transform translate-y-0"
                                    leave-active-class="transition-all duration-200 ease-in"
                                    leave-from-class="opacity-100 transform translate-y-0"
                                    leave-to-class="opacity-0 transform -translate-y-2"
                                >
                                    <div v-if="showPaidAtField" class="space-y-2">
                                        <Label for="paid_at" class="text-sm font-medium text-gray-900">Date de paiement effective *</Label>
                                        <div class="relative">
                                            <Icon name="check-circle" class="absolute top-1/2 left-3.5 h-4 w-4 -translate-y-1/2 transform text-emerald-400" />
                                            <Input
                                                id="paid_at"
                                                v-model="form.paid_at"
                                                @input="form?.clearErrors('paid_at')"
                                                type="date"
                                                :required="form.payment_status === 'paid'"
                                                :min="getMinDateForEvent()"
                                                class="h-12 border-gray-200 pl-11 pr-4 transition-all focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 hover:border-gray-300 rounded-lg"
                                                :class="{
                                                    'border-red-300 bg-red-50 focus:border-red-500 focus:ring-red-500/10': (form && form.errors && form.errors.paid_at) ||
                                                                                      (hasValidation && validation && !validation.isPaidAtValid)
                                                }"
                                            />
                                        </div>
                                        <InputError v-if="form && form.errors" :message="form.errors.paid_at" />
                                        <div v-if="hasValidation && validation && validation.paidAtValidationError"
                                             class="text-sm text-red-600">
                                            {{ validation.paidAtValidationError }}
                                        </div>
                                        <p class="text-xs text-gray-500">Quand le paiement a été effectivement reçu</p>
                                    </div>
                                </Transition>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex flex-col gap-4 border-t border-gray-200 bg-gray-50/50 rounded-b-xl pt-8 pb-2 -mx-6 px-6 sm:flex-row sm:items-center sm:justify-between">
                        <div class="order-2 sm:order-1">
                            <Button
                                variant="outline"
                                type="button"
                                @click="handleCancel"
                                class="h-12 px-6 text-sm font-medium transition-all hover:bg-gray-100 border-gray-300"
                            >
                                <Icon name="x" class="mr-2 h-4 w-4" />
                                Annuler
                            </Button>
                        </div>
                        <div class="order-1 sm:order-2">
                            <Button
                                type="submit"
                                :disabled="!isFormValid || form.processing"
                                class="h-12 px-8 w-full sm:w-auto bg-emerald-600 text-white font-medium transition-all hover:bg-emerald-700 hover:shadow-lg hover:shadow-emerald-600/25 focus:ring-4 focus:ring-emerald-500/20 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:shadow-none"
                            >
                                <Icon v-if="form.processing" name="loader-2" class="mr-2 h-4 w-4 animate-spin" />
                                <Icon v-else name="check" class="mr-2 h-4 w-4" />
                                {{ form.processing ? 'Création en cours...' : 'Créer l\'événement' }}
                            </Button>
                        </div>
                    </div>
                </form>
                </div>
            </CardContent>
        </Card>

        <!-- Sidebar avec guides -->
        <div v-if="!hasError" class="space-y-6 form:col-span-4">
            <!-- Guide rapide -->
            <Card class="border-0 bg-gradient-to-br from-emerald-50 via-emerald-50 to-teal-50 shadow-sm overflow-hidden">
                <CardContent class="p-6">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="flex h-8 w-8 items-center justify-center rounded-full bg-emerald-100">
                            <Icon name="lightbulb" class="h-4 w-4 text-emerald-600" />
                        </div>
                        <h3 class="text-base font-semibold text-emerald-900">Guide rapide</h3>
                    </div>
                    <div class="space-y-3 text-sm text-emerald-800">
                        <div class="flex items-start gap-3">
                            <div class="flex h-5 w-5 items-center justify-center rounded-full bg-emerald-200 mt-0.5">
                                <div class="h-2 w-2 rounded-full bg-emerald-600"></div>
                            </div>
                            <p>Sélectionnez le type pour adapter l'interface automatiquement</p>
                        </div>
                        <div class="flex items-start gap-3">
                            <div class="flex h-5 w-5 items-center justify-center rounded-full bg-emerald-200 mt-0.5">
                                <div class="h-2 w-2 rounded-full bg-emerald-600"></div>
                            </div>
                            <p>Les dates sont validées selon la chronologie du projet</p>
                        </div>
                        <div class="flex items-start gap-3">
                            <div class="flex h-5 w-5 items-center justify-center rounded-full bg-emerald-200 mt-0.5">
                                <div class="h-2 w-2 rounded-full bg-emerald-600"></div>
                            </div>
                            <p>Description détaillée recommandée pour le suivi</p>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Types d'événements -->
            <Card class="border border-gray-100 shadow-sm">
                <CardContent class="p-6">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="flex h-8 w-8 items-center justify-center rounded-full bg-gray-100">
                            <Icon name="info" class="h-4 w-4 text-gray-600" />
                        </div>
                        <h3 class="text-base font-semibold text-gray-900">Types disponibles</h3>
                    </div>
                    <div class="space-y-3">
                        <div class="rounded-xl border border-blue-200 bg-blue-50/50 p-4">
                            <div class="mb-2 flex items-center gap-3">
                                <div class="flex h-6 w-6 items-center justify-center rounded-full bg-blue-100">
                                    <Icon name="flag" class="h-3 w-3 text-blue-600" />
                                </div>
                                <span class="font-medium text-blue-900">Étape</span>
                            </div>
                            <p class="text-sm text-blue-800">Tâches, réunions, livrables et jalons du projet</p>
                        </div>
                        <div class="rounded-xl border border-purple-200 bg-purple-50/50 p-4">
                            <div class="mb-2 flex items-center gap-3">
                                <div class="flex h-6 w-6 items-center justify-center rounded-full bg-purple-100">
                                    <Icon name="banknote" class="h-3 w-3 text-purple-600" />
                                </div>
                                <span class="font-medium text-purple-900">Facturation</span>
                            </div>
                            <p class="text-sm text-purple-800">Documents financiers, acomptes, factures et paiements</p>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Workflow -->
            <Card class="border border-gray-100 shadow-sm">
                <CardContent class="p-6">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="flex h-8 w-8 items-center justify-center rounded-full bg-gray-100">
                            <Icon name="list-checks" class="h-4 w-4 text-gray-600" />
                        </div>
                        <h3 class="text-base font-semibold text-gray-900">Workflow</h3>
                    </div>
                    <div class="space-y-3">
                        <div class="flex items-center gap-3 rounded-lg bg-emerald-50 border border-emerald-200 p-3">
                            <div class="flex h-6 w-6 items-center justify-center rounded-full bg-emerald-500 text-xs font-bold text-white">
                                1
                            </div>
                            <span class="text-sm font-medium text-emerald-900">Créer l'événement</span>
                        </div>
                        <div class="flex items-center gap-3 rounded-lg bg-gray-50 border border-gray-200 p-3">
                            <div class="flex h-6 w-6 items-center justify-center rounded-full bg-gray-300 text-xs font-bold text-gray-600">
                                2
                            </div>
                            <span class="text-sm text-gray-600">Suivre l'avancement</span>
                        </div>
                        <div class="flex items-center gap-3 rounded-lg bg-gray-50 border border-gray-200 p-3">
                            <div class="flex h-6 w-6 items-center justify-center rounded-full bg-gray-300 text-xs font-bold text-gray-600">
                                3
                            </div>
                            <span class="text-sm text-gray-600">Finaliser et archiver</span>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>

        </div>
    </div>
</template>

<script setup lang="ts">
import { computed, ref, watch } from 'vue'
import Icon from '@/components/Icon.vue'
import InputError from '@/components/InputError.vue'
import EventTypeCards from '@/components/ui/EventTypeCards.vue'
import ProjectSelector from '@/components/ui/ProjectSelector.vue'
import { Button } from '@/components/ui/button'
import { Card, CardContent } from '@/components/ui/card'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Link } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import { useEventCreateForm } from '@/composables/events/create/useEventCreateForm'
import type { EventCreateFormValidation, EventCreateProject } from '@/types/events/create'

interface Props {
    projectId?: number | null
    isLoading: boolean
    hasError: boolean
    projects: Array<EventCreateProject>
    selectedProject?: EventCreateProject | null
    eventData?: any
}

const props = defineProps<Props>()

// Variables réactives
const form = ref<any>(null)
const validation = ref<EventCreateFormValidation | null>(null)
const isFormValid = ref(false)
const showBillingFields = ref(false)
const showProjectSelector = ref(false)
const showPaidAtField = ref(false)
const showCompletedAtField = ref(false)
const currentProject = ref<EventCreateProject | null>(null)
const handleSubmit = ref<(() => void) | null>(null)
const markProjectIdAsTouched = ref<(() => void) | null>(null)
const markNameAsTouched = ref<(() => void) | null>(null)
const markTypeAsTouched = ref<(() => void) | null>(null)


// Créer le composable une seule fois quand les données sont disponibles
let composableInstance: ReturnType<typeof useEventCreateForm> | null = null

const initializeComposable = (): void => {
    if (props.projects && !props.isLoading && !props.hasError && !composableInstance) {
        composableInstance = useEventCreateForm(
            props.projects,
            props.selectedProject || null,
            props.projectId
        )

        // Assigner les références une seule fois
        form.value = composableInstance.form
        validation.value = composableInstance.validation
        handleSubmit.value = composableInstance.handleSubmit
        markProjectIdAsTouched.value = composableInstance.markProjectIdAsTouched
        markNameAsTouched.value = composableInstance.markNameAsTouched
        markTypeAsTouched.value = composableInstance.markTypeAsTouched

        // Watcher pour les computed du composable
        watch(composableInstance.isFormValid, (val: boolean) => {
            isFormValid.value = val
        }, { immediate: true })

        watch(composableInstance.showBillingFields, (val: boolean) => {
            showBillingFields.value = val
        }, { immediate: true })

        watch(composableInstance.showProjectSelector, (val: boolean) => {
            showProjectSelector.value = val
        }, { immediate: true })

        watch(composableInstance.showPaidAtField, (val: boolean) => {
            showPaidAtField.value = val
        }, { immediate: true })

        watch(composableInstance.showCompletedAtField, (val: boolean) => {
            showCompletedAtField.value = val
        }, { immediate: true })

        watch(composableInstance.currentProject, (val: EventCreateProject | null) => {
            currentProject.value = val
        }, { immediate: true })
    }
}

// Watcher pour initialiser quand les données arrivent
watch(
    () => [props.projects, props.isLoading, props.hasError] as const,
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

// Handler de soumission qui utilise le handleSubmit du composable
const onSubmit = () => {
    if (handleSubmit.value && isFormValid.value) {
        handleSubmit.value()
    }
}

// Handler pour le changement de projet
const handleProjectChange = (project: any) => {
    if (form.value) {
        form.value.clearErrors('project_id')
        if (markProjectIdAsTouched.value) {
            markProjectIdAsTouched.value()
        }
    }
}

// Handler pour annuler et revenir à la page précédente
const handleCancel = () => {
    window.history.back()
}

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

const getPaymentStatusIcon = (paymentStatus: string): string => {
    switch (paymentStatus) {
        case 'paid':
            return 'check-circle'
        case 'pending':
        default:
            return 'clock'
    }
}

const getPaymentStatusIconClasses = (paymentStatus: string): string => {
    switch (paymentStatus) {
        case 'paid':
            return 'text-emerald-400'
        case 'pending':
        default:
            return 'text-orange-400'
    }
}
</script>
