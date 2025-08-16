<template>

    <Head :title="`Activity Log #${activity.id}`" />
    <DashboardLayout>
        <template #header>
            <div class="d-flex align-center">
                <Link :href="route('dashboard.activity-logs.index')" class="text-decoration-none mr-2">
                <v-btn icon variant="text">
                    <v-icon icon="mdi-arrow-left" />
                </v-btn>
                </Link>
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    {{ $t('Activity Log Details') }}
                </h2>
            </div>
        </template>
        <v-container fluid class="py-8">
            <v-card>
                <v-card-title>
                    <v-chip :color="descriptionMeta[activity.description]?.color || 'default'" variant="tonal"
                        class="font-weight-medium" size="small">
                        <v-icon start
                            :icon="descriptionMeta[activity.description]?.icon || 'mdi-information-outline'" />
                        {{ activity.description }}
                    </v-chip>
                </v-card-title>
                <v-card-text>
                    <v-list>
                        <v-list-item>
                            <v-list-item-title>{{ $t('ID') }}</v-list-item-title>
                            <v-list-item-subtitle>{{ activity.id }}</v-list-item-subtitle>
                        </v-list-item>
                        <v-list-item>
                            <v-list-item-title>{{ $t('Causer') }}</v-list-item-title>
                            <v-list-item-subtitle>{{ activity.causer ? activity.causer.name : 'System'
                            }}</v-list-item-subtitle>
                        </v-list-item>
                        <v-list-item>
                            <v-list-item-title>{{ $t('Subject') }}</v-list-item-title>
                            <v-list-item-subtitle>{{ activity.subject_type ? activity.subject_type.split('\\').pop() :
                                '-'
                            }}</v-list-item-subtitle>
                        </v-list-item>
                        <v-list-item>
                            <v-list-item-title>{{ $t('Created At') }}</v-list-item-title>
                            <v-list-item-subtitle>{{ new Date(activity.created_at).toLocaleString()
                            }}</v-list-item-subtitle>
                        </v-list-item>
                        <v-list-item v-if="activity.properties">
                            <v-list-item-title>{{ $t('Properties') }}</v-list-item-title>
                            <v-list-item-subtitle>
                                <v-textarea :model-value="JSON.stringify(activity.properties, null, 2)" readonly
                                    rows="8" auto-grow
                                    style="font-family: monospace; font-size: 14px; background: #f8f8f8;"></v-textarea>
                            </v-list-item-subtitle>
                        </v-list-item>
                    </v-list>
                </v-card-text>
            </v-card>
        </v-container>
    </DashboardLayout>
</template>

<script setup>
    import DashboardLayout from '@/Layouts/DashboardLayout.vue';
    import { Head, Link } from '@inertiajs/vue3';

    const props = defineProps({
        activity: Object
    });

    console.log(props.activity); // Debugging line to check activity data

    const descriptionMeta = {
        'created': { icon: 'mdi-plus', color: 'primary' },
        'updated': { icon: 'mdi-pencil', color: 'warning' },
        'deleted': { icon: 'mdi-delete', color: 'error' },
        'logged in': { icon: 'mdi-login', color: 'success' },
        'logged out': { icon: 'mdi-logout', color: 'secondary' },
    };
</script>
