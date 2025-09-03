<template>
    <div class="mx-auto max-w-7xl">
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
                    <form @submit.prevent="onSubmit" class="space-y-6">
                    <!-- General errors -->
                    <div v-if="form.errors.general" class="rounded-md bg-red-50 p-4">
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

                    <!-- Section Informations de base -->
                    <div class="space-y-6 my-12 mt-0">
                        <div class="border-b border-gray-100 pb-4">
                            <h3 class="mb-1 text-lg font-semibold text-gray-900">Informations de base</h3>
                            <p class="text-sm text-gray-600">Les informations essentielles de votre événement</p>
                        </div>

                        <!-- Sélecteur de projet (si pas de projet présélectionné) -->
                        <div v-if="showProjectSelector" class="space-y-3">
                            <Label for="project_id" class="text-sm font-medium text-gray-700">Projet *</Label>
                            <div class="relative">
                                <Icon name="folder" class="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 transform text-gray-400 pointer-events-none" />
                                <select
                                    id="project_id"
                                    v-model="form.project_id"
                                    @change="form.clearErrors('project_id')"
                                    @blur="markProjectIdAsTouched?.()"
                                    required
                                    class="h-11 w-full appearance-none rounded-md border border-gray-200 bg-white py-2 pr-10 pl-10 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500 focus:ring-offset-0"
                                    :class="{ 'border-red-500 focus:border-red-500': form.errors.project_id || (hasValidation && validation && validation.projectIdValidationError) }"
                                >
                                    <option value="">Sélectionnez un projet</option>
                                    <option v-for="project in projects" :key="project.id" :value="project.id">
                                        {{ project.client.name }} - {{ project.name }}
                                    </option>
                                </select>
                                <Icon name="chevron-down" class="absolute top-1/2 right-3 h-4 w-4 -translate-y-1/2 transform text-gray-400 pointer-events-none" />
                            </div>
                            <InputError :message="form.errors.project_id" />
                            <InputError v-if="hasValidation && validation && validation.projectIdValidationError" :message="validation.projectIdValidationError" />
                        </div>

                        <!-- Affichage du projet présélectionné -->
                        <div v-else-if="currentProject" class="space-y-3">
                            <Label class="text-sm font-medium text-gray-700">Projet sélectionné</Label>
                            <div class="flex items-center gap-3 rounded-md border border-gray-200 bg-gray-50 p-3">
                                <Icon name="folder" class="h-4 w-4 text-gray-400" />
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ currentProject.name }}</p>
                                    <p class="text-xs text-gray-500">{{ currentProject.client.name }}</p>
                                </div>
                            </div>
                            <input type="hidden" name="project_id" :value="form.project_id" />
                        </div>

                        <div class="grid gap-6 sm:grid-cols-2">
                            <div class="space-y-3">
                                <Label for="name" class="text-sm font-medium text-gray-700">Nom de l'événement *</Label>
                                <div class="relative">
                                    <Icon name="calendar" class="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 transform text-gray-400" />
                                    <Input
                                        id="name"
                                        v-model="form.name"
                                        @input="form.clearErrors('name')"
                                        @blur="markNameAsTouched?.()"
                                        type="text"
                                        required
                                        placeholder="ex: Réunion de lancement"
                                        class="h-11 border-gray-200 pl-10 focus:border-emerald-500 focus:ring-emerald-500"
                                        :class="{ 'border-red-500 focus:border-red-500': form.errors.name || (hasValidation && validation && validation.nameValidationError) }"
                                    />
                                </div>
                                <InputError :message="form.errors.name" />
                                <InputError v-if="hasValidation && validation && validation.nameValidationError" :message="validation.nameValidationError" />
                            </div>

                            <div class="space-y-3">
                                <Label for="type" class="text-sm font-medium text-gray-700">Catégorie *</Label>
                                <div class="relative">
                                    <Icon name="tag" class="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 transform text-gray-400 pointer-events-none" />
                                    <select
                                        id="type"
                                        v-model="form.type"
                                        @change="form.clearErrors('type')"
                                        @blur="markTypeAsTouched?.()"
                                        required
                                        class="h-11 w-full appearance-none rounded-md border border-gray-200 bg-white py-2 pr-10 pl-10 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500 focus:ring-offset-0"
                                        :class="{ 'border-red-500 focus:border-red-500': form.errors.type || (hasValidation && validation && validation.typeValidationError) }"
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
                                    <Icon name="chevron-down" class="absolute top-1/2 right-3 h-4 w-4 -translate-y-1/2 transform text-gray-400 pointer-events-none" />
                                </div>
                                <InputError :message="form.errors.type" />
                                <InputError v-if="hasValidation && validation && validation.typeValidationError" :message="validation.typeValidationError" />
                            </div>
                        </div>

                        <div class="">
                            <Label for="description" class="text-sm font-medium text-gray-700 mb-3">Description</Label>
                            <div class="relative">
                                <Icon name="file-text" class="absolute top-3 left-3 h-4 w-4 text-gray-400" />
                                <textarea
                                    id="description"
                                    v-model="form.description"
                                    @input="form.clearErrors('description')"
                                    rows="4"
                                    placeholder="Décrivez l'événement en détail..."
                                    class="min-h-[100px] w-full resize-none rounded-md border border-gray-200 bg-white py-3 pr-3 pl-10 text-sm placeholder:text-gray-400 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500 focus:ring-offset-0"
                                    :class="{ 'border-red-500 focus:border-red-500': form.errors.description }"
                                />
                            </div>
                            <InputError :message="form.errors.description" />
                        </div>
                    </div>

                    <!-- Section Planification -->
                    <div class="space-y-6 my-12">
                        <div class="border-b border-gray-100 pb-4">
                            <h3 class="mb-1 text-lg font-semibold text-gray-900">Planification</h3>
                            <p class="text-sm text-gray-600">Définissez le type et les dates importantes de votre événement</p>
                        </div>

                        <!-- Ligne 1: Type d'événement + Date de création -->
                        <div class="grid gap-6 sm:grid-cols-2">
                            <!-- Type d'événement -->
                            <div class="space-y-3">
                                <Label for="event_type" class="text-sm font-medium text-gray-700">Type d'événement</Label>
                                <div class="relative">
                                    <Icon name="layers" class="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 transform text-gray-400" />
                                    <select
                                        id="event_type"
                                        v-model="form.event_type"
                                        @change="form.clearErrors('event_type')"
                                        class="h-11 w-full appearance-none rounded-md border border-gray-200 bg-white py-2 pr-10 pl-10 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500 focus:ring-offset-0"
                                        :class="{ 'border-red-500 focus:border-red-500': form.errors.event_type }"
                                    >
                                        <option value="step">Étape</option>
                                        <option value="billing">Facturation</option>
                                    </select>
                                    <Icon name="chevron-down" class="absolute top-1/2 right-3 h-4 w-4 -translate-y-1/2 transform text-gray-400 pointer-events-none" />
                                </div>
                                <InputError :message="form.errors.event_type" />
                                <p class="text-xs text-gray-500">
                                    {{ form.event_type === 'step' ? 'Événement lié à l\'avancement du projet' : 'Événement lié à la facturation' }}
                                </p>
                            </div>

                            <!-- Date de création -->
                            <div class="space-y-3">
                                <Label for="created_date" class="text-sm font-medium text-gray-700">Date de création *</Label>
                                <div class="relative">
                                    <Icon name="clock" class="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 transform text-gray-400" />
                                    <Input
                                        id="created_date"
                                        v-model="form.created_date"
                                        @input="form.clearErrors('created_date')"
                                        type="date"
                                        required
                                        :min="getProjectStartDateForInput()"
                                        class="text-sm h-11 border-gray-200 pl-10 focus:border-emerald-500 focus:ring-emerald-500"
                                        :class="{ 'border-red-500 focus:border-red-500': (form && form.errors && form.errors.created_date) || (hasValidation && validation && validation.createdAtValidationError) }"
                                    />
                                </div>
                                <InputError :message="form.errors.created_date" />
                                <InputError v-if="hasValidation && validation && validation.createdAtValidationError" :message="validation.createdAtValidationError" />
                                <p class="text-xs text-gray-500">Date à laquelle l'événement est prévu d'être créé</p>
                            </div>
                        </div>

                        <!-- Ligne 2: Date d'envoi/exécution prévue + Échéance (si facturation) -->
                        <div class="grid gap-6 sm:grid-cols-2">
                            <!-- Date d'envoi/exécution prévue -->
                            <div class="space-y-3">
                                <Label :for="form.event_type === 'step' ? 'execution_date' : 'send_date'" class="text-sm font-medium text-gray-700">
                                    {{ form.event_type === 'step' ? 'Date d\'exécution prévue *' : 'Date d\'envoi prévue *' }}
                                </Label>
                                <div class="relative">
                                    <Icon :name="form.event_type === 'step' ? 'clock' : 'send'" class="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 transform text-gray-400" />
                                    <!-- Champ pour étape -->
                                    <Input
                                        v-if="form.event_type === 'step'"
                                        id="execution_date"
                                        v-model="form.execution_date"
                                        @input="form.clearErrors('execution_date')"
                                        type="date"
                                        required
                                        :min="getMinDateForEvent()"
                                        class="text-sm h-11 border-gray-200 pl-10 focus:border-emerald-500 focus:ring-emerald-500"
                                        :class="{ 'border-red-500 focus:border-red-500': (form && form.errors && form.errors.execution_date) || (hasValidation && validation && validation.executionDateValidationError) }"
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
                                        class="text-sm h-11 border-gray-200 pl-10 focus:border-emerald-500 focus:ring-emerald-500"
                                        :class="{ 'border-red-500 focus:border-red-500': (form && form.errors && form.errors.send_date) || (hasValidation && validation && validation.sendDateValidationError) }"
                                    />
                                </div>
                                <InputError v-if="form.event_type === 'step'" :message="form.errors.execution_date" />
                                <InputError v-else :message="form.errors.send_date" />
                                <InputError v-if="hasValidation && validation && validation.executionDateValidationError && form.event_type === 'step'" :message="validation.executionDateValidationError" />
                                <InputError v-if="hasValidation && validation && validation.sendDateValidationError && form.event_type === 'billing'" :message="validation.sendDateValidationError" />
                            </div>

                            <!-- Échéance de paiement (uniquement pour facturation) -->
                            <div v-if="form.event_type === 'billing'" class="space-y-3">
                                <Label for="payment_due_date" class="text-sm font-medium text-gray-700">Échéance de paiement *</Label>
                                <div class="relative">
                                    <Icon name="clock" class="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 transform text-gray-400" />
                                    <Input
                                        id="payment_due_date"
                                        v-model="form.payment_due_date"
                                        @input="form?.clearErrors('payment_due_date')"
                                        type="date"
                                        required
                                        :min="form.send_date || undefined"
                                        class="text-sm h-11 border-gray-200 pl-10 focus:border-emerald-500 focus:ring-emerald-500"
                                        :class="{ 'border-red-500 focus:border-red-500': (form && form.errors && form.errors.payment_due_date) || (hasValidation && validation && validation.paymentDueDateValidationError) }"
                                    />
                                </div>
                                <InputError :message="form.errors.payment_due_date" />
                                <InputError v-if="hasValidation && validation && validation.paymentDueDateValidationError" :message="validation.paymentDueDateValidationError" />
                                <p class="text-xs text-gray-500">Date limite de paiement pour le client</p>
                            </div>
                        </div>

                        <!-- Ligne 3: Statut + Date d'envoi/exécution réelle (si envoyé/fait) -->
                        <div class="grid gap-6 sm:grid-cols-2">
                            <!-- Statut -->
                            <div class="space-y-3">
                                <Label for="status" class="text-sm font-medium text-gray-700">Statut *</Label>
                                <div class="relative">
                                    <Icon name="activity" class="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 transform text-gray-400" />
                                    <select
                                        v-if="form"
                                        id="status"
                                        v-model="form.status"
                                        @change="form.clearErrors('status')"
                                        required
                                        class="h-11 w-full appearance-none rounded-md border border-gray-200 bg-white py-2 pr-10 pl-10 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500 focus:ring-offset-0"
                                        :class="{ 'border-red-500 focus:border-red-500': form.errors.status }"
                                    >
                                        <option v-if="form.event_type === 'step'" value="todo">À faire</option>
                                        <option v-if="form.event_type === 'step'" value="done">Fait</option>
                                        <option v-if="showBillingFields" value="to_send">À envoyer</option>
                                        <option v-if="showBillingFields" value="sent">Envoyé</option>
                                        <option value="cancelled">Annulé</option>
                                    </select>
                                    <Icon name="chevron-down" class="absolute top-1/2 right-3 h-4 w-4 -translate-y-1/2 transform text-gray-400 pointer-events-none" />
                                </div>
                                <InputError :message="form.errors.status" />
                            </div>

                            <!-- Date d'envoi/exécution réelle (visible si statut = "fait" ou "envoyé") -->
                            <Transition
                                enter-active-class="transition-all duration-200 ease-out"
                                enter-from-class="opacity-0 transform -translate-y-2"
                                enter-to-class="opacity-100 transform translate-y-0"
                                leave-active-class="transition-all duration-200 ease-in"
                                leave-from-class="opacity-100 transform translate-y-0"
                                leave-to-class="opacity-0 transform -translate-y-2"
                            >
                                <div v-if="showCompletedAtField" class="space-y-3">
                                    <Label for="completed_at" class="text-sm font-medium text-gray-700">
                                        {{ form.event_type === 'step' ? 'Date d\'exécution *' : 'Date d\'envoi *' }}
                                    </Label>
                                    <div class="relative">
                                        <Icon name="check-circle" class="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 transform text-emerald-500" />
                                        <Input
                                            id="completed_at"
                                            v-model="form.completed_at"
                                            @input="form.clearErrors('completed_at')"
                                            type="date"
                                            required
                                            :min="getMinDateForEvent()"
                                            class="text-sm h-11 border-gray-200 pl-10 focus:border-emerald-500 focus:ring-emerald-500"
                                            :class="{
                                                'border-red-500 focus:border-red-500': (form && form.errors && form.errors.completed_at) || completedAtError
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

                    <!-- Section Facturation -->
                    <div v-if="showBillingFields" class="space-y-6 my-12">
                        <div class="border-b border-gray-100 pb-4">
                            <h3 class="mb-1 text-lg font-semibold text-gray-900">Informations de facturation</h3>
                            <p class="text-sm text-gray-600">Gérez les aspects financiers de cet événement</p>
                        </div>

                        <div class="grid gap-6 sm:grid-cols-2">
                            <div class="space-y-3">
                                <Label for="amount" class="text-sm font-medium text-gray-700">Montant (€)</Label>
                                <div class="relative">
                                    <Icon name="banknote" class="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 transform text-gray-400" />
                                    <Input
                                        id="amount"
                                        v-model="form.amount"
                                        @input="form.clearErrors('amount')"
                                        type="number"
                                        step="0.01"
                                        min="0"
                                        placeholder="0.00"
                                        class="h-11 border-gray-200 pl-10 focus:border-emerald-500 focus:ring-emerald-500"
                                        :class="{ 'border-red-500 focus:border-red-500': form.errors.amount }"
                                    />
                                </div>
                                <InputError :message="form.errors.amount" />
                            </div>

                            <div class="space-y-3">
                                <Label for="payment_status" class="text-sm font-medium text-gray-700">État du paiement</Label>
                                <div class="relative">
                                    <Icon name="bookmark" class="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 transform text-gray-400" />
                                    <select
                                        v-if="form"
                                        id="payment_status"
                                        v-model="form.payment_status"
                                        @change="form.clearErrors('payment_status')"
                                        class="h-11 w-full appearance-none rounded-md border border-gray-200 bg-white py-2 pr-10 pl-10 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500 focus:ring-offset-0"
                                        :class="{ 'border-red-500 focus:border-red-500': form.errors.payment_status }"
                                    >
                                        <option value="pending">À payer</option>
                                        <option value="paid">Payé</option>
                                    </select>
                                    <Icon name="chevron-down" class="absolute top-1/2 right-3 h-4 w-4 -translate-y-1/2 transform text-gray-400 pointer-events-none" />
                                </div>
                                <InputError :message="form.errors.payment_status" />
                            </div>
                        </div>

                        <!-- Date de paiement si payé -->
                        <Transition
                            enter-active-class="transition-all duration-300 ease-out"
                            enter-from-class="opacity-0 transform -translate-y-2"
                            enter-to-class="opacity-100 transform translate-y-0"
                            leave-active-class="transition-all duration-200 ease-in"
                            leave-from-class="opacity-100 transform translate-y-0"
                            leave-to-class="opacity-0 transform -translate-y-2"
                        >
                            <div v-if="showPaidAtField" class="space-y-3">
                            <Label for="paid_at" class="text-sm font-medium text-gray-700">Date de paiement *</Label>
                            <div class="relative">
                                <Icon name="check-circle" class="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 transform text-gray-400" />
                                <Input
                                    id="paid_at"
                                    v-model="form.paid_at"
                                    @input="form?.clearErrors('paid_at')"
                                    type="date"
                                    :required="form.payment_status === 'paid'"
                                    :min="getMinDateForEvent()"
                                    class="text-sm h-11 border-gray-200 pl-10 focus:border-emerald-500 focus:ring-emerald-500"
                                    :class="{
                                        'border-red-500 focus:border-red-500': (form && form.errors && form.errors.paid_at) ||
                                                                              (hasValidation && validation && !validation.isPaidAtValid)
                                    }"
                                />
                            </div>
                            <InputError v-if="form && form.errors" :message="form.errors.paid_at" />
                            <div v-if="hasValidation && validation && validation.paidAtValidationError"
                                 class="text-sm text-red-600">
                                {{ validation.paidAtValidationError }}
                            </div>
                            </div>
                        </Transition>
                        </div>

                    <!-- Actions -->
                    <div class="flex flex-col items-center justify-between gap-4 border-t border-gray-100 pt-6 sm:flex-row">
                        <div class="flex w-full flex-col gap-2 sm:w-auto sm:flex-row">
                            <Button variant="outline" as-child class="w-full sm:w-auto">
                                <Link :href="route('events.index')">
                                    <Icon name="x" class="mr-2 h-4 w-4" />
                                    Annuler
                                </Link>
                            </Button>
                        </div>
                        <Button
                            type="submit"
                            :disabled="!isFormValid"
                            class="w-full bg-emerald-600 text-white hover:bg-emerald-700 sm:w-auto disabled:opacity-50"
                        >
                            <Icon v-if="form.processing" name="loader-2" class="mr-2 h-4 w-4 animate-spin" />
                            <Icon v-else name="plus" class="mr-2 h-4 w-4" />
                            Créer l'événement
                        </Button>
                    </div>
                </form>
                </div>
            </CardContent>
        </Card>

        <!-- Sidebar avec conseils -->
        <div v-if="!hasError" class="space-y-6 form:col-span-4">
            <!-- Conseils -->
            <Card class="border-0 bg-gradient-to-br from-emerald-50 to-emerald-100 shadow-sm p-6">
                <CardHeader class="pb-4">
                    <CardTitle class="flex items-center gap-2 text-emerald-900">
                        <Icon name="lightbulb" class="h-5 w-5" />
                        Conseils
                    </CardTitle>
                </CardHeader>
                <CardContent class="space-y-4 text-sm text-emerald-800 p-0">
                    <div class="flex items-start gap-3">
                        <Icon name="check-circle" class="mt-0.5 h-4 w-4 text-emerald-600" />
                        <p>Choisissez d'abord le type d'événement pour adapter l'interface.</p>
                    </div>
                    <div class="flex items-start gap-3">
                        <Icon name="check-circle" class="mt-0.5 h-4 w-4 text-emerald-600" />
                        <p>Les dates doivent respecter la chronologie du projet.</p>
                    </div>
                    <div class="flex items-start gap-3">
                        <Icon name="check-circle" class="mt-0.5 h-4 w-4 text-emerald-600" />
                        <p>Une description claire facilite le suivi ultérieur.</p>
                    </div>
                </CardContent>
            </Card>

            <!-- Types d'événements -->
            <Card class="border-0 bg-white shadow-sm p-6">
                <CardHeader class="pb-4">
                    <CardTitle class="flex items-center gap-2 text-gray-900">
                        <Icon name="info" class="h-5 w-5" />
                        Types d'événements
                    </CardTitle>
                </CardHeader>
                <CardContent class="space-y-3 text-sm p-0">
                    <div class="rounded-lg border border-blue-200 bg-blue-50 p-3">
                        <div class="mb-1 flex items-center gap-2">
                            <Icon name="play-circle" class="h-4 w-4 text-blue-600" />
                            <span class="font-medium text-blue-900">Étape</span>
                        </div>
                        <p class="text-blue-800">Tâches, réunions, livrables du projet</p>
                    </div>
                    <div class="rounded-lg border border-orange-200 bg-orange-50 p-3">
                        <div class="mb-1 flex items-center gap-2">
                            <Icon name="banknote" class="h-4 w-4 text-orange-600" />
                            <span class="font-medium text-orange-900">Facturation</span>
                        </div>
                        <p class="text-orange-800">Devis, factures et paiements</p>
                    </div>
                </CardContent>
            </Card>

            <!-- Prochaines étapes -->
            <Card class="border-0 bg-white shadow-sm p-6">
                <CardHeader class="pb-4">
                    <CardTitle class="flex items-center gap-2 text-gray-900">
                        <Icon name="list-checks" class="h-5 w-5" />
                        Prochaines étapes
                    </CardTitle>
                </CardHeader>
                <CardContent class="space-y-3 text-sm p-0">
                    <div class="flex items-center gap-3 rounded-lg bg-gray-50 p-3">
                        <div class="flex h-6 w-6 items-center justify-center rounded-full bg-emerald-100 text-xs font-medium text-emerald-600">
                            1
                        </div>
                        <span>Créer l'événement</span>
                    </div>
                    <div class="flex items-center gap-3 rounded-lg bg-gray-50 p-3 opacity-60">
                        <div class="flex h-6 w-6 items-center justify-center rounded-full bg-gray-200 text-xs font-medium text-gray-500">
                            2
                        </div>
                        <span>Suivre l'avancement</span>
                    </div>
                    <div class="flex items-center gap-3 rounded-lg bg-gray-50 p-3 opacity-60">
                        <div class="flex h-6 w-6 items-center justify-center rounded-full bg-gray-200 text-xs font-medium text-gray-500">
                            3
                        </div>
                        <span>Gérer les statuts</span>
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
</script>
