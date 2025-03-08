<template>
    <v-menu location="bottom end">
        <template v-slot:activator="{ props }">
            <v-btn variant="text" v-bind="props" prepend-icon="mdi-account-circle">
                <span class="d-none d-sm-block">{{ user.name }}</span>
            </v-btn>
        </template>
        <v-list>
            <v-list-item :href="route('profile.edit')" link>
                <v-list-item-title>Profile</v-list-item-title>
            </v-list-item>
            <v-list-item @click="logout" link>
                <v-list-item-title>Logout</v-list-item-title>
            </v-list-item>
        </v-list>
    </v-menu>
</template>

<script setup>
    import { router } from '@inertiajs/vue3'

    const props = defineProps({
        user: {
            type: Object,
            required: true
        }
    })

    const logout = () => {
        router.post(route('logout'));
    }
</script>
